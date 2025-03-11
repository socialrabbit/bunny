<?php

namespace SocialRabbit\Bunny\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UninstallCommand extends Command
{
    protected $signature = 'bunny:uninstall {type? : The type of website to uninstall}';
    protected $description = 'Uninstall the Bunny package or a specific website type';

    public function handle()
    {
        $type = $this->argument('type');

        if ($type) {
            $this->uninstallWebsiteType($type);
        } else {
            $this->uninstallPackage();
        }
    }

    protected function uninstallWebsiteType($type)
    {
        $this->info("Uninstalling {$type} website...");

        try {
            $websiteClass = app("bunny.website.{$type}");
            $websiteClass->uninstall();

            $this->info("Website type {$type} has been uninstalled successfully!");
        } catch (\Exception $e) {
            $this->error("An error occurred while uninstalling the website type: " . $e->getMessage());
            return 1;
        }
    }

    protected function uninstallPackage()
    {
        if (!$this->confirm('Are you sure you want to uninstall the Bunny package? This will remove all related files and data.')) {
            return;
        }

        $this->info('Uninstalling Bunny Package...');

        // Remove published files
        $paths = [
            config_path('bunny.php'),
            resource_path('views/vendor/bunny'),
            public_path('vendor/bunny'),
            database_path('migrations/vendor/bunny'),
            resource_path('lang/vendor/bunny'),
        ];

        foreach ($paths as $path) {
            if (File::exists($path)) {
                if (is_dir($path)) {
                    File::deleteDirectory($path);
                } else {
                    File::delete($path);
                }
            }
        }

        // Rollback migrations
        $this->call('migrate:rollback', [
            '--path' => 'vendor/socialrabbit/bunny/database/migrations'
        ]);

        $this->info('Bunny Package has been uninstalled successfully!');
        $this->info('Please remove the package from your composer.json and run composer update');
    }
} 