<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Integrations\Laravel\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'health-monitor:install';
    protected $description = 'Install PHP Health Monitor';

    public function handle(): int
    {
        $this->info('Installing PHP Health Monitor...');

        // Publish config
        $this->call('vendor:publish', [
            '--tag' => 'health-monitor-config',
            '--force' => $this->option('force'),
        ]);

        $this->info('✓ Configuration published');

        // Create storage directory
        $storagePath = storage_path('health-monitor');
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
            $this->info('✓ Storage directory created');
        }

        $this->newLine();
        $this->info('PHP Health Monitor installed successfully! 🎉');
        $this->newLine();
        $this->line('Next steps:');
        $this->line('1. Configure in config/health-monitor.php');
        $this->line('2. Access dashboard at /health-monitor');
        $this->line('3. Default credentials: admin/admin (change this!)');

        return self::SUCCESS;
    }
}