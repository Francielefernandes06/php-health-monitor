<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Integrations\Laravel\Console;

use Illuminate\Console\Command;
use PHPHealth\Monitor\Monitor;

class CleanupCommand extends Command
{
    protected $signature = 'health-monitor:cleanup {--days=7 : Number of days to keep}';
    protected $description = 'Cleanup old monitoring data';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        
        $this->info("Cleaning up data older than {$days} days...");

        $monitor = app(Monitor::class);
        $storage = $monitor->getStorage();

        try {
            $deleted = $storage->cleanup($days);
            
            $this->info("✓ Cleaned {$deleted} old records");
            
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Cleanup failed: ' . $e->getMessage());
            
            return self::FAILURE;
        }
    }
}