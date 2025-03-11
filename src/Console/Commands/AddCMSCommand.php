<?php

namespace Kisalay\Bunny\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddCMSCommand extends Command
{
    protected $signature = 'bunny:add-cms {--force : Force the operation to run when in production}';
    protected $description = 'Add CMS capabilities to your website';

    public function handle()
    {
        if (!$this->confirm('This will add CMS capabilities to your website. Do you want to continue?')) {
            return;
        }

        $this->info('Adding CMS capabilities...');

        // Publish CMS assets
        $this->publishAssets();

        // Run migrations
        $this->runMigrations();

        // Update configuration
        $this->updateConfig();

        // Create default roles and admin
        $this->createDefaultRoles();
        $this->createDefaultAdmin();

        $this->info('CMS capabilities have been added successfully!');
        $this->info('You can now access the CMS at: ' . config('bunny.cms.route_prefix', 'admin'));
        $this->info('Default admin credentials:');
        $this->info('Email: admin@example.com');
        $this->info('Password: password');
    }

    protected function publishAssets()
    {
        $this->info('Publishing CMS assets...');

        // Publish views
        $this->publishViews();

        // Publish config
        $this->publishConfig();

        // Publish migrations
        $this->publishMigrations();

        // Publish translations
        $this->publishTranslations();
    }

    protected function publishViews()
    {
        $sourcePath = __DIR__ . "/../../Resources/views/cms";
        $destinationPath = resource_path("views/vendor/bunny/cms");

        if (File::exists($sourcePath)) {
            File::copyDirectory($sourcePath, $destinationPath);
            $this->info('Published CMS views');
        }
    }

    protected function publishConfig()
    {
        $sourcePath = __DIR__ . "/../../Config/cms.php";
        $destinationPath = config_path("bunny/cms.php");

        if (File::exists($sourcePath)) {
            File::copy($sourcePath, $destinationPath);
            $this->info('Published CMS config');
        }
    }

    protected function publishMigrations()
    {
        $sourcePath = __DIR__ . "/../../Database/migrations/cms";
        $destinationPath = database_path("migrations/vendor/bunny/cms");

        if (File::exists($sourcePath)) {
            File::copyDirectory($sourcePath, $destinationPath);
            $this->info('Published CMS migrations');
        }
    }

    protected function publishTranslations()
    {
        $sourcePath = __DIR__ . "/../../Resources/lang/cms";
        $destinationPath = resource_path("lang/vendor/bunny/cms");

        if (File::exists($sourcePath)) {
            File::copyDirectory($sourcePath, $destinationPath);
            $this->info('Published CMS translations');
        }
    }

    protected function runMigrations()
    {
        $this->info('Running CMS migrations...');
        Artisan::call('migrate', [
            '--path' => "database/migrations/vendor/bunny/cms",
            '--force' => $this->option('force'),
        ]);
        $this->info('CMS migrations completed');
    }

    protected function updateConfig()
    {
        $this->info('Updating CMS configuration...');
        Config::set('bunny.cms.enabled', true);
        Config::set('bunny.cms.route_prefix', 'admin');
        $this->info('CMS configuration updated');
    }

    protected function createDefaultRoles()
    {
        $this->info('Creating default roles...');
        if (!Schema::hasTable('roles')) {
            $this->error('Roles table not found. Please run migrations first.');
            return;
        }

        DB::table('roles')->insert([
            [
                'name' => 'admin',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'editor',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'author',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        $this->info('Default roles created');
    }

    protected function createDefaultAdmin()
    {
        $this->info('Creating default admin user...');
        if (!Schema::hasTable('users')) {
            $this->error('Users table not found. Please run migrations first.');
            return;
        }

        $admin = \App\Models\User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $admin->assignRole('admin');
        $this->info('Default admin user created');
    }
} 