<?php

namespace Kisalay\Bunny\WebsiteTypes;

class RestaurantWebsite extends BaseWebsiteType
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
            'menu_management',
            'table_reservation',
            'online_ordering',
            'delivery_tracking',
            'loyalty_program',
            'special_offers',
            'gallery_management',
            'customer_reviews',
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
            'calendar',
            'chat',
            'analytics',
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
        $this->createDefaultMenuCategories();
        $this->createDefaultMenuItems();
        $this->createDefaultTables();
        $this->createDefaultSpecialOffers();
        $this->createDefaultGallery();
    }

    protected function createDefaultPages()
    {
        $pages = [
            [
                'title' => 'Home',
                'slug' => 'home',
                'content' => 'Welcome to our restaurant...',
            ],
            [
                'title' => 'Menu',
                'slug' => 'menu',
                'content' => 'Explore our delicious menu...',
            ],
            [
                'title' => 'Reservations',
                'slug' => 'reservations',
                'content' => 'Book your table...',
            ],
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => 'Our story and values...',
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

    protected function createDefaultMenuCategories()
    {
        $categories = [
            [
                'name' => 'Appetizers',
                'slug' => 'appetizers',
                'description' => 'Start your meal with our delicious appetizers',
                'image' => 'menu/categories/appetizers.jpg',
            ],
            [
                'name' => 'Main Course',
                'slug' => 'main-course',
                'description' => 'Our signature main dishes',
                'image' => 'menu/categories/main-course.jpg',
            ],
            [
                'name' => 'Desserts',
                'slug' => 'desserts',
                'description' => 'Sweet endings to your meal',
                'image' => 'menu/categories/desserts.jpg',
            ],
            [
                'name' => 'Beverages',
                'slug' => 'beverages',
                'description' => 'Refreshing drinks and cocktails',
                'image' => 'menu/categories/beverages.jpg',
            ],
        ];

        foreach ($categories as $category) {
            \App\Models\MenuCategory::create($category);
        }
    }

    protected function createDefaultMenuItems()
    {
        $items = [
            [
                'name' => 'Bruschetta',
                'slug' => 'bruschetta',
                'description' => 'Toasted bread with fresh tomatoes and herbs',
                'price' => 8.99,
                'category_id' => 1,
                'image' => 'menu/items/bruschetta.jpg',
                'spicy_level' => 0,
                'vegetarian' => true,
                'gluten_free' => false,
            ],
            [
                'name' => 'Grilled Salmon',
                'slug' => 'grilled-salmon',
                'description' => 'Fresh salmon with seasonal vegetables',
                'price' => 24.99,
                'category_id' => 2,
                'image' => 'menu/items/salmon.jpg',
                'spicy_level' => 0,
                'vegetarian' => false,
                'gluten_free' => true,
            ],
            [
                'name' => 'Tiramisu',
                'slug' => 'tiramisu',
                'description' => 'Classic Italian dessert with coffee and mascarpone',
                'price' => 7.99,
                'category_id' => 3,
                'image' => 'menu/items/tiramisu.jpg',
                'spicy_level' => 0,
                'vegetarian' => true,
                'gluten_free' => false,
            ],
        ];

        foreach ($items as $item) {
            \App\Models\MenuItem::create($item);
        }
    }

    protected function createDefaultTables()
    {
        $tables = [
            [
                'number' => 'T1',
                'capacity' => 2,
                'location' => 'Indoor',
                'status' => 'available',
                'features' => ['window', 'quiet'],
            ],
            [
                'number' => 'T2',
                'capacity' => 4,
                'location' => 'Outdoor',
                'status' => 'available',
                'features' => ['patio', 'shade'],
            ],
            [
                'number' => 'T3',
                'capacity' => 6,
                'location' => 'Indoor',
                'status' => 'available',
                'features' => ['private', 'booth'],
            ],
        ];

        foreach ($tables as $table) {
            \App\Models\Table::create($table);
        }
    }

    protected function createDefaultSpecialOffers()
    {
        $offers = [
            [
                'name' => 'Happy Hour',
                'slug' => 'happy-hour',
                'description' => 'Special prices on drinks and appetizers',
                'discount' => 20,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(3),
                'terms' => 'Monday to Friday, 4-7 PM',
                'image' => 'offers/happy-hour.jpg',
            ],
            [
                'name' => 'Weekend Brunch',
                'slug' => 'weekend-brunch',
                'description' => 'All-you-can-eat brunch buffet',
                'discount' => 15,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(6),
                'terms' => 'Saturdays and Sundays, 10 AM - 2 PM',
                'image' => 'offers/brunch.jpg',
            ],
        ];

        foreach ($offers as $offer) {
            \App\Models\SpecialOffer::create($offer);
        }
    }

    protected function createDefaultGallery()
    {
        $gallery = [
            [
                'title' => 'Restaurant Interior',
                'description' => 'Our cozy dining area',
                'image' => 'gallery/interior.jpg',
                'category' => 'Ambience',
            ],
            [
                'title' => 'Signature Dish',
                'description' => 'Our most popular dish',
                'image' => 'gallery/signature-dish.jpg',
                'category' => 'Food',
            ],
            [
                'title' => 'Outdoor Seating',
                'description' => 'Beautiful patio area',
                'image' => 'gallery/patio.jpg',
                'category' => 'Ambience',
            ],
        ];

        foreach ($gallery as $item) {
            \App\Models\Gallery::create($item);
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