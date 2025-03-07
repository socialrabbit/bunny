<?php

namespace Bunny\Portfolio;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Facades\Image;

class PortfolioManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The configuration options.
     *
     * @var array
     */
    protected $config;

    /**
     * Create a new portfolio manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->config = $app['config']['bunny.portfolio'];
    }

    /**
     * Generate portfolio components.
     *
     * @param  array  $options
     * @return void
     */
    public function generateComponents(array $options)
    {
        $this->generateProjects($options);
        $this->generateAbout($options);
        $this->generateContact($options);
        $this->generateLayout($options);
        $this->generateAssets($options);
    }

    /**
     * Generate project components.
     *
     * @param  array  $options
     * @return void
     */
    protected function generateProjects(array $options)
    {
        // Generate project model
        $this->generateModel('Project', [
            'title' => ['type' => 'string', 'required' => true],
            'slug' => ['type' => 'string', 'required' => true],
            'description' => ['type' => 'text', 'required' => true],
            'image' => ['type' => 'string', 'required' => true],
            'link' => ['type' => 'string', 'nullable' => true],
            'github' => ['type' => 'string', 'nullable' => true],
            'technologies' => ['type' => 'json', 'required' => true],
            'category_id' => ['type' => 'integer', 'required' => true],
            'featured' => ['type' => 'boolean', 'default' => false],
            'order' => ['type' => 'integer', 'default' => 0],
        ]);

        // Generate category model
        $this->generateModel('Category', [
            'name' => ['type' => 'string', 'required' => true],
            'slug' => ['type' => 'string', 'required' => true],
            'description' => ['type' => 'text', 'nullable' => true],
        ]);

        // Generate controllers
        $this->generateController('Project', 'portfolio');
        $this->generateController('Category', 'portfolio');

        // Generate views
        $this->generateViews('projects', [
            'index',
            'show',
            'grid',
            'filters',
            'search',
        ]);

        // Generate API resources
        if ($options['api_enabled']) {
            $this->generateApiResources('Project');
            $this->generateApiResources('Category');
        }
    }

    /**
     * Generate about section components.
     *
     * @param  array  $options
     * @return void
     */
    protected function generateAbout(array $options)
    {
        // Generate models
        $this->generateModel('Skill', [
            'name' => ['type' => 'string', 'required' => true],
            'level' => ['type' => 'integer', 'required' => true],
            'category' => ['type' => 'string', 'required' => true],
            'icon' => ['type' => 'string', 'nullable' => true],
        ]);

        $this->generateModel('Experience', [
            'title' => ['type' => 'string', 'required' => true],
            'company' => ['type' => 'string', 'required' => true],
            'location' => ['type' => 'string', 'required' => true],
            'start_date' => ['type' => 'date', 'required' => true],
            'end_date' => ['type' => 'date', 'nullable' => true],
            'description' => ['type' => 'text', 'required' => true],
            'technologies' => ['type' => 'json', 'required' => true],
        ]);

        // Generate views
        $this->generateViews('about', [
            'index',
            'skills',
            'experience',
            'education',
        ]);
    }

    /**
     * Generate contact section components.
     *
     * @param  array  $options
     * @return void
     */
    protected function generateContact(array $options)
    {
        // Generate model
        $this->generateModel('Contact', [
            'name' => ['type' => 'string', 'required' => true],
            'email' => ['type' => 'string', 'required' => true],
            'subject' => ['type' => 'string', 'required' => true],
            'message' => ['type' => 'text', 'required' => true],
            'status' => ['type' => 'string', 'default' => 'pending'],
        ]);

        // Generate views
        $this->generateViews('contact', [
            'index',
            'form',
            'success',
            'map',
        ]);

        // Generate mail templates
        $this->generateMail('ContactForm', [
            'contact' => 'App\Models\Contact',
        ]);
    }

    /**
     * Generate layout components.
     *
     * @param  array  $options
     * @return void
     */
    protected function generateLayout(array $options)
    {
        $this->generateViews('layouts', [
            'app',
            'navigation',
            'footer',
            'meta',
        ]);

        if ($options['dark_mode']) {
            $this->generateViews('components', [
                'theme-switcher',
            ]);
        }
    }

    /**
     * Generate assets.
     *
     * @param  array  $options
     * @return void
     */
    protected function generateAssets(array $options)
    {
        // Generate CSS
        $this->generateStyles([
            'app',
            'portfolio',
            'animations',
            'dark-mode',
        ]);

        // Generate JavaScript
        $this->generateScripts([
            'app',
            'portfolio',
            'filters',
            'animations',
        ]);

        // Generate images directory structure
        $this->generateImageDirectories();
    }

    /**
     * Generate model with relationships and traits.
     *
     * @param  string  $name
     * @param  array  $fields
     * @return void
     */
    protected function generateModel($name, array $fields)
    {
        $stub = File::get(__DIR__ . '/stubs/portfolio/model.stub');
        $content = $this->parseStub($stub, [
            'modelName' => $name,
            'fillable' => $this->generateFillable($fields),
            'casts' => $this->generateCasts($fields),
            'rules' => $this->generateValidationRules($fields),
        ]);

        $path = app_path("Models/{$name}.php");
        File::put($path, $content);
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