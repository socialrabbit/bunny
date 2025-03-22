<?php
// src/APIManager.php

namespace Bunny;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class APIManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Create a new API manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Generate API components.
     *
     * @param  string  $siteType
     * @param  string  $modelName
     * @param  array  $fields
     * @param  string  $apiType
     * @return void
     */
    public function generateComponents($siteType, $modelName, array $fields, $apiType)
    {
        $this->generateController($siteType, $modelName, $apiType);
        $this->generateResource($modelName, $fields);
        $this->generateRequest($modelName, $fields);
        $this->generateRoutes($siteType, $modelName, $apiType);
        $this->generateTests($siteType, $modelName, $apiType);
        $this->generatePolicies($modelName);
    }

    /**
     * Generate the API controller.
     *
     * @param  string  $siteType
     * @param  string  $modelName
     * @param  string  $apiType
     * @return void
     */
    protected function generateController($siteType, $modelName, $apiType)
    {
        $stub = File::get(__DIR__ . "/stubs/api/controllers/{$apiType}.stub");
        $content = $this->parseStub($stub, [
            'modelName' => $modelName,
            'modelVariable' => Str::camel($modelName),
            'resourceName' => "{$modelName}Resource",
            'requestName' => "{$modelName}Request",
        ]);

        $path = app_path("Http/Controllers/Api/{$modelName}Controller.php");
        File::put($path, $content);
    }

    /**
     * Generate the API resource.
     *
     * @param  string  $modelName
     * @param  array  $fields
     * @return void
     */
    protected function generateResource($modelName, array $fields)
    {
        $stub = File::get(__DIR__ . '/stubs/api/resource.stub');
        $content = $this->parseStub($stub, [
            'modelName' => $modelName,
            'fields' => $this->generateResourceFields($fields),
        ]);

        $path = app_path("Http/Resources/{$modelName}Resource.php");
        File::put($path, $content);
    }

    /**
     * Generate the API request.
     *
     * @param  string  $modelName
     * @param  array  $fields
     * @return void
     */
    protected function generateRequest($modelName, array $fields)
    {
        $stub = File::get(__DIR__ . '/stubs/api/request.stub');
        $content = $this->parseStub($stub, [
            'modelName' => $modelName,
            'rules' => $this->generateValidationRules($fields),
        ]);

        $path = app_path("Http/Requests/Api/{$modelName}Request.php");
        File::put($path, $content);
    }

    /**
     * Generate the API routes.
     *
     * @param  string  $siteType
     * @param  string  $modelName
     * @param  string  $apiType
     * @return void
     */
    protected function generateRoutes($siteType, $modelName, $apiType)
    {
        $stub = File::get(__DIR__ . "/stubs/api/routes/{$apiType}.stub");
        $content = $this->parseStub($stub, [
            'modelName' => $modelName,
            'routeName' => Str::plural(Str::kebab($modelName)),
        ]);

        $path = routes_path("api.php");
        File::append($path, "\n" . $content);
    }

    /**
     * Generate the API tests.
     *
     * @param  string  $siteType
     * @param  string  $modelName
     * @param  string  $apiType
     * @return void
     */
    protected function generateTests($siteType, $modelName, $apiType)
    {
        $stub = File::get(__DIR__ . "/stubs/api/tests/{$apiType}.stub");
        $content = $this->parseStub($stub, [
            'modelName' => $modelName,
            'modelVariable' => Str::camel($modelName),
        ]);

        $path = base_path("tests/Feature/Api/{$modelName}Test.php");
        File::put($path, $content);
    }

    /**
     * Generate the API policies.
     *
     * @param  string  $modelName
     * @return void
     */
    protected function generatePolicies($modelName)
    {
        $stub = File::get(__DIR__ . '/stubs/policy.stub');
        $content = $this->parseStub($stub, [
            'modelName' => $modelName,
        ]);

        $path = app_path("Policies/{$modelName}Policy.php");
        File::put($path, $content);
    }

    /**
     * Generate resource fields.
     *
     * @param  array  $fields
     * @return string
     */
    protected function generateResourceFields(array $fields)
    {
        $resourceFields = [];
        foreach ($fields as $field) {
            $resourceFields[] = "'{$field['name']}' => \$this->{$field['name']},";
        }

        return implode("\n            ", $resourceFields);
    }

    /**
     * Generate validation rules.
     *
     * @param  array  $fields
     * @return string
     */
    protected function generateValidationRules(array $fields)
    {
        $rules = [];
        foreach ($fields as $field) {
            $rule = [];
            
            // Required rule
            if (!isset($field['nullable']) || !$field['nullable']) {
                $rule[] = 'required';
            }

            // Type rule
            if (isset($field['type'])) {
                switch ($field['type']) {
                    case 'email':
                        $rule[] = 'email';
                        break;
                    case 'integer':
                        $rule[] = 'integer';
                        break;
                    case 'float':
                        $rule[] = 'numeric';
                        break;
                    case 'date':
                        $rule[] = 'date';
                        break;
                    case 'datetime':
                        $rule[] = 'date';
                        break;
                }
            }

            // Min/Max rules
            if (isset($field['min'])) {
                $rule[] = "min:{$field['min']}";
            }
            if (isset($field['max'])) {
                $rule[] = "max:{$field['max']}";
            }

            // Unique rule
            if (isset($field['unique']) && $field['unique']) {
                $rule[] = 'unique:' . Str::plural(Str::snake($field['name']));
            }

            $rules[] = "'{$field['name']}' => ['" . implode("', '", $rule) . "']";
        }

        return "[\n            " . implode(",\n            ", $rules) . "\n        ]";
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