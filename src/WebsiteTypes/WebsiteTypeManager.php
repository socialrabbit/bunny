<?php

namespace Kisalay\Bunny\WebsiteTypes;

use Illuminate\Support\Collection;

class WebsiteTypeManager
{
    protected $types;
    protected $instances = [];

    public function __construct(array $types)
    {
        $this->types = $types;
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    public function getType(string $type): ?BaseWebsiteType
    {
        if (!isset($this->types[$type])) {
            return null;
        }

        if (!isset($this->instances[$type])) {
            $class = $this->types[$type];
            $this->instances[$type] = new $class();
        }

        return $this->instances[$type];
    }

    public function install(string $type): bool
    {
        $websiteType = $this->getType($type);
        
        if (!$websiteType) {
            return false;
        }

        try {
            $websiteType->install();
            return true;
        } catch (\Exception $e) {
            \Log::error("Error installing website type {$type}: " . $e->getMessage());
            return false;
        }
    }

    public function uninstall(string $type): bool
    {
        $websiteType = $this->getType($type);
        
        if (!$websiteType) {
            return false;
        }

        try {
            $websiteType->uninstall();
            return true;
        } catch (\Exception $e) {
            \Log::error("Error uninstalling website type {$type}: " . $e->getMessage());
            return false;
        }
    }

    public function getFeatures(string $type): array
    {
        $websiteType = $this->getType($type);
        
        if (!$websiteType) {
            return [];
        }

        return $websiteType->getFeatures();
    }

    public function getDependencies(string $type): array
    {
        $websiteType = $this->getType($type);
        
        if (!$websiteType) {
            return [];
        }

        return $websiteType->getDependencies();
    }

    public function getAllFeatures(): Collection
    {
        $features = collect();
        
        foreach ($this->types as $type => $class) {
            $websiteType = $this->getType($type);
            if ($websiteType) {
                $features->put($type, $websiteType->getFeatures());
            }
        }

        return $features;
    }

    public function getAllDependencies(): Collection
    {
        $dependencies = collect();
        
        foreach ($this->types as $type => $class) {
            $websiteType = $this->getType($type);
            if ($websiteType) {
                $dependencies->put($type, $websiteType->getDependencies());
            }
        }

        return $dependencies;
    }

    public function getTypeInfo(string $type): array
    {
        $websiteType = $this->getType($type);
        
        if (!$websiteType) {
            return [];
        }

        return [
            'type' => $type,
            'features' => $websiteType->getFeatures(),
            'dependencies' => $websiteType->getDependencies(),
            'class' => get_class($websiteType),
        ];
    }

    public function getAllTypeInfo(): Collection
    {
        $info = collect();
        
        foreach ($this->types as $type => $class) {
            $info->put($type, $this->getTypeInfo($type));
        }

        return $info;
    }

    public function hasType(string $type): bool
    {
        return isset($this->types[$type]);
    }

    public function getTypeClass(string $type): ?string
    {
        return $this->types[$type] ?? null;
    }

    public function getTypeInstance(string $type): ?BaseWebsiteType
    {
        return $this->getType($type);
    }

    public function getTypeName(string $type): string
    {
        return ucwords(str_replace('-', ' ', $type));
    }

    public function getTypeDescription(string $type): string
    {
        $descriptions = [
            'ecommerce' => 'Online store with product management and payment processing',
            'portfolio' => 'Personal or professional portfolio website',
            'educational' => 'Educational platform with courses and learning management',
            'healthcare' => 'Healthcare website with patient portal and appointment scheduling',
            'hospitality' => 'Hotel and hospitality management system',
            'real-estate' => 'Real estate website with property listings and agent profiles',
            'restaurant' => 'Restaurant website with menu management and reservations',
            'fitness' => 'Fitness center website with class scheduling and member portal',
        ];

        return $descriptions[$type] ?? 'No description available';
    }

    public function getTypeIcon(string $type): string
    {
        $icons = [
            'ecommerce' => 'shopping-cart',
            'portfolio' => 'briefcase',
            'educational' => 'graduation-cap',
            'healthcare' => 'heartbeat',
            'hospitality' => 'bed',
            'real-estate' => 'home',
            'restaurant' => 'utensils',
            'fitness' => 'dumbbell',
        ];

        return $icons[$type] ?? 'globe';
    }

    public function getTypeColor(string $type): string
    {
        $colors = [
            'ecommerce' => '#4CAF50',
            'portfolio' => '#2196F3',
            'educational' => '#9C27B0',
            'healthcare' => '#F44336',
            'hospitality' => '#FF9800',
            'real-estate' => '#795548',
            'restaurant' => '#E91E63',
            'fitness' => '#00BCD4',
        ];

        return $colors[$type] ?? '#607D8B';
    }
} 