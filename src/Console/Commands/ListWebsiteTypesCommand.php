<?php

namespace Kisalay\Bunny\Console\Commands;

use Illuminate\Console\Command;
use Kisalay\Bunny\WebsiteTypes\WebsiteTypeManager;

class ListWebsiteTypesCommand extends Command
{
    protected $signature = 'bunny:list-types {--detailed}';
    protected $description = 'List all available website types';

    protected $manager;

    public function __construct(WebsiteTypeManager $manager)
    {
        parent::__construct();
        $this->manager = $manager;
    }

    public function handle()
    {
        $types = $this->manager->getAllTypeInfo();
        $detailed = $this->option('detailed');

        $this->info('Available Website Types:');
        $this->line('');

        foreach ($types as $type => $info) {
            $this->displayType($type, $info, $detailed);
            $this->line('');
        }

        $this->info('To install a website type, use:');
        $this->line('  php artisan bunny:install-type {type}');
        $this->line('');
        $this->info('To uninstall a website type, use:');
        $this->line('  php artisan bunny:uninstall-type {type}');
    }

    protected function displayType(string $type, array $info, bool $detailed)
    {
        $name = $this->manager->getTypeName($type);
        $description = $this->manager->getTypeDescription($type);
        $icon = $this->manager->getTypeIcon($type);
        $color = $this->manager->getTypeColor($type);

        $this->line(sprintf(
            '<fg=%s>%s</> %s',
            $color,
            $icon,
            $name
        ));

        $this->line("  {$description}");

        if ($detailed) {
            $this->line('');
            $this->line('  Features:');
            foreach ($info['features'] as $feature) {
                $this->line("    - {$feature}");
            }

            $this->line('');
            $this->line('  Dependencies:');
            foreach ($info['dependencies'] as $dependency) {
                $this->line("    - {$dependency}");
            }

            $this->line('');
            $this->line('  Class:');
            $this->line("    {$info['class']}");
        }
    }
} 