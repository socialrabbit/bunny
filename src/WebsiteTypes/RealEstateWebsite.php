<?php

namespace Bunny\WebsiteTypes;

class RealEstateWebsite extends BaseWebsiteType
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
            'property_listings',
            'virtual_tours',
            'agent_profiles',
            'mortgage_calculator',
            'property_search',
            'saved_searches',
            'contact_forms',
            'market_analysis',
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
        $this->createDefaultProperties();
        $this->createDefaultAgents();
        $this->createDefaultAmenities();
        $this->createDefaultLocations();
        $this->createDefaultMarketReports();
    }

    protected function createDefaultPages()
    {
        $pages = [
            [
                'title' => 'Home',
                'slug' => 'home',
                'content' => 'Welcome to our real estate platform...',
            ],
            [
                'title' => 'Properties',
                'slug' => 'properties',
                'content' => 'Browse our property listings...',
            ],
            [
                'title' => 'Agents',
                'slug' => 'agents',
                'content' => 'Meet our real estate agents...',
            ],
            [
                'title' => 'Market Analysis',
                'slug' => 'market-analysis',
                'content' => 'Real estate market insights...',
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

    protected function createDefaultProperties()
    {
        $properties = [
            [
                'title' => 'Modern Downtown Apartment',
                'slug' => 'modern-downtown-apartment',
                'description' => 'Beautiful apartment in the heart of downtown',
                'type' => 'apartment',
                'status' => 'for_sale',
                'price' => 500000.00,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'area' => 1200,
                'location_id' => 1,
                'agent_id' => 1,
                'features' => [
                    'parking',
                    'elevator',
                    'security',
                    'gym',
                ],
                'images' => [
                    'properties/apartment-1.jpg',
                    'properties/apartment-2.jpg',
                ],
                'virtual_tour_url' => 'https://virtualtour.example.com/property1',
            ],
            [
                'title' => 'Luxury Villa with Pool',
                'slug' => 'luxury-villa-pool',
                'description' => 'Spacious villa with private pool',
                'type' => 'villa',
                'status' => 'for_rent',
                'price' => 3500.00,
                'bedrooms' => 4,
                'bathrooms' => 3,
                'area' => 2500,
                'location_id' => 2,
                'agent_id' => 2,
                'features' => [
                    'pool',
                    'garden',
                    'garage',
                    'security',
                ],
                'images' => [
                    'properties/villa-1.jpg',
                    'properties/villa-2.jpg',
                ],
                'virtual_tour_url' => 'https://virtualtour.example.com/property2',
            ],
        ];

        foreach ($properties as $property) {
            \App\Models\Property::create($property);
        }
    }

    protected function createDefaultAgents()
    {
        $agents = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@example.com',
                'phone' => '+1234567890',
                'bio' => 'Experienced real estate agent specializing in luxury properties',
                'image' => 'agents/john-smith.jpg',
                'specializations' => ['luxury', 'residential', 'commercial'],
                'languages' => ['English', 'Spanish'],
                'experience_years' => 10,
            ],
            [
                'name' => 'Jane Doe',
                'email' => 'jane.doe@example.com',
                'phone' => '+1987654321',
                'bio' => 'Expert in residential properties and investment opportunities',
                'image' => 'agents/jane-doe.jpg',
                'specializations' => ['residential', 'investment', 'rental'],
                'languages' => ['English', 'French'],
                'experience_years' => 8,
            ],
        ];

        foreach ($agents as $agent) {
            \App\Models\Agent::create($agent);
        }
    }

    protected function createDefaultAmenities()
    {
        $amenities = [
            [
                'name' => 'Swimming Pool',
                'icon' => 'pool',
                'description' => 'Private or shared swimming pool',
            ],
            [
                'name' => 'Parking',
                'icon' => 'parking',
                'description' => 'Dedicated parking space',
            ],
            [
                'name' => 'Security System',
                'icon' => 'security',
                'description' => '24/7 security system',
            ],
            [
                'name' => 'Gym',
                'icon' => 'gym',
                'description' => 'On-site fitness center',
            ],
        ];

        foreach ($amenities as $amenity) {
            \App\Models\Amenity::create($amenity);
        }
    }

    protected function createDefaultLocations()
    {
        $locations = [
            [
                'name' => 'Downtown',
                'slug' => 'downtown',
                'description' => 'City center area',
                'parent_id' => null,
                'coordinates' => [
                    'lat' => 40.7128,
                    'lng' => -74.0060,
                ],
            ],
            [
                'name' => 'Suburbs',
                'slug' => 'suburbs',
                'description' => 'Residential suburban area',
                'parent_id' => null,
                'coordinates' => [
                    'lat' => 40.7580,
                    'lng' => -73.9855,
                ],
            ],
        ];

        foreach ($locations as $location) {
            \App\Models\Location::create($location);
        }
    }

    protected function createDefaultMarketReports()
    {
        $reports = [
            [
                'title' => 'Monthly Market Overview',
                'slug' => 'monthly-market-overview',
                'description' => 'Analysis of current market trends',
                'type' => 'monthly',
                'data' => [
                    'average_price' => 450000.00,
                    'total_sales' => 150,
                    'days_on_market' => 45,
                    'price_trend' => 'increasing',
                ],
                'published_at' => now(),
            ],
            [
                'title' => 'Quarterly Investment Report',
                'slug' => 'quarterly-investment-report',
                'description' => 'Investment opportunities and analysis',
                'type' => 'quarterly',
                'data' => [
                    'roi_average' => 8.5,
                    'rental_yield' => 5.2,
                    'market_growth' => 3.8,
                    'investment_hotspots' => ['downtown', 'suburbs'],
                ],
                'published_at' => now(),
            ],
        ];

        foreach ($reports as $report) {
            \App\Models\MarketReport::create($report);
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