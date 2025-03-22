<?php

namespace Bunny\WebsiteTypes;

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
        // Create default ecommerce data
    }
}