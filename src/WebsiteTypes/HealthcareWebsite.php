<?php

namespace Bunny\WebsiteTypes;

class HealthcareWebsite extends BaseWebsiteType
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
            'patient_portal',
            'appointment_scheduling',
            'medical_records',
            'prescription_management',
            'telemedicine_integration',
            'health_blog',
            'insurance_verification',
            'emergency_contact',
        ];
    }

    public function getDependencies()
    {
        return [
            'auth',
            'roles',
            'media',
            'notifications',
            'calendar',
            'chat',
            'payment',
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
        // Create default healthcare data
    }
}