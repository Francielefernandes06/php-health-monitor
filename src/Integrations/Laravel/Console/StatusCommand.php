<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Integrations\Laravel\Console;

use Illuminate\Console\Command;
use PHPHealth\Monitor\Monitor;

class StatusCommand extends Command
{
    protected $signature = 'health-monitor:status';
    protected $description = 'Show PHP Health Monitor status';

    public function handle(): int
    {
        $monitor = app(Monitor::class);
        $storage = $monitor->getStorage();

        $this->info('PHP Health Monitor Status');
        $this->newLine();

        // Basic info
        $this->line('Version: ' . Monitor::version());
        $this->line('Enabled: ' . (config('health-monitor.enabled') ? '✓ Yes' : '✗ No'));
        $this->line('Storage: ' . config('health-monitor.storage.driver'));
        
        $this->newLine();

        // Stats (if storage supports it)
        try {
            $recent = $storage->retrieve(['limit' => 1]);
            
            if (!empty($recent)) {
                $this->line('Last request monitored: ' . date('Y-m-d H:i:s', $recent[0]['created_at']));
            }
            
            $this->line('Total requests monitored: ' . count($storage->retrieve(['limit' => 10000])));
        } catch (\Exception $e) {
            $this->warn('Could not retrieve stats: ' . $e->getMessage());
        }

        return self::SUCCESS;
    }
}