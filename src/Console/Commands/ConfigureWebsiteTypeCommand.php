<?php

namespace Kisalay\Bunny\Console\Commands;

use Illuminate\Console\Command;
use Kisalay\Bunny\WebsiteTypes\WebsiteTypeManager;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;

class ConfigureWebsiteTypeCommand extends Command
{
    protected $signature = 'bunny:config {type} {--force}';
    protected $description = 'Configure a specific website type';

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
        $this->info("Configuring {$typeInfo['type']} website type...");

        try {
            $this->configureSettings($type);
            $this->configureFeatures($type, $typeInfo);
            $this->configureDependencies($type, $typeInfo);
            $this->configureAssets($type);

            $this->info("Successfully configured {$typeInfo['type']} website type!");
            $this->displayNextSteps($type);
        } catch (\Exception $e) {
            $this->error("Error configuring {$typeInfo['type']} website type: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }

    protected function configureSettings(string $type)
    {
        $configPath = config_path("bunny/{$type}.php");
        
        if (!File::exists($configPath)) {
            $this->error("Configuration file not found: {$configPath}");
            return;
        }

        $config = require $configPath;
        $defaults = Config::get('bunny.website-types.defaults.settings');

        $this->info("\nConfiguring settings:");
        foreach ($defaults as $key => $value) {
            $current = $config['settings'][$key] ?? $value;
            $newValue = $this->ask("Enable {$key}?", $current ? 'yes' : 'no') === 'yes';
            $config['settings'][$key] = $newValue;
        }

        File::put($configPath, '<?php return ' . var_export($config, true) . ';');
    }

    protected function configureFeatures(string $type, array $typeInfo)
    {
        $this->info("\nConfiguring features:");
        foreach ($typeInfo['features'] as $feature) {
            $enabled = $this->confirm("Enable {$feature}?", true);
            // Update feature configuration
        }
    }

    protected function configureDependencies(string $type, array $typeInfo)
    {
        $this->info("\nConfiguring dependencies:");
        foreach ($typeInfo['dependencies'] as $dependency) {
            $enabled = $this->confirm("Enable {$dependency}?", true);
            // Update dependency configuration
        }
    }

    protected function configureAssets(string $type)
    {
        $this->info("\nConfiguring assets:");
        
        $publishOptions = Config::get('bunny.website-types.publish');
        foreach ($publishOptions as $option => $default) {
            $publish = $this->confirm("Publish {$option}?", $default);
            // Update asset publishing configuration
        }
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

    protected function displayNextSteps(string $type)
    {
        $this->line("\nNext steps:");
        $this->line("1. Clear configuration cache:");
        $this->line("   php artisan config:clear");

        $this->line("\n2. Restart your development server:");
        $this->line("   php artisan serve");

        $this->line("\n3. Visit your website to verify the configuration:");
        $this->line("   http://localhost:8000");

        $this->line("\nNote: You can modify the configuration file at:");
        $this->line("   config/bunny/{$type}.php");
    }
} 