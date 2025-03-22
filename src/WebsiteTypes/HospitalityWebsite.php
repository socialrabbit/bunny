<?php

namespace Bunny\WebsiteTypes;

class HospitalityWebsite extends BaseWebsiteType
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
            'room_booking',
            'event_management',
            'menu_system',
            'guest_feedback',
            'loyalty_program',
            'special_offers',
            'virtual_tours',
            'online_checkin',
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
        $this->createDefaultRooms();
        $this->createDefaultAmenities();
        $this->createDefaultEvents();
        $this->createDefaultMenuItems();
        $this->createDefaultSpecialOffers();
    }

    protected function createDefaultPages()
    {
        $pages = [
            [
                'title' => 'Home',
                'slug' => 'home',
                'content' => 'Welcome to our hotel...',
            ],
            [
                'title' => 'Rooms',
                'slug' => 'rooms',
                'content' => 'Browse our rooms...',
            ],
            [
                'title' => 'Events',
                'slug' => 'events',
                'content' => 'Host your events with us...',
            ],
            [
                'title' => 'Dining',
                'slug' => 'dining',
                'content' => 'Experience our cuisine...',
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

    protected function createDefaultRooms()
    {
        $rooms = [
            [
                'name' => 'Standard Room',
                'slug' => 'standard-room',
                'description' => 'Comfortable standard room with essential amenities',
                'price' => 100.00,
                'capacity' => 2,
                'amenities' => ['wifi', 'tv', 'air_conditioning', 'bathroom'],
                'images' => [
                    'rooms/standard-1.jpg',
                    'rooms/standard-2.jpg',
                ],
                'status' => 'active',
            ],
            [
                'name' => 'Deluxe Suite',
                'slug' => 'deluxe-suite',
                'description' => 'Spacious suite with premium amenities',
                'price' => 200.00,
                'capacity' => 4,
                'amenities' => ['wifi', 'tv', 'air_conditioning', 'bathroom', 'kitchen', 'balcony'],
                'images' => [
                    'rooms/deluxe-1.jpg',
                    'rooms/deluxe-2.jpg',
                ],
                'status' => 'active',
            ],
        ];

        foreach ($rooms as $room) {
            \App\Models\Room::create($room);
        }
    }

    protected function createDefaultAmenities()
    {
        $amenities = [
            [
                'name' => 'WiFi',
                'icon' => 'wifi',
                'description' => 'Free high-speed internet access',
            ],
            [
                'name' => 'Swimming Pool',
                'icon' => 'pool',
                'description' => 'Outdoor swimming pool',
            ],
            [
                'name' => 'Restaurant',
                'icon' => 'restaurant',
                'description' => 'On-site restaurant',
            ],
            [
                'name' => 'Parking',
                'icon' => 'parking',
                'description' => 'Free parking for guests',
            ],
        ];

        foreach ($amenities as $amenity) {
            \App\Models\Amenity::create($amenity);
        }
    }

    protected function createDefaultEvents()
    {
        $events = [
            [
                'name' => 'Wedding Package',
                'slug' => 'wedding-package',
                'description' => 'Complete wedding celebration package',
                'price' => 5000.00,
                'capacity' => 100,
                'duration' => '1 day',
                'includes' => [
                    'venue',
                    'catering',
                    'decoration',
                    'entertainment',
                ],
                'image' => 'events/wedding.jpg',
            ],
            [
                'name' => 'Corporate Meeting',
                'slug' => 'corporate-meeting',
                'description' => 'Professional meeting space with equipment',
                'price' => 500.00,
                'capacity' => 20,
                'duration' => '1 day',
                'includes' => [
                    'conference room',
                    'projector',
                    'catering',
                    'wifi',
                ],
                'image' => 'events/corporate.jpg',
            ],
        ];

        foreach ($events as $event) {
            \App\Models\Event::create($event);
        }
    }

    protected function createDefaultMenuItems()
    {
        $menuItems = [
            [
                'name' => 'Classic Burger',
                'category' => 'Main Course',
                'description' => 'Juicy beef patty with fresh vegetables',
                'price' => 15.99,
                'image' => 'menu/burger.jpg',
            ],
            [
                'name' => 'Caesar Salad',
                'category' => 'Appetizer',
                'description' => 'Fresh romaine lettuce with parmesan cheese',
                'price' => 12.99,
                'image' => 'menu/salad.jpg',
            ],
            [
                'name' => 'Chocolate Cake',
                'category' => 'Dessert',
                'description' => 'Rich chocolate cake with ganache',
                'price' => 8.99,
                'image' => 'menu/cake.jpg',
            ],
        ];

        foreach ($menuItems as $item) {
            \App\Models\MenuItem::create($item);
        }
    }

    protected function createDefaultSpecialOffers()
    {
        $offers = [
            [
                'name' => 'Weekend Getaway',
                'slug' => 'weekend-getaway',
                'description' => 'Special rates for weekend stays',
                'discount' => 20,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(3),
                'terms' => 'Minimum 2 nights stay',
                'image' => 'offers/weekend.jpg',
            ],
            [
                'name' => 'Honeymoon Package',
                'slug' => 'honeymoon-package',
                'description' => 'Romantic getaway for couples',
                'discount' => 15,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(6),
                'terms' => 'Valid for newlyweds',
                'image' => 'offers/honeymoon.jpg',
            ],
        ];

        foreach ($offers as $offer) {
            \App\Models\SpecialOffer::create($offer);
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