<?php

namespace Bunny\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Bunny\Models\{
    Product,
    Category,
    Order,
    Customer,
    Cart,
    Wishlist,
    Review,
    Coupon,
    FlashSale,
    Subscription,
    Inventory,
    ShippingZone,
    TaxRule,
    PaymentMethod
};
use Bunny\Events\{
    OrderPlaced,
    OrderCancelled,
    ProductCreated,
    ProductUpdated,
    InventoryUpdated,
    FlashSaleStarted,
    FlashSaleEnded
};

class EcommerceService
{
    protected $cache;
    protected $storage;
    protected $cartService;
    protected $paymentService;
    protected $analyticsService;
    protected $marketingService;

    public function __construct(
        CartService $cartService,
        PaymentService $paymentService,
        AnalyticsService $analyticsService,
        MarketingService $marketingService
    ) {
        $this->cache = Cache::tags(['ecommerce']);
        $this->storage = Storage::disk('public');
        $this->cartService = $cartService;
        $this->paymentService = $paymentService;
        $this->analyticsService = $analyticsService;
        $this->marketingService = $marketingService;
    }

    /**
     * Create or update product
     */
    public function createOrUpdateProduct(array $data)
    {
        DB::beginTransaction();
        try {
            $product = Product::updateOrCreate(
                ['id' => $data['id'] ?? null],
                [
                    'name' => $data['name'],
                    'slug' => $data['slug'] ?? Str::slug($data['name']),
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'compare_price' => $data['compare_price'] ?? null,
                    'category_id' => $data['category_id'],
                    'brand' => $data['brand'] ?? null,
                    'sku' => $data['sku'] ?? null,
                    'barcode' => $data['barcode'] ?? null,
                    'weight' => $data['weight'] ?? null,
                    'dimensions' => $data['dimensions'] ?? null,
                    'is_featured' => $data['is_featured'] ?? false,
                    'is_published' => $data['is_published'] ?? true,
                    'meta_title' => $data['meta_title'] ?? null,
                    'meta_description' => $data['meta_description'] ?? null,
                    'featured_image' => $this->handleImageUpload($data['featured_image'] ?? null),
                    'gallery' => $this->handleMultipleImages($data['gallery'] ?? []),
                ]
            );

            // Handle variants
            if (!empty($data['variants'])) {
                $this->handleProductVariants($product, $data['variants']);
            }

            // Handle inventory
            if (!empty($data['inventory'])) {
                $this->handleProductInventory($product, $data['inventory']);
            }

            DB::commit();
            $this->cache->flush();
            
            if (isset($data['id'])) {
                event(new ProductUpdated($product));
            } else {
                event(new ProductCreated($product));
            }

            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Handle product variants
     */
    protected function handleProductVariants($product, array $variants)
    {
        $product->variants()->delete();

        foreach ($variants as $variant) {
            $product->variants()->create([
                'name' => $variant['name'],
                'sku' => $variant['sku'] ?? null,
                'price' => $variant['price'],
                'compare_price' => $variant['compare_price'] ?? null,
                'weight' => $variant['weight'] ?? null,
                'dimensions' => $variant['dimensions'] ?? null,
                'is_default' => $variant['is_default'] ?? false,
                'options' => $variant['options'],
            ]);
        }
    }

    /**
     * Handle product inventory
     */
    protected function handleProductInventory($product, array $inventory)
    {
        $product->inventory()->updateOrCreate(
            ['product_id' => $product->id],
            [
                'quantity' => $inventory['quantity'],
                'low_stock_threshold' => $inventory['low_stock_threshold'] ?? 5,
                'warehouse_id' => $inventory['warehouse_id'] ?? null,
                'location' => $inventory['location'] ?? null,
            ]
        );
    }

    /**
     * Create or update category
     */
    public function createOrUpdateCategory(array $data)
    {
        $category = Category::updateOrCreate(
            ['id' => $data['id'] ?? null],
            [
                'name' => $data['name'],
                'slug' => $data['slug'] ?? Str::slug($data['name']),
                'description' => $data['description'] ?? null,
                'parent_id' => $data['parent_id'] ?? null,
                'is_featured' => $data['is_featured'] ?? false,
                'is_published' => $data['is_published'] ?? true,
                'meta_title' => $data['meta_title'] ?? null,
                'meta_description' => $data['meta_description'] ?? null,
                'image' => $this->handleImageUpload($data['image'] ?? null),
            ]
        );

        $this->cache->flush();
        return $category;
    }

    /**
     * Create order
     */
    public function createOrder(array $data, $customerId)
    {
        DB::beginTransaction();
        try {
            $cart = $this->cartService->getCart($data['cart_id']);
            
            $order = Order::create([
                'customer_id' => $customerId,
                'status' => 'pending',
                'subtotal' => $cart->subtotal,
                'tax' => $cart->tax,
                'shipping' => $cart->shipping,
                'discount' => $cart->discount,
                'total' => $cart->total,
                'shipping_address' => $data['shipping_address'],
                'billing_address' => $data['billing_address'],
                'payment_method' => $data['payment_method'],
                'shipping_method' => $data['shipping_method'],
                'notes' => $data['notes'] ?? null,
            ]);

            // Add order items
            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->total,
                ]);

                // Update inventory
                $this->updateInventory($item->product_id, $item->variant_id, -$item->quantity);
            }

            // Process payment
            $payment = $this->paymentService->processPayment($order, $data['payment_details']);

            if ($payment->success) {
                $order->update(['status' => 'paid']);
                event(new OrderPlaced($order));
            } else {
                throw new \Exception($payment->message);
            }

            // Clear cart
            $this->cartService->clearCart($cart->id);

            DB::commit();
            $this->cache->flush();

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update inventory
     */
    protected function updateInventory($productId, $variantId, $quantity)
    {
        $inventory = Inventory::where('product_id', $productId)
            ->where('variant_id', $variantId)
            ->first();

        if ($inventory) {
            $inventory->update([
                'quantity' => DB::raw("quantity + {$quantity}")
            ]);

            event(new InventoryUpdated($inventory));

            // Check low stock
            if ($inventory->quantity <= $inventory->low_stock_threshold) {
                // Notify admin
            }
        }
    }

    /**
     * Create flash sale
     */
    public function createFlashSale(array $data)
    {
        $flashSale = FlashSale::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'is_active' => true,
            'discount_type' => $data['discount_type'],
            'discount_value' => $data['discount_value'],
            'products' => $data['products'],
            'categories' => $data['categories'] ?? [],
        ]);

        event(new FlashSaleStarted($flashSale));
        return $flashSale;
    }

    /**
     * Create subscription
     */
    public function createSubscription(array $data, $customerId)
    {
        $subscription = Subscription::create([
            'customer_id' => $customerId,
            'plan_id' => $data['plan_id'],
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addDays($data['duration']),
            'billing_cycle' => $data['billing_cycle'],
            'payment_method' => $data['payment_method'],
            'auto_renew' => $data['auto_renew'] ?? true,
        ]);

        return $subscription;
    }

    /**
     * Add product review
     */
    public function addReview(array $data, $customerId)
    {
        $review = Review::create([
            'customer_id' => $customerId,
            'product_id' => $data['product_id'],
            'rating' => $data['rating'],
            'title' => $data['title'],
            'content' => $data['content'],
            'is_approved' => false,
        ]);

        return $review;
    }

    /**
     * Add to wishlist
     */
    public function addToWishlist($productId, $customerId)
    {
        $wishlist = Wishlist::firstOrCreate(['customer_id' => $customerId]);
        $wishlist->products()->syncWithoutDetaching([$productId]);

        return $wishlist;
    }

    /**
     * Get product recommendations
     */
    public function getProductRecommendations($customerId)
    {
        return $this->cache->remember("recommendations.{$customerId}", 3600, function () use ($customerId) {
            $customer = Customer::find($customerId);
            
            // Get customer's order history
            $orderHistory = $customer->orders()
                ->with('items.product')
                ->get()
                ->pluck('items')
                ->flatten()
                ->pluck('product');

            // Get similar products
            $similarProducts = Product::whereIn('category_id', $orderHistory->pluck('category_id'))
                ->whereNotIn('id', $orderHistory->pluck('id'))
                ->inRandomOrder()
                ->take(10)
                ->get();

            return $similarProducts;
        });
    }

    /**
     * Handle single image upload
     */
    protected function handleImageUpload($image)
    {
        if (!$image) return null;

        if (is_string($image)) {
            return $image;
        }

        $path = $image->store('products/images', 'public');
        return $this->storage->url($path);
    }

    /**
     * Handle multiple image uploads
     */
    protected function handleMultipleImages($images)
    {
        if (empty($images)) return [];

        return collect($images)->map(function ($image) {
            return $this->handleImageUpload($image);
        })->filter()->values()->toArray();
    }

    /**
     * Get store statistics
     */
    public function getStoreStatistics()
    {
        return $this->cache->remember('store.statistics', 3600, function () {
            return [
                'total_products' => Product::count(),
                'total_orders' => Order::count(),
                'total_customers' => Customer::count(),
                'total_revenue' => Order::where('status', 'completed')->sum('total'),
                'average_order_value' => Order::where('status', 'completed')->avg('total'),
                'top_products' => $this->getTopProducts(),
                'recent_orders' => Order::with('customer')->latest()->take(5)->get(),
                'low_stock_products' => $this->getLowStockProducts(),
            ];
        });
    }

    /**
     * Get top products
     */
    protected function getTopProducts()
    {
        return Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(5)
            ->get();
    }

    /**
     * Get low stock products
     */
    protected function getLowStockProducts()
    {
        return Product::whereHas('inventory', function ($query) {
            $query->whereRaw('quantity <= low_stock_threshold');
        })->take(5)->get();
    }
} 