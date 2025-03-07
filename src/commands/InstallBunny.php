<?php
// src/Commands/InstallBunny.php

namespace Bunny\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Bunny\Helpers\StubParser;
use Symfony\Component\Process\Process;
use Illuminate\Support\Str;
use Bunny\Services\GitHubService;

class InstallBunny extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bunny:install {--force} {--github-token=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install and configure the Bunny package';

    /**
     * The GitHub service instance.
     *
     * @var GitHubService
     */
    protected $github;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(GitHubService $github)
    {
        parent::__construct();
        $this->github = $github;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Welcome to Bunny Installation! 🐰');
        $this->info('This will help you set up your new web application.');

        // Check if already installed
        if (!$this->option('force') && $this->isInstalled()) {
            $this->error('Bunny is already installed! Use --force to reinstall.');
            return 1;
        }

        // Get repository stats
        $stats = $this->github->getStats();
        $this->info("\nRepository Statistics:");
        $this->line("⭐ Stars: {$stats['stars']}");
        $this->line("🔱 Forks: {$stats['forks']}");
        $this->line("👀 Watchers: {$stats['watchers']}\n");

        // Ask about starring the repository
        if ($this->confirm('Would you like to star the Bunny repository on GitHub?')) {
            $token = $this->option('github-token') ?? $this->ask('Please enter your GitHub token (or press enter to skip)');
            
            if ($token) {
                if ($this->github->isStarred($token)) {
                    $this->info('You have already starred the repository!');
                } else {
                    if ($this->github->star($token)) {
                        $this->info('Thank you for starring the repository!');
                    } else {
                        $this->warn('Failed to star the repository. Please try again later.');
                    }
                }
            } else {
                $this->line('Skipping GitHub star. You can star the repository later at: ' . $this->github->getRepositoryUrl());
            }
        }

        // Continue with installation
        $this->publishConfig();
        $this->publishAssets();
        $this->generateComponents();

        $this->info("\nBunny has been installed successfully! 🎉");
        $this->info('Visit our documentation at: https://github.com/socialrabbit/bunny/wiki');

        return 0;
    }

    /**
     * Check if Bunny is already installed.
     *
     * @return bool
     */
    protected function isInstalled(): bool
    {
        return File::exists(config_path('bunny.php'));
    }

    /**
     * Publish the configuration file.
     *
     * @return void
     */
    protected function publishConfig()
    {
        $this->call('vendor:publish', [
            '--provider' => 'Bunny\BunnyServiceProvider',
            '--tag' => 'bunny-config',
            '--force' => $this->option('force'),
        ]);
    }

    /**
     * Publish the assets.
     *
     * @return void
     */
    protected function publishAssets()
    {
        $this->call('vendor:publish', [
            '--provider' => 'Bunny\BunnyServiceProvider',
            '--tag' => 'bunny-assets',
            '--force' => $this->option('force'),
        ]);
    }

    /**
     * Generate the components.
     *
     * @return void
     */
    protected function generateComponents()
    {
        // Get user preferences
        $framework = $this->choice(
            'Which frontend framework would you like to use?',
            ['vue', 'react', 'alpine', 'none'],
            'vue'
        );

        $uiLibrary = $this->choice(
            'Which UI library would you like to use?',
            ['tailwind', 'bootstrap', 'none'],
            'tailwind'
        );

        $siteType = $this->choice(
            'Which type of website would you like to create?',
            ['portfolio', 'ecommerce', 'cms'],
            'portfolio'
        );

        $apiType = $this->choice(
            'Which API type would you like to use?',
            ['rest', 'graphql', 'none'],
            'rest'
        );

        // Generate components based on choices
        $this->call('bunny:frontend', [
            '--framework' => $framework,
            '--ui-library' => $uiLibrary,
        ]);

        $this->call('bunny:backend', [
            '--site-type' => $siteType,
        ]);

        if ($apiType !== 'none') {
            $this->call('bunny:api', [
                '--type' => $apiType,
            ]);
        }
    }

    protected function parseFields($input)
    {
        $fields = [];
        foreach (explode(',', $input) as $fieldDef) {
            $parts = explode(':', trim($fieldDef));
            if (count($parts) >= 2) {
                $fields[] = [
                    'name'        => trim($parts[0]),
                    'type'        => trim($parts[1]),
                    'constraints' => array_slice($parts, 2)
                ];
            }
        }
        return $fields;
    }

    protected function generateModel($modelName, $fields)
    {
        $stub = File::get(__DIR__ . '/../stubs/common/model.stub');
        $fillable = implode(", ", array_map(function ($f) {
            return "'{$f['name']}'";
        }, $fields));
        $relationships = "// Define relationships here if needed";

        $content = StubParser::parse($stub, [
            'modelName'         => $modelName,
            'fillableFields'    => $fillable,
            'optionalRelations' => $relationships,
        ]);
        $path = app_path("Models/{$modelName}.php");
        File::put($path, $content);
        $this->info("Model {$modelName} created at {$path}.");
    }

    protected function generateController($siteType, $modelName)
    {
        $stub = File::get(__DIR__ . '/../stubs/common/controller.stub');
        $controllerName = "{$modelName}Controller";
        $content = StubParser::parse($stub, [
            'controllerName' => $controllerName,
            'modelName'      => $modelName,
            'viewName'       => strtolower($siteType),
        ]);
        $path = app_path("Http/Controllers/{$controllerName}.php");
        File::put($path, $content);
        $this->info("Controller {$controllerName} created at {$path}.");
    }

    protected function generateMigration($modelName, $fields)
    {
        $stub = File::get(__DIR__ . '/../stubs/common/migration.stub');
        $tableName = strtolower(Str::plural($modelName));
        $migrationName = date('Y_m_d_His') . "_create_{$tableName}_table.php";

        $migrationFields = "";
        foreach ($fields as $field) {
            $line = "\$table->{$field['type']}('{$field['name']}')";
            if (in_array('unique', $field['constraints'])) {
                $line .= "->unique()";
            }
            if (in_array('nullable', $field['constraints'])) {
                $line .= "->nullable()";
            }
            $line .= ";";
            $migrationFields .= "            " . $line . "\n";
        }

        $content = StubParser::parse($stub, [
            'tableName'       => $tableName,
            'migrationFields' => $migrationFields,
        ]);
        $path = database_path("migrations/{$migrationName}.php");
        File::put($path, $content);
        $this->info("Migration for table {$tableName} created at {$path}.");
    }

    protected function generateView($siteType)
    {
        $stub = File::get(__DIR__ . '/../stubs/common/view.stub');
        $content = StubParser::parse($stub, [
            'viewName' => $siteType,
        ]);
        $path = resource_path("views/{$siteType}.blade.php");
        File::put($path, $content);
        $this->info("View {$siteType}.blade.php created at {$path}.");
    }

    protected function generateFactory($modelName, $fields)
    {
        $stub = File::get(__DIR__ . '/../stubs/common/factory.stub');
        $factoryFields = "";
        foreach ($fields as $field) {
            $factoryFields .= "            '{$field['name']}' => \$this->faker->word,\n";
        }
        $content = StubParser::parse($stub, [
            'modelName'     => $modelName,
            'factoryFields' => $factoryFields,
        ]);
        $path = database_path("factories/{$modelName}Factory.php");
        File::put($path, $content);
        $this->info("Factory for {$modelName} created at {$path}.");
    }

    protected function generateSeeder($modelName)
    {
        $stub = File::get(__DIR__ . '/../stubs/common/seeder.stub');
        $content = StubParser::parse($stub, [
            'modelName' => $modelName,
        ]);
        $path = database_path("seeders/{$modelName}Seeder.php");
        File::put($path, $content);
        $this->info("Seeder for {$modelName} created at {$path}.");
    }

    protected function generateAuthScaffolding()
    {
        $this->info("Authentication scaffolding has been generated.");
    }

    protected function generatePaymentIntegration()
    {
        $this->info("Payment gateway integration scaffolding has been generated.");
    }

    protected function installOptionalPackages()
    {
        $optional = config('bunny.optional_packages', []);
        if (empty($optional)) {
            $this->info('No optional packages defined.');
            return;
        }
        $packageKeys = array_keys($optional);
        $selected = $this->choice(
            'Select optional packages to install (separate multiple choices with commas)',
            $packageKeys,
            null,
            null,
            true
        );
        foreach ($selected as $key) {
            $packageName = $optional[$key];
            $this->info("Installing {$packageName} ...");
            $process = new Process(['composer', 'require', $packageName]);
            $process->setTimeout(300);
            $process->run(function ($type, $buffer) {
                echo $buffer;
            });
            if (!$process->isSuccessful()) {
                $this->error("Failed to install {$packageName}: " . $process->getErrorOutput());
            } else {
                $this->info("Successfully installed {$packageName}");
            }
        }
    }

    // CMS Generation
    protected function generateCMS()
    {
        $this->info("Installing CMS functionality...");

        // Generate CMS migration
        $migrationName = date('Y_m_d_His') . "_create_pages_table.php";
        $cmsMigrationPath = database_path("migrations/{$migrationName}");

        $cmsMigrationContent = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pages', function (Blueprint \$table) {
            \$table->id();
            \$table->string('slug')->unique();
            \$table->string('title');
            \$table->text('content')->nullable();
            \$table->string('meta_title')->nullable();
            \$table->text('meta_description')->nullable();
            \$table->boolean('is_published')->default(false);
            \$table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pages');
    }
};
EOT;

        File::put($cmsMigrationPath, $cmsMigrationContent);
        $this->info("Created CMS pages migration at {$cmsMigrationPath}");

        // Generate CMS Model
        $modelContent = <<<EOT
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Page extends Model
{
    use HasFactory;

    protected \$fillable = [
        'slug',
        'title',
        'content',
        'meta_title',
        'meta_description',
        'is_published'
    ];

    protected \$casts = [
        'is_published' => 'boolean',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
EOT;

        $modelPath = app_path('Models/Page.php');
        File::put($modelPath, $modelContent);
        $this->info("Created CMS Page model at {$modelPath}");

        // Generate CMS Controller
        $controllerContent = <<<EOT
<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        \$pages = Page::paginate(10);
        return view('cms.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('cms.pages.create');
    }

    public function store(Request \$request)
    {
        \$validated = \$request->validate([
            'title' => 'required|max:255',
            'slug' => 'required|unique:pages|max:255',
            'content' => 'nullable',
            'meta_title' => 'nullable|max:255',
            'meta_description' => 'nullable',
            'is_published' => 'boolean'
        ]);

        Page::create(\$validated);
        return redirect()->route('pages.index')->with('success', 'Page created successfully.');
    }

    public function edit(Page \$page)
    {
        return view('cms.pages.edit', compact('page'));
    }

    public function update(Request \$request, Page \$page)
    {
        \$validated = \$request->validate([
            'title' => 'required|max:255',
            'slug' => 'required|unique:pages,slug,'.\$page->id.'|max:255',
            'content' => 'nullable',
            'meta_title' => 'nullable|max:255',
            'meta_description' => 'nullable',
            'is_published' => 'boolean'
        ]);

        \$page->update(\$validated);
        return redirect()->route('pages.index')->with('success', 'Page updated successfully.');
    }

    public function destroy(Page \$page)
    {
        \$page->delete();
        return redirect()->route('pages.index')->with('success', 'Page deleted successfully.');
    }
}
EOT;

        $controllerPath = app_path('Http/Controllers/PageController.php');
        File::put($controllerPath, $controllerContent);
        $this->info("Created CMS PageController at {$controllerPath}");

        $this->info('CMS functionality has been installed successfully!');
    }

    // Copy default site templates (Portfolio or Ecommerce)
    protected function copyDefaultTemplates($siteType)
    {
        $sourcePath = __DIR__ . "/../stubs/{$siteType}/";
        $destinationPath = resource_path("views/{$siteType}");
        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }
        // Copy each file from the default templates folder.
        foreach (File::files($sourcePath) as $file) {
            $dest = $destinationPath . '/' . $file->getFilename();
            File::copy($file->getPathname(), $dest);
            $this->info("Default template {$file->getFilename()} copied to {$dest}");
        }
    }

    protected function generateFrontendComponents($siteType, $modelName, $framework, $uiLibrary)
    {
        $this->info("Generating frontend components for {$framework}...");

        // Create frontend directory structure
        $frontendPath = resource_path("js/components/{$siteType}");
        if (!File::isDirectory($frontendPath)) {
            File::makeDirectory($frontendPath, 0755, true);
        }

        // Generate component based on framework
        switch ($framework) {
            case 'vue':
                $this->generateVueComponents($siteType, $modelName, $uiLibrary);
                break;
            case 'react':
                $this->generateReactComponents($siteType, $modelName, $uiLibrary);
                break;
            case 'alpine':
                $this->generateAlpineComponents($siteType, $modelName, $uiLibrary);
                break;
        }

        // Install necessary npm packages
        $this->installFrontendDependencies($framework, $uiLibrary);
    }

    protected function generateVueComponents($siteType, $modelName, $uiLibrary)
    {
        // Generate Vue component stub
        $componentStub = File::get(__DIR__ . "/../stubs/frontend/vue/{$siteType}.vue.stub");
        $componentContent = StubParser::parse($componentStub, [
            'modelName' => $modelName,
            'uiLibrary' => $uiLibrary,
        ]);

        $componentPath = resource_path("js/components/{$siteType}/{$modelName}List.vue");
        File::put($componentPath, $componentContent);
        $this->info("Created Vue component at {$componentPath}");
    }

    protected function generateReactComponents($siteType, $modelName, $uiLibrary)
    {
        // Generate React component stub
        $componentStub = File::get(__DIR__ . "/../stubs/frontend/react/{$siteType}.jsx.stub");
        $componentContent = StubParser::parse($componentStub, [
            'modelName' => $modelName,
            'uiLibrary' => $uiLibrary,
        ]);

        $componentPath = resource_path("js/components/{$siteType}/{$modelName}List.jsx");
        File::put($componentPath, $componentContent);
        $this->info("Created React component at {$componentPath}");
    }

    protected function generateAlpineComponents($siteType, $modelName, $uiLibrary)
    {
        // Generate Alpine.js component stub
        $componentStub = File::get(__DIR__ . "/../stubs/frontend/alpine/{$siteType}.js.stub");
        $componentContent = StubParser::parse($componentStub, [
            'modelName' => $modelName,
            'uiLibrary' => $uiLibrary,
        ]);

        $componentPath = resource_path("js/components/{$siteType}/{$modelName}List.js");
        File::put($componentPath, $componentContent);
        $this->info("Created Alpine.js component at {$componentPath}");
    }

    protected function installFrontendDependencies($framework, $uiLibrary)
    {
        $dependencies = [];
        $devDependencies = [];

        // Add framework-specific dependencies
        switch ($framework) {
            case 'vue':
                $dependencies[] = 'vue@^3.3.0';
                $dependencies[] = '@vitejs/plugin-vue';
                break;
            case 'react':
                $dependencies[] = 'react@^18.2.0';
                $dependencies[] = 'react-dom@^18.2.0';
                $dependencies[] = '@vitejs/plugin-react';
                break;
            case 'alpine':
                $dependencies[] = 'alpinejs@^3.13.0';
                break;
        }

        // Add UI library dependencies
        switch ($uiLibrary) {
            case 'tailwind':
                $devDependencies[] = 'tailwindcss@^3.3.0';
                $devDependencies[] = 'postcss@^8.4.0';
                $devDependencies[] = 'autoprefixer@^10.4.0';
                break;
            case 'bootstrap':
                $dependencies[] = 'bootstrap@^5.3.0';
                break;
        }

        // Install dependencies using npm
        if (!empty($dependencies)) {
            $this->info('Installing frontend dependencies...');
            $process = new Process(['npm', 'install', ...$dependencies]);
            $process->run();
        }

        if (!empty($devDependencies)) {
            $this->info('Installing frontend dev dependencies...');
            $process = new Process(['npm', 'install', '-D', ...$devDependencies]);
            $process->run();
        }
    }
}
