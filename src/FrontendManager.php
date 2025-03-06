<?php
// src/FrontendManager.php

namespace Bunny;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FrontendManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Create a new frontend manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Generate frontend components.
     *
     * @param  string  $framework
     * @param  string  $uiLibrary
     * @param  string  $siteType
     * @param  string  $modelName
     * @return void
     */
    public function generateComponents($framework, $uiLibrary, $siteType, $modelName)
    {
        $this->createComponentDirectory($siteType);

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

        $this->installDependencies($framework, $uiLibrary);
    }

    /**
     * Create the component directory.
     *
     * @param  string  $siteType
     * @return void
     */
    protected function createComponentDirectory($siteType)
    {
        $path = resource_path("js/components/{$siteType}");
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }

    /**
     * Generate Vue components.
     *
     * @param  string  $siteType
     * @param  string  $modelName
     * @param  string  $uiLibrary
     * @return void
     */
    protected function generateVueComponents($siteType, $modelName, $uiLibrary)
    {
        $stub = File::get(__DIR__ . "/stubs/frontend/vue/{$siteType}.vue.stub");
        $content = $this->parseStub($stub, [
            'modelName' => $modelName,
            'uiLibrary' => $uiLibrary,
        ]);

        $path = resource_path("js/components/{$siteType}/{$modelName}List.vue");
        File::put($path, $content);
    }

    /**
     * Generate React components.
     *
     * @param  string  $siteType
     * @param  string  $modelName
     * @param  string  $uiLibrary
     * @return void
     */
    protected function generateReactComponents($siteType, $modelName, $uiLibrary)
    {
        $stub = File::get(__DIR__ . "/stubs/frontend/react/{$siteType}.jsx.stub");
        $content = $this->parseStub($stub, [
            'modelName' => $modelName,
            'uiLibrary' => $uiLibrary,
        ]);

        $path = resource_path("js/components/{$siteType}/{$modelName}List.jsx");
        File::put($path, $content);
    }

    /**
     * Generate Alpine.js components.
     *
     * @param  string  $siteType
     * @param  string  $modelName
     * @param  string  $uiLibrary
     * @return void
     */
    protected function generateAlpineComponents($siteType, $modelName, $uiLibrary)
    {
        $stub = File::get(__DIR__ . "/stubs/frontend/alpine/{$siteType}.js.stub");
        $content = $this->parseStub($stub, [
            'modelName' => $modelName,
            'uiLibrary' => $uiLibrary,
        ]);

        $path = resource_path("js/components/{$siteType}/{$modelName}List.js");
        File::put($path, $content);
    }

    /**
     * Install frontend dependencies.
     *
     * @param  string  $framework
     * @param  string  $uiLibrary
     * @return void
     */
    protected function installDependencies($framework, $uiLibrary)
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

        // Install dependencies
        if (!empty($dependencies)) {
            $this->runCommand(['npm', 'install', ...$dependencies]);
        }

        if (!empty($devDependencies)) {
            $this->runCommand(['npm', 'install', '-D', ...$devDependencies]);
        }
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

    /**
     * Run a command.
     *
     * @param  array  $command
     * @return void
     */
    protected function runCommand(array $command)
    {
        $process = new \Symfony\Component\Process\Process($command);
        $process->setTimeout(300);
        $process->run();
    }
} 