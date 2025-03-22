<?php

namespace Bunny\Console\Commands;

use Illuminate\Console\Command;
use Bunny\Providers\WebsiteTypeServiceProvider;

class CreateWebsiteCommand extends Command
{
    protected $signature = 'bunny:create {type : The type of website to create}';
    protected $description = 'Create a new website using Bunny';

    protected $websiteTypes;

    public function __construct(WebsiteTypeServiceProvider $provider)
    {
        parent::__construct();
        $this->websiteTypes = $provider->getWebsiteTypes();
    }

    public function handle()
    {
        $type = $this->argument('type');

        if (!array_key_exists($type, $this->websiteTypes)) {
            $this->error("Invalid website type. Available types: " . implode(', ', array_keys($this->websiteTypes)));
            return 1;
        }

        $this->info("Creating {$type} website...");

        try {
            $websiteClass = $this->websiteTypes[$type];
            $website = app()->make($websiteClass);
            
            $website->install();

            $this->info("Website created successfully!");
            $this->info("Your {$type} website has been set up with all necessary features and dependencies.");
            $this->info("You can now customize it according to your needs.");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("An error occurred while creating the website: " . $e->getMessage());
            return 1;
        }
    }
}