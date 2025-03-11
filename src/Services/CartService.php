<?php

namespace Bunny\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class CartService
{
    protected $cart;
    protected $sessionKey = 'bunny_cart';
    protected $cacheKey = 'bunny_cart_';
    protected $ttl = 3600; // 1 hour

    public function __construct()
    {
        $this->cart = $this->loadCart();
    }

    public function add($productId, $quantity = 1, $options = [])
    {
        $item = [
            'product_id' => $productId,
            'quantity' => $quantity,
            'options' => $options,
            'added_at' => now(),
        ];

        $this->cart->push($item);
        $this->saveCart();

        return $this;
    }

    public function update($productId, $quantity, $options = [])
    {
        $this->cart = $this->cart->map(function ($item) use ($productId, $quantity, $options) {
            if ($item['product_id'] === $productId) {
                return array_merge($item, [
                    'quantity' => $quantity,
                    'options' => $options,
                    'updated_at' => now(),
                ]);
            }
            return $item;
        });

        $this->saveCart();
        return $this;
    }

    public function remove($productId)
    {
        $this->cart = $this->cart->filter(function ($item) use ($productId) {
            return $item['product_id'] !== $productId;
        });

        $this->saveCart();
        return $this;
    }

    public function clear()
    {
        $this->cart = collect([]);
        $this->saveCart();
        return $this;
    }

    public function getItems()
    {
        return $this->cart;
    }

    public function getTotal()
    {
        return $this->cart->sum(function ($item) {
            return $item['quantity'] * $this->getProductPrice($item['product_id']);
        });
    }

    public function getCount()
    {
        return $this->cart->sum('quantity');
    }

    public function isEmpty()
    {
        return $this->cart->isEmpty();
    }

    protected function loadCart()
    {
        $sessionId = Session::getId();
        $cart = Cache::get($this->cacheKey . $sessionId);

        if (!$cart) {
            $cart = Session::get($this->sessionKey, collect([]));
            Cache::put($this->cacheKey . $sessionId, $cart, $this->ttl);
        }

        return collect($cart);
    }

    protected function saveCart()
    {
        $sessionId = Session::getId();
        Session::put($this->sessionKey, $this->cart);
        Cache::put($this->cacheKey . $sessionId, $this->cart, $this->ttl);
    }

    protected function getProductPrice($productId)
    {
        // Implement product price retrieval logic
        return app('bunny.products')->getPrice($productId);
    }

    public function persistForUser($userId)
    {
        $sessionId = Session::getId();
        $cart = Cache::get($this->cacheKey . $sessionId);

        if ($cart) {
            Cache::put($this->cacheKey . 'user_' . $userId, $cart, $this->ttl);
            Cache::forget($this->cacheKey . $sessionId);
        }

        return $this;
    }

    public function loadUserCart($userId)
    {
        $cart = Cache::get($this->cacheKey . 'user_' . $userId);
        if ($cart) {
            $this->cart = collect($cart);
            $this->saveCart();
        }

        return $this;
    }
} 