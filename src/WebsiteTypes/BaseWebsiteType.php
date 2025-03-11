<?php

namespace Kisalay\Bunny\WebsiteTypes;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;

abstract class BaseWebsiteType
{
    protected $app;
    protected $type;
    protected $features = [];
    protected $dependencies = [];
    protected $config = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->type = strtolower(class_basename($this));
    }

    abstract public function install();
    abstract public function uninstall();
    abstract public function getFeatures();
    abstract public function getDependencies();

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

    protected function publishAssets()
    {
        $paths = [
            'views' => resource_path("views/vendor/bunny/{$this->type}"),
            'config' => config_path("bunny/{$this->type}.php"),
            'migrations' => database_path("migrations/vendor/bunny/{$this->type}"),
            'lang' => resource_path("lang/vendor/bunny/{$this->type}"),
        ];

        foreach ($paths as $type => $path) {
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }
        }

        // Publish assets using Laravel's asset publishing
        Artisan::call('vendor:publish', [
            '--tag' => "bunny-{$this->type}-assets",
            '--force' => true,
        ]);
    }

    protected function runMigrations()
    {
        $migrationPath = database_path("migrations/vendor/bunny/{$this->type}");
        
        if (File::exists($migrationPath)) {
            Artisan::call('migrate', [
                '--path' => "vendor/bunny/{$this->type}/migrations",
                '--force' => true,
            ]);
        }
    }

    protected function rollbackMigrations()
    {
        $migrationPath = database_path("migrations/vendor/bunny/{$this->type}");
        
        if (File::exists($migrationPath)) {
            Artisan::call('migrate:rollback', [
                '--path' => "vendor/bunny/{$this->type}/migrations",
                '--force' => true,
            ]);
        }
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
        $configPath = config_path("bunny/{$this->type}.php");
        
        if (File::exists($configPath)) {
            $config = require $configPath;
            
            // Update config with features and dependencies
            $config['features'] = $this->features;
            $config['dependencies'] = $this->dependencies;
            
            File::put($configPath, '<?php return ' . var_export($config, true) . ';');
        }
    }

    protected function createDefaultData()
    {
        // This method should be implemented by child classes
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

    protected function createModel($model, $data)
    {
        try {
            return $model::create($data);
        } catch (\Exception $e) {
            // Log error and continue
            \Log::error("Error creating {$model}: " . $e->getMessage());
            return null;
        }
    }

    protected function createManyModels($model, $dataArray)
    {
        $created = [];
        foreach ($dataArray as $data) {
            if ($item = $this->createModel($model, $data)) {
                $created[] = $item;
            }
        }
        return $created;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getFeaturesList()
    {
        return $this->features;
    }

    public function getDependenciesList()
    {
        return $this->dependencies;
    }
} 