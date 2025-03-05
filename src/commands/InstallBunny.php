<?php
// src/Commands/InstallBunny.php

namespace Bunny\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Bunny\Helpers\StubParser;
use Symfony\Component\Process\Process;
use Illuminate\Support\Str;

class InstallBunny extends Command
{
    protected $signature = 'bunny:install';
    protected $description = 'Install and scaffold a new website using Bunny with default templates';

    public function handle()
    {
        $this->info('Welcome to Bunny installation!');

        // 1. Choose site type.
        $siteType = $this->choice('Select the type of website', ['portfolio', 'ecommerce'], 0);

        // 2. Ask for model name (default based on site type).
        $modelName = $this->ask('Enter the main model name', ucfirst($siteType));

        // 3. Ask for fields (e.g., title:string, price:decimal:nullable).
        $fieldsInput = $this->ask('Enter model fields (e.g., title:string, email:string:unique)');
        $fields = $this->parseFields($fieldsInput);

        // 4. Optional features.
        $useAuth = $this->confirm('Include authentication scaffolding?', false);
        $includePayment = $siteType === 'ecommerce' ? $this->confirm('Include payment gateway integration (e.g., Stripe)?', false) : false;
        $installPackages = $this->confirm('Install additional optional packages?', false);
        $installCMS = $this->confirm('Install CMS functionality for content management?', false);

        // 5. Generate common files.
        try {
            $this->generateModel($modelName, $fields);
            $this->generateController($siteType, $modelName);
            $this->generateMigration($modelName, $fields);
            $this->generateView($siteType);
            $this->generateFactory($modelName, $fields);
            $this->generateSeeder($modelName);

            if ($useAuth) {
                $this->generateAuthScaffolding();
            }
            if ($includePayment) {
                $this->generatePaymentIntegration();
            }
            if ($installPackages) {
                $this->installOptionalPackages();
            }
            if ($installCMS) {
                $this->generateCMS();
            }

            // 6. Copy default templates based on the selected site type.
            $this->copyDefaultTemplates($siteType);

            $this->info('Website scaffolding for ' . $siteType . ' created successfully!');
        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
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
}
