<?php

namespace Kisalay\Bunny\WebsiteTypes;

class EcommerceWebsite extends BaseWebsiteType
{
    public function install()
    {
        $this->addFeatures();
        $this->addDependencies();
        $this->publishAssets();
        $this->runMigrations();
        $this->updateConfig();
        $this->createDefaultData();
    }

    public function uninstall()
    {
        $this->rollbackMigrations();
        $this->removeAssets();
    }

    public function getFeatures()
    {
        return [
            'smart_cart',
            'product_management',
            'order_processing',
            'inventory_tracking',
            'customer_management',
            'marketing_tools',
            'analytics_dashboard',
            'payment_processing',
        ];
    }

    public function getDependencies()
    {
        return [
            'auth',
            'roles',
            'media',
            'notifications',
            'payment',
            'shipping',
            'tax',
            'inventory',
        ];
    }

    protected function addFeatures()
    {
        foreach ($this->getFeatures() as $feature) {
            $this->addFeature($feature);
        }
    }

    protected function addDependencies()
    {
        foreach ($this->getDependencies() as $dependency) {
            $this->addDependency($dependency);
        }
    }

    protected function createDefaultData()
    {
        $this->createDefaultPages();
        $this->createDefaultCategories();
        $this->createDefaultProducts();
        $this->createDefaultShippingZones();
        $this->createDefaultTaxRates();
        $this->createDefaultPaymentMethods();
    }

    protected function createDefaultPages()
    {
        $pages = [
            [
                'title' => 'Home',
                'slug' => 'home',
                'content' => 'Welcome to our store...',
            ],
            [
                'title' => 'Shop',
                'slug' => 'shop',
                'content' => 'Browse our products...',
            ],
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => 'Learn about our company...',
            ],
            [
                'title' => 'Contact',
                'slug' => 'contact',
                'content' => 'Get in touch...',
            ],
        ];

        foreach ($pages as $page) {
            \App\Models\Page::create($page);
        }
    }

    protected function createDefaultCategories()
    {
        $categories = [
            [
                'name' => 'Electronics',
                'slug' => 'electronics',
                'description' => 'Electronic devices and accessories',
                'parent_id' => null,
            ],
            [
                'name' => 'Clothing',
                'slug' => 'clothing',
                'description' => 'Fashion and apparel',
                'parent_id' => null,
            ],
            [
                'name' => 'Home & Living',
                'slug' => 'home-living',
                'description' => 'Home decor and furniture',
                'parent_id' => null,
            ],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }

    protected function createDefaultProducts()
    {
        $products = [
            [
                'name' => 'Product 1',
                'slug' => 'product-1',
                'description' => 'Description of product 1...',
                'price' => 99.99,
                'sale_price' => 79.99,
                'category_id' => 1,
                'stock' => 100,
                'sku' => 'PROD-001',
                'image' => 'products/product-1.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'Product 2',
                'slug' => 'product-2',
                'description' => 'Description of product 2...',
                'price' => 149.99,
                'sale_price' => 129.99,
                'category_id' => 2,
                'stock' => 50,
                'sku' => 'PROD-002',
                'image' => 'products/product-2.jpg',
                'status' => 'active',
            ],
        ];

        foreach ($products as $product) {
            \App\Models\Product::create($product);
        }
    }

    protected function createDefaultShippingZones()
    {
        $zones = [
            [
                'name' => 'Standard Shipping',
                'description' => 'Standard shipping within the country',
                'price' => 5.99,
                'estimated_days' => '3-5',
                'countries' => ['US', 'CA'],
            ],
            [
                'name' => 'Express Shipping',
                'description' => 'Express shipping within the country',
                'price' => 12.99,
                'estimated_days' => '1-2',
                'countries' => ['US', 'CA'],
            ],
        ];

        foreach ($zones as $zone) {
            \App\Models\ShippingZone::create($zone);
        }
    }

    protected function createDefaultTaxRates()
    {
        $taxRates = [
            [
                'name' => 'Standard VAT',
                'rate' => 20,
                'description' => 'Standard VAT rate',
                'countries' => ['GB', 'DE', 'FR'],
            ],
            [
                'name' => 'US Sales Tax',
                'rate' => 8.25,
                'description' => 'Standard US sales tax',
                'countries' => ['US'],
            ],
        ];

        foreach ($taxRates as $rate) {
            \App\Models\TaxRate::create($rate);
        }
    }

    protected function createDefaultPaymentMethods()
    {
        $paymentMethods = [
            [
                'name' => 'Credit Card',
                'code' => 'credit_card',
                'description' => 'Pay with credit card',
                'enabled' => true,
                'settings' => [
                    'stripe_key' => 'your_stripe_key',
                    'stripe_secret' => 'your_stripe_secret',
                ],
            ],
            [
                'name' => 'PayPal',
                'code' => 'paypal',
                'description' => 'Pay with PayPal',
                'enabled' => true,
                'settings' => [
                    'client_id' => 'your_paypal_client_id',
                    'client_secret' => 'your_paypal_client_secret',
                ],
            ],
        ];

        foreach ($paymentMethods as $method) {
            \App\Models\PaymentMethod::create($method);
        }
    }

    protected function removeAssets()
    {
        $paths = [
            resource_path("views/vendor/bunny/{$this->type}"),
            config_path("bunny/{$this->type}.php"),
            database_path("migrations/vendor/bunny/{$this->type}"),
            resource_path("lang/vendor/bunny/{$this->type}"),
        ];

        foreach ($paths as $path) {
            if (File::exists($path)) {
                File::deleteDirectory($path);
            }
        }
    }
} 