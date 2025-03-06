<?php
// src/BackendManager.php

namespace Bunny;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BackendManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Create a new backend manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Generate backend components.
     *
     * @param  string  $siteType
     * @param  string  $modelName
     * @param  array  $fields
     * @return void
     */
    public function generateComponents($siteType, $modelName, array $fields)
    {
        $this->generateModel($modelName, $fields);
        $this->generateController($siteType, $modelName);
        $this->generateMigration($modelName, $fields);
        $this->generateRoutes($siteType, $modelName);
        $this->generateViews($siteType, $modelName, $fields);
    }

    /**
     * Generate the model.
     *
     * @param  string  $modelName
     * @param  array  $fields
     * @return void
     */
    protected function generateModel($modelName, array $fields)
    {
        $stub = File::get(__DIR__ . '/stubs/backend/model.stub');
        $content = $this->parseStub($stub, [
            'modelName' => $modelName,
            'fillable' => $this->generateFillable($fields),
            'casts' => $this->generateCasts($fields),
        ]);

        $path = app_path("Models/{$modelName}.php");
        File::put($path, $content);
    }

    /**
     * Generate the controller.
     *
     * @param  string  $siteType
     * @param  string  $modelName
     * @return void
     */
    protected function generateController($siteType, $modelName)
    {
        $stub = File::get(__DIR__ . "/stubs/backend/controllers/{$siteType}.stub");
        $content = $this->parseStub($stub, [
            'modelName' => $modelName,
            'modelVariable' => Str::camel($modelName),
        ]);

        $path = app_path("Http/Controllers/{$modelName}Controller.php");
        File::put($path, $content);
    }

    /**
     * Generate the migration.
     *
     * @param  string  $modelName
     * @param  array  $fields
     * @return void
     */
    protected function generateMigration($modelName, array $fields)
    {
        $stub = File::get(__DIR__ . '/stubs/backend/migration.stub');
        $content = $this->parseStub($stub, [
            'tableName' => Str::plural(Str::snake($modelName)),
            'columns' => $this->generateColumns($fields),
        ]);

        $timestamp = date('Y_m_d_His');
        $path = database_path("migrations/{$timestamp}_create_" . Str::plural(Str::snake($modelName)) . "_table.php");
        File::put($path, $content);
    }

    /**
     * Generate the routes.
     *
     * @param  string  $siteType
     * @param  string  $modelName
     * @return void
     */
    protected function generateRoutes($siteType, $modelName)
    {
        $stub = File::get(__DIR__ . "/stubs/backend/routes/{$siteType}.stub");
        $content = $this->parseStub($stub, [
            'modelName' => $modelName,
            'routeName' => Str::plural(Str::kebab($modelName)),
        ]);

        $path = routes_path("web.php");
        File::append($path, "\n" . $content);
    }

    /**
     * Generate the views.
     *
     * @param  string  $siteType
     * @param  string  $modelName
     * @param  array  $fields
     * @return void
     */
    protected function generateViews($siteType, $modelName, array $fields)
    {
        $views = ['index', 'create', 'edit', 'show'];
        foreach ($views as $view) {
            $stub = File::get(__DIR__ . "/stubs/backend/views/{$siteType}/{$view}.blade.stub");
            $content = $this->parseStub($stub, [
                'modelName' => $modelName,
                'modelVariable' => Str::camel($modelName),
                'fields' => $this->generateFormFields($fields),
            ]);

            $path = resource_path("views/{$siteType}/{$view}.blade.php");
            File::put($path, $content);
        }
    }

    /**
     * Generate fillable array.
     *
     * @param  array  $fields
     * @return string
     */
    protected function generateFillable(array $fields)
    {
        $fillable = array_map(function ($field) {
            return "'{$field['name']}'";
        }, $fields);

        return "[\n            " . implode(",\n            ", $fillable) . "\n        ]";
    }

    /**
     * Generate casts array.
     *
     * @param  array  $fields
     * @return string
     */
    protected function generateCasts(array $fields)
    {
        $casts = [];
        foreach ($fields as $field) {
            if (isset($field['type']) && in_array($field['type'], ['boolean', 'integer', 'float', 'array', 'json', 'date', 'datetime'])) {
                $casts[] = "'{$field['name']}' => '{$field['type']}'";
            }
        }

        return empty($casts) ? '[]' : "[\n            " . implode(",\n            ", $casts) . "\n        ]";
    }

    /**
     * Generate migration columns.
     *
     * @param  array  $fields
     * @return string
     */
    protected function generateColumns(array $fields)
    {
        $columns = [];
        foreach ($fields as $field) {
            $type = $field['type'] ?? 'string';
            $nullable = isset($field['nullable']) && $field['nullable'] ? '->nullable()' : '';
            $default = isset($field['default']) ? "->default('{$field['default']}')" : '';
            
            $columns[] = "\$table->{$type}('{$field['name']}'){$nullable}{$default};";
        }

        return implode("\n            ", $columns);
    }

    /**
     * Generate form fields.
     *
     * @param  array  $fields
     * @return string
     */
    protected function generateFormFields(array $fields)
    {
        $formFields = [];
        foreach ($fields as $field) {
            $type = $field['type'] ?? 'text';
            $label = ucfirst(str_replace('_', ' ', $field['name']));
            $required = isset($field['nullable']) && !$field['nullable'] ? 'required' : '';
            
            $formFields[] = "<div class=\"mb-3\">
                <label for=\"{$field['name']}\" class=\"form-label\">{$label}</label>
                <input type=\"{$type}\" class=\"form-control\" id=\"{$field['name']}\" name=\"{$field['name']}\" {$required}>
            </div>";
        }

        return implode("\n", $formFields);
    }

    /**
     * Parse the stub file.
     *
     * @param  string  $stub
     * @param  array  $replacements
     * @return string
     */
    protected function parseStub($stub, array $replacements)
    {
        foreach ($replacements as $key => $value) {
            $stub = str_replace('{{ ' . $key . ' }}', $value, $stub);
        }

        return $stub;
    }
} 