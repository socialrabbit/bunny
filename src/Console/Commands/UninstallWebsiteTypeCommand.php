<?php

namespace Kisalay\Bunny\Console\Commands;

use Illuminate\Console\Command;
use Kisalay\Bunny\WebsiteTypes\WebsiteTypeManager;

class UninstallWebsiteTypeCommand extends Command
{
    protected $signature = 'bunny:uninstall-type {type} {--force}';
    protected $description = 'Uninstall a specific website type';

    protected $manager;

    public function __construct(WebsiteTypeManager $manager)
    {
        parent::__construct();
        $this->manager = $manager;
    }

    public function handle()
    {
        $type = $this->argument('type');
        $force = $this->option('force');

        if (!$this->manager->hasType($type)) {
            $this->error("Website type '{$type}' does not exist.");
            $this->info('Available types:');
            $this->displayAvailableTypes();
            return 1;
        }

        $typeInfo = $this->manager->getTypeInfo($type);
        $this->info("Uninstalling {$typeInfo['type']} website type...");
        $this->displayTypeInfo($typeInfo);

        if (!$force) {
            $this->warn('Warning: This will remove all data and assets related to this website type.');
            if (!$this->confirm('Are you sure you want to proceed?')) {
                $this->info('Uninstallation cancelled.');
                return 0;
            }
        }

        try {
            if ($this->manager->uninstall($type)) {
                $this->info("Successfully uninstalled {$typeInfo['type']} website type!");
                $this->info('Next steps:');
                $this->displayNextSteps();
            } else {
                $this->error("Failed to uninstall {$typeInfo['type']} website type.");
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("Error uninstalling {$typeInfo['type']} website type: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }

    protected function displayAvailableTypes()
    {
        $types = $this->manager->getAllTypeInfo();
        
        foreach ($types as $type => $info) {
            $this->line(sprintf(
                '  %s - %s',
                str_pad($type, 20),
                $this->manager->getTypeDescription($type)
            ));
        }
    }

    protected function displayTypeInfo(array $info)
    {
        $this->line("\nFeatures that will be removed:");
        foreach ($info['features'] as $feature) {
            $this->line("  - {$feature}");
        }

        $this->line("\nDependencies that will be removed:");
        foreach ($info['dependencies'] as $dependency) {
            $this->line("  - {$dependency}");
        }

        $this->line("\nDescription:");
        $this->line("  " . $this->manager->getTypeDescription($info['type']));
    }

    protected function displayNextSteps()
    {
        $this->line("\n1. Clear cache:");
        $this->line("   php artisan cache:clear");
        $this->line("   php artisan config:clear");
        $this->line("   php artisan view:clear");

        $this->line("\n2. Restart your development server:");
        $this->line("   php artisan serve");

        $this->line("\nNote: If you want to reinstall this website type later, you can use:");
        $this->line("   php artisan bunny:install-type {type}");
    }
} 