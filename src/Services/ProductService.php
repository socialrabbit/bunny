<?php

namespace Bunny\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Bunny\Models\Product;
use Bunny\Models\ProductVariant;
use Bunny\Models\ProductCategory;
use Bunny\Events\ProductStockLow;
use Bunny\Events\ProductCreated;
use Bunny\Events\ProductUpdated;

class ProductService
{
    protected $cacheKey = 'bunny_product_';
    protected $ttl = 3600; // 1 hour

    public function create(array $data)
    {
        $product = Product::create($data);
        
        if (isset($data['variants'])) {
            $this->createVariants($product, $data['variants']);
        }

        event(new ProductCreated($product));
        return $product;
    }

    public function update($productId, array $data)
    {
        $product = Product::findOrFail($productId);
        $product->update($data);

        if (isset($data['variants'])) {
            $this->updateVariants($product, $data['variants']);
        }

        event(new ProductUpdated($product));
        return $product;
    }

    public function delete($productId)
    {
        $product = Product::findOrFail($productId);
        $product->delete();
        Cache::forget($this->cacheKey . $productId);
    }

    public function get($productId)
    {
        return Cache::remember($this->cacheKey . $productId, $this->ttl, function () use ($productId) {
            return Product::with(['variants', 'category'])->findOrFail($productId);
        });
    }

    public function getPrice($productId, $variantId = null)
    {
        if ($variantId) {
            $variant = ProductVariant::findOrFail($variantId);
            return $variant->price;
        }

        $product = $this->get($productId);
        return $product->price;
    }

    public function bulkImport(array $products)
    {
        $imported = [];
        foreach ($products as $product) {
            $imported[] = $this->create($product);
        }
        return $imported;
    }

    public function bulkExport()
    {
        return Product::with(['variants', 'category'])->get();
    }

    public function checkStock()
    {
        $lowStockProducts = Product::where('stock', '<=', config('bunny.ecommerce.low_stock_threshold'))->get();
        
        foreach ($lowStockProducts as $product) {
            event(new ProductStockLow($product));
        }

        return $lowStockProducts;
    }

    public function createVariants(Product $product, array $variants)
    {
        foreach ($variants as $variant) {
            $product->variants()->create($variant);
        }
    }

    public function updateVariants(Product $product, array $variants)
    {
        $existingVariantIds = $product->variants->pluck('id')->toArray();
        $newVariantIds = [];

        foreach ($variants as $variant) {
            if (isset($variant['id'])) {
                $product->variants()->where('id', $variant['id'])->update($variant);
                $newVariantIds[] = $variant['id'];
            } else {
                $newVariant = $product->variants()->create($variant);
                $newVariantIds[] = $newVariant->id;
            }
        }

        // Delete removed variants
        $product->variants()
            ->whereNotIn('id', $newVariantIds)
            ->delete();
    }

    public function handleDigitalDelivery($productId, $userId)
    {
        $product = $this->get($productId);
        
        if (!$product->is_digital) {
            throw new \Exception('Product is not a digital product');
        }

        // Generate download link
        $downloadUrl = $this->generateDownloadUrl($product, $userId);
        
        // Send email with download link
        // Implement email sending logic here

        return $downloadUrl;
    }

    protected function generateDownloadUrl($product, $userId)
    {
        $token = md5($product->id . $userId . time());
        Cache::put('download_' . $token, [
            'product_id' => $product->id,
            'user_id' => $userId,
            'expires_at' => now()->addHours(24)
        ], 86400); // 24 hours

        return route('bunny.download', ['token' => $token]);
    }

    public function search($query, $filters = [])
    {
        $products = Product::query();

        if (!empty($query)) {
            $products->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            });
        }

        if (!empty($filters['category'])) {
            $products->where('category_id', $filters['category']);
        }

        if (!empty($filters['price_min'])) {
            $products->where('price', '>=', $filters['price_min']);
        }

        if (!empty($filters['price_max'])) {
            $products->where('price', '<=', $filters['price_max']);
        }

        if (!empty($filters['in_stock'])) {
            $products->where('stock', '>', 0);
        }

        return $products->with(['variants', 'category'])->paginate(12);
    }
} 