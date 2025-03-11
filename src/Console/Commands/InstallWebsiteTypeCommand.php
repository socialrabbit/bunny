<?php

namespace Kisalay\Bunny\Console\Commands;

use Illuminate\Console\Command;
use Kisalay\Bunny\WebsiteTypes\WebsiteTypeManager;

class InstallWebsiteTypeCommand extends Command
{
    protected $signature = 'bunny:install-type {type} {--force}';
    protected $description = 'Install a specific website type';

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
        $this->info("Installing {$typeInfo['type']} website type...");
        $this->displayTypeInfo($typeInfo);

        if (!$force) {
            if (!$this->confirm('Do you want to proceed with the installation?')) {
                $this->info('Installation cancelled.');
                return 0;
            }
        }

        try {
            if ($this->manager->install($type)) {
                $this->info("Successfully installed {$typeInfo['type']} website type!");
                $this->info('Next steps:');
                $this->displayNextSteps($type);
            } else {
                $this->error("Failed to install {$typeInfo['type']} website type.");
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("Error installing {$typeInfo['type']} website type: {$e->getMessage()}");
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
        $this->line("\nFeatures:");
        foreach ($info['features'] as $feature) {
            $this->line("  - {$feature}");
        }

        $this->line("\nDependencies:");
        foreach ($info['dependencies'] as $dependency) {
            $this->line("  - {$dependency}");
        }

        $this->line("\nDescription:");
        $this->line("  " . $this->manager->getTypeDescription($info['type']));
    }

    protected function displayNextSteps(string $type)
    {
        $this->line("\n1. Configure your website settings:");
        $this->line("   php artisan bunny:config {$type}");

        $this->line("\n2. Publish assets:");
        $this->line("   php artisan vendor:publish --tag=bunny-{$type}-assets");

        $this->line("\n3. Run migrations:");
        $this->line("   php artisan migrate");

        $this->line("\n4. Seed the database:");
        $this->line("   php artisan db:seed --class=Bunny{$type}Seeder");

        $this->line("\n5. Clear cache:");
        $this->line("   php artisan cache:clear");
        $this->line("   php artisan config:clear");
        $this->line("   php artisan view:clear");

        $this->line("\n6. Start your development server:");
        $this->line("   php artisan serve");
    }
} 