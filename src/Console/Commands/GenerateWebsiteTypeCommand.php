<?php

namespace Kisalay\Bunny\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateWebsiteTypeCommand extends Command
{
    protected $signature = 'bunny:make-type {name} {--force}';
    protected $description = 'Generate a new website type';

    public function handle()
    {
        $name = $this->argument('name');
        $force = $this->option('force');

        $type = Str::kebab($name);
        $className = Str::studly($name) . 'Website';

        $this->info("Generating website type: {$className}");
        $this->line('');

        // Generate the website type class
        $this->generateWebsiteTypeClass($className, $type, $force);

        // Generate the configuration file
        $this->generateConfigFile($type, $force);

        // Generate the migration files
        $this->generateMigrationFiles($type, $force);

        // Generate the view files
        $this->generateViewFiles($type, $force);

        // Generate the translation files
        $this->generateTranslationFiles($type, $force);

        // Update the service provider
        $this->updateServiceProvider($className, $type);

        $this->info("\nWebsite type generated successfully!");
        $this->displayNextSteps($type);
    }

    protected function generateWebsiteTypeClass(string $className, string $type, bool $force)
    {
        $path = app_path("WebsiteTypes/{$className}.php");
        
        if (File::exists($path) && !$force) {
            if (!$this->confirm("The file {$path} already exists. Do you want to overwrite it?")) {
                return;
            }
        }

        $stub = $this->getStub('website-type');
        $content = str_replace(
            ['{{ class }}', '{{ type }}', '{{ features }}', '{{ dependencies }}'],
            [$className, $type, $this->getDefaultFeatures(), $this->getDefaultDependencies()],
            $stub
        );

        File::put($path, $content);
        $this->info("Created {$className}.php");
    }

    protected function generateConfigFile(string $type, bool $force)
    {
        $path = config_path("bunny/{$type}.php");
        
        if (File::exists($path) && !$force) {
            if (!$this->confirm("The file {$path} already exists. Do you want to overwrite it?")) {
                return;
            }
        }

        $stub = $this->getStub('config');
        $content = str_replace(
            ['{{ type }}', '{{ name }}', '{{ description }}', '{{ features }}', '{{ dependencies }}'],
            [
                $type,
                Str::title(str_replace('-', ' ', $type)),
                "Configuration for {$type} website type",
                $this->getDefaultFeatures(),
                $this->getDefaultDependencies(),
            ],
            $stub
        );

        File::put($path, $content);
        $this->info("Created {$type}.php configuration file");
    }

    protected function generateMigrationFiles(string $type, bool $force)
    {
        $path = database_path("migrations/vendor/bunny/{$type}");
        
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        // Generate base migration
        $this->generateMigration($type, 'create_base_tables', $force);
        $this->generateMigration($type, 'create_settings_table', $force);
    }

    protected function generateMigration(string $type, string $name, bool $force)
    {
        $path = database_path("migrations/vendor/bunny/{$type}/{$name}.php");
        
        if (File::exists($path) && !$force) {
            if (!$this->confirm("The file {$path} already exists. Do you want to overwrite it?")) {
                return;
            }
        }

        $stub = $this->getStub('migration');
        $content = str_replace(
            ['{{ class }}', '{{ table }}'],
            [Str::studly($name), $this->getTableName($type, $name)],
            $stub
        );

        File::put($path, $content);
        $this->info("Created {$name}.php migration");
    }

    protected function generateViewFiles(string $type, bool $force)
    {
        $path = resource_path("views/vendor/bunny/{$type}");
        
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        // Generate base views
        $this->generateView($type, 'index', $force);
        $this->generateView($type, 'show', $force);
        $this->generateView($type, 'create', $force);
        $this->generateView($type, 'edit', $force);
    }

    protected function generateView(string $type, string $name, bool $force)
    {
        $path = resource_path("views/vendor/bunny/{$type}/{$name}.blade.php");
        
        if (File::exists($path) && !$force) {
            if (!$this->confirm("The file {$path} already exists. Do you want to overwrite it?")) {
                return;
            }
        }

        $stub = $this->getStub("view.{$name}");
        $content = str_replace(
            ['{{ type }}', '{{ name }}'],
            [$type, Str::title(str_replace('-', ' ', $type))],
            $stub
        );

        File::put($path, $content);
        $this->info("Created {$name}.blade.php view");
    }

    protected function generateTranslationFiles(string $type, bool $force)
    {
        $path = resource_path("lang/vendor/bunny/{$type}");
        
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        // Generate English translations
        $this->generateTranslation($type, 'en', $force);
    }

    protected function generateTranslation(string $type, string $locale, bool $force)
    {
        $path = resource_path("lang/vendor/bunny/{$type}/{$locale}.php");
        
        if (File::exists($path) && !$force) {
            if (!$this->confirm("The file {$path} already exists. Do you want to overwrite it?")) {
                return;
            }
        }

        $stub = $this->getStub('translation');
        $content = str_replace(
            ['{{ type }}', '{{ name }}'],
            [$type, Str::title(str_replace('-', ' ', $type))],
            $stub
        );

        File::put($path, $content);
        $this->info("Created {$locale}.php translation file");
    }

    protected function updateServiceProvider(string $className, string $type)
    {
        $path = app_path('Providers/WebsiteTypeServiceProvider.php');
        
        if (!File::exists($path)) {
            $this->error('Service provider not found. Please create it first.');
            return;
        }

        $content = File::get($path);
        $content = str_replace(
            'protected $websiteTypes = [',
            "protected \$websiteTypes = [\n        '{$type}' => {$className}::class,",
            $content
        );

        File::put($path, $content);
        $this->info('Updated service provider');
    }

    protected function getStub(string $name): string
    {
        $path = __DIR__ . "/stubs/{$name}.stub";
        
        if (!File::exists($path)) {
            throw new \Exception("Stub file not found: {$path}");
        }

        return File::get($path);
    }

    protected function getTableName(string $type, string $name): string
    {
        return "{$type}_{$name}";
    }

    protected function getDefaultFeatures(): string
    {
        return <<<'EOT'
    public function getFeatures()
    {
        return [
            'feature_1',
            'feature_2',
            'feature_3',
        ];
    }
EOT;
    }

    protected function getDefaultDependencies(): string
    {
        return <<<'EOT'
    public function getDependencies()
    {
        return [
            'auth',
            'media',
            'notifications',
        ];
    }
EOT;
    }

    protected function displayNextSteps(string $type)
    {
        $this->line("\nNext steps:");
        $this->line("1. Review and customize the generated files:");
        $this->line("   - app/WebsiteTypes/{$type}.php");
        $this->line("   - config/bunny/{$type}.php");
        $this->line("   - database/migrations/vendor/bunny/{$type}");
        $this->line("   - resources/views/vendor/bunny/{$type}");
        $this->line("   - resources/lang/vendor/bunny/{$type}");

        $this->line("\n2. Run the migrations:");
        $this->line("   php artisan migrate");

        $this->line("\n3. Publish the assets:");
        $this->line("   php artisan vendor:publish --tag=bunny-{$type}-assets");

        $this->line("\n4. Clear the cache:");
        $this->line("   php artisan config:clear");
        $this->line("   php artisan view:clear");
        $this->line("   php artisan cache:clear");

        $this->line("\n5. Test your new website type:");
        $this->line("   php artisan bunny:install-type {$type}");
    }
} 