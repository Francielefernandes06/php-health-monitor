<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Tests\Unit;

use PHPHealth\Monitor\Monitor;
use PHPUnit\Framework\TestCase;

class MonitorShutdownTest extends TestCase
{
    public function test_shutdown_persists_data(): void
    {
        $dbPath = __DIR__ . '/../Fixtures/monitor-shutdown-test.db';
        if (file_exists($dbPath)) {
            unlink($dbPath);
        }
        $monitor = new Monitor([
            'storage' => [
                'driver' => 'sqlite',
                'database_path' => $dbPath,
            ],
        ]);
        $monitor->start();
        $monitor->shutdown();
        restore_error_handler();
        restore_exception_handler();
        $storage = $monitor->getStorage();
        $data = $storage->retrieve(['limit' => 1]);
        $this->assertIsArray($data);
        if (file_exists($dbPath)) {
            unlink($dbPath);
        }
    }
}
