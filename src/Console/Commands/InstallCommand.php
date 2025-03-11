<?php

namespace Kisalay\Bunny\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'bunny:install';
    protected $description = 'Install the Bunny package';

    public function handle()
    {
        $this->info('Installing Bunny Package...');

        // Publish configuration
        $this->call('vendor:publish', [
            '--provider' => 'Kisalay\\Bunny\\Providers\\BunnyServiceProvider',
            '--tag' => 'bunny-config'
        ]);

        // Publish views
        $this->call('vendor:publish', [
            '--provider' => 'Kisalay\\Bunny\\Providers\\BunnyServiceProvider',
            '--tag' => 'bunny-views'
        ]);

        // Publish assets
        $this->call('vendor:publish', [
            '--provider' => 'Kisalay\\Bunny\\Providers\\BunnyServiceProvider',
            '--tag' => 'bunny-assets'
        ]);

        // Run migrations
        $this->call('migrate');

        // Create storage symlink
        $this->call('storage:link');

        $this->info('Bunny Package has been installed successfully!');
        $this->info('You can now use "php artisan bunny:create {type}" to create a new website.');
    }
} 