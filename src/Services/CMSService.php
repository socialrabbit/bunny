<?php

namespace Kisalay\Bunny\Services;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CMSService
{
    protected $app;
    protected $features = [];
    protected $dependencies = [];
    protected $config = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function install()
    {
        $this->publishAssets();
        $this->runMigrations();
        $this->installDependencies();
        $this->updateConfig();
        $this->createDefaultRoles();
        $this->createDefaultAdmin();
    }

    public function uninstall()
    {
        $this->uninstallDependencies();
        $this->rollbackMigrations();
        $this->removeAssets();
    }

    protected function publishAssets()
    {
        $this->publishViews();
        $this->publishConfig();
        $this->publishMigrations();
        $this->publishTranslations();
    }

    protected function publishViews()
    {
        $sourcePath = __DIR__ . "/../Resources/views/cms";
        $destinationPath = resource_path("views/vendor/bunny/cms");

        if (File::exists($sourcePath)) {
            File::copyDirectory($sourcePath, $destinationPath);
        }
    }

    protected function publishConfig()
    {
        $sourcePath = __DIR__ . "/../Config/cms.php";
        $destinationPath = config_path("bunny/cms.php");

        if (File::exists($sourcePath)) {
            File::copy($sourcePath, $destinationPath);
        }
    }

    protected function publishMigrations()
    {
        $sourcePath = __DIR__ . "/../Database/migrations/cms";
        $destinationPath = database_path("migrations/vendor/bunny/cms");

        if (File::exists($sourcePath)) {
            File::copyDirectory($sourcePath, $destinationPath);
        }
    }

    protected function publishTranslations()
    {
        $sourcePath = __DIR__ . "/../Resources/lang/cms";
        $destinationPath = resource_path("lang/vendor/bunny/cms");

        if (File::exists($sourcePath)) {
            File::copyDirectory($sourcePath, $destinationPath);
        }
    }

    protected function runMigrations()
    {
        Artisan::call('migrate', [
            '--path' => "database/migrations/vendor/bunny/cms",
        ]);
    }

    protected function rollbackMigrations()
    {
        Artisan::call('migrate:rollback', [
            '--path' => "database/migrations/vendor/bunny/cms",
        ]);
    }

    protected function installDependencies()
    {
        foreach ($this->dependencies as $dependency) {
            if (method_exists($this, "install{$dependency}")) {
                $this->{"install{$dependency}"}();
            }
        }
    }

    protected function uninstallDependencies()
    {
        foreach ($this->dependencies as $dependency) {
            if (method_exists($this, "uninstall{$dependency}")) {
                $this->{"uninstall{$dependency}"}();
            }
        }
    }

    protected function updateConfig()
    {
        foreach ($this->config as $key => $value) {
            Config::set("bunny.cms.{$key}", $value);
        }
    }

    protected function createDefaultRoles()
    {
        if (!Schema::hasTable('roles')) {
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
    }

    protected function createDefaultAdmin()
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        $admin = \App\Models\User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $admin->assignRole('admin');
    }

    protected function removeAssets()
    {
        $paths = [
            resource_path("views/vendor/bunny/cms"),
            config_path("bunny/cms.php"),
            database_path("migrations/vendor/bunny/cms"),
            resource_path("lang/vendor/bunny/cms"),
        ];

        foreach ($paths as $path) {
            if (File::exists($path)) {
                File::deleteDirectory($path);
            }
        }
    }

    public function addFeature($feature)
    {
        $this->features[] = $feature;
    }

    public function addDependency($dependency)
    {
        $this->dependencies[] = $dependency;
    }

    public function setConfig($key, $value)
    {
        $this->config[$key] = $value;
    }

    public function getFeatures()
    {
        return $this->features;
    }

    public function getDependencies()
    {
        return $this->dependencies;
    }
} 