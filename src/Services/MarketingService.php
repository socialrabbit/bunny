<?php

namespace Bunny\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Bunny\Models\Discount;
use Bunny\Models\Customer;
use Bunny\Models\Order;
use Bunny\Events\DiscountApplied;
use Bunny\Events\FlashSaleStarted;
use Bunny\Events\FlashSaleEnded;
use Carbon\Carbon;

class MarketingService
{
    protected $cacheKey = 'bunny_marketing_';
    protected $ttl = 3600; // 1 hour

    public function createDiscount(array $data)
    {
        $discount = Discount::create($data);
        Cache::forget($this->cacheKey . 'active_discounts');
        return $discount;
    }

    public function applyDiscount(Order $order, $code)
    {
        $discount = $this->getActiveDiscount($code);
        
        if (!$discount) {
            throw new \Exception('Invalid or expired discount code');
        }

        if (!$this->isDiscountValid($discount, $order)) {
            throw new \Exception('Discount code is not valid for this order');
        }

        $amount = $this->calculateDiscountAmount($order, $discount);
        $order->discount_amount = $amount;
        $order->discount_code = $code;
        $order->save();

        event(new DiscountApplied($order, $discount));
        return $order;
    }

    public function createFlashSale(array $data)
    {
        $sale = Discount::create(array_merge($data, [
            'type' => 'flash_sale',
            'starts_at' => Carbon::parse($data['starts_at']),
            'ends_at' => Carbon::parse($data['ends_at']),
        ]));

        Cache::forget($this->cacheKey . 'active_flash_sales');
        event(new FlashSaleStarted($sale));

        return $sale;
    }

    public function endFlashSale($saleId)
    {
        $sale = Discount::findOrFail($saleId);
        $sale->update(['ends_at' => now()]);
        
        Cache::forget($this->cacheKey . 'active_flash_sales');
        event(new FlashSaleEnded($sale));

        return $sale;
    }

    public function recoverAbandonedCarts()
    {
        $abandonedCarts = Order::where('status', 'pending')
            ->where('created_at', '<=', now()->subHours(24))
            ->get();

        foreach ($abandonedCarts as $cart) {
            $this->sendAbandonedCartEmail($cart);
        }

        return $abandonedCarts->count();
    }

    public function sendMarketingEmail(array $data)
    {
        $customers = $this->getTargetCustomers($data['segment']);
        
        foreach ($customers as $customer) {
            Mail::send(
                $data['template'],
                ['customer' => $customer, 'data' => $data['data']],
                function ($message) use ($customer, $data) {
                    $message->to($customer->email)
                        ->subject($data['subject']);
                }
            );
        }

        return $customers->count();
    }

    public function segmentCustomers(array $criteria)
    {
        $query = Customer::query();

        if (isset($criteria['purchase_frequency'])) {
            $query->whereHas('orders', function ($q) use ($criteria) {
                $q->where('created_at', '>=', now()->subDays($criteria['purchase_frequency']));
            }, '>=', $criteria['min_orders']);
        }

        if (isset($criteria['total_spent'])) {
            $query->whereHas('orders', function ($q) use ($criteria) {
                $q->where('status', 'completed')
                    ->havingRaw('SUM(total) >= ?', [$criteria['total_spent']]);
            });
        }

        if (isset($criteria['last_purchase'])) {
            $query->whereHas('orders', function ($q) use ($criteria) {
                $q->where('created_at', '>=', now()->subDays($criteria['last_purchase']));
            });
        }

        return $query->get();
    }

    protected function getActiveDiscount($code)
    {
        return Cache::remember($this->cacheKey . 'discount_' . $code, $this->ttl, function () use ($code) {
            return Discount::where('code', $code)
                ->where('starts_at', '<=', now())
                ->where('ends_at', '>=', now())
                ->where('is_active', true)
                ->first();
        });
    }

    protected function isDiscountValid($discount, $order)
    {
        if ($discount->min_order_amount && $order->subtotal < $discount->min_order_amount) {
            return false;
        }

        if ($discount->max_uses && $discount->used_count >= $discount->max_uses) {
            return false;
        }

        if ($discount->user_id && $discount->user_id !== $order->customer_id) {
            return false;
        }

        return true;
    }

    protected function calculateDiscountAmount($order, $discount)
    {
        if ($discount->type === 'percentage') {
            return $order->subtotal * ($discount->value / 100);
        }

        return $discount->value;
    }

    protected function sendAbandonedCartEmail($cart)
    {
        Mail::send(
            'bunny-ecommerce::emails.abandoned-cart',
            ['cart' => $cart],
            function ($message) use ($cart) {
                $message->to($cart->customer->email)
                    ->subject('Your Cart is Waiting!');
            }
        );
    }

    protected function getTargetCustomers($segment)
    {
        return Cache::remember($this->cacheKey . 'segment_' . $segment, $this->ttl, function () use ($segment) {
            return match ($segment) {
                'all' => Customer::all(),
                'active' => Customer::whereHas('orders', function ($q) {
                    $q->where('created_at', '>=', now()->subDays(30));
                })->get(),
                'inactive' => Customer::whereDoesntHave('orders', function ($q) {
                    $q->where('created_at', '>=', now()->subDays(90));
                })->get(),
                'high_value' => Customer::whereHas('orders', function ($q) {
                    $q->where('status', 'completed')
                        ->havingRaw('SUM(total) >= ?', [1000]);
                })->get(),
                default => collect(),
            };
        });
    }
} 