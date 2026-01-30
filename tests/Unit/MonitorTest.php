<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPHealth\Monitor\Monitor;
use PHPHealth\Monitor\Support\Config;

class MonitorTest extends TestCase
{
    private Monitor $monitor;

    protected function setUp(): void
    {
        $this->monitor = new Monitor([
            'storage' => [
                'driver' => 'sqlite',
                'database_path' => sys_get_temp_dir() . '/health-monitor-test.db',
            ],
        ]);
    }

    protected function tearDown(): void
    {
        // Limpa o banco de dados de teste
        $dbPath = sys_get_temp_dir() . '/health-monitor-test.db';
        if (file_exists($dbPath)) {
            unlink($dbPath);
        }
    }

    public function test_can_create_monitor_instance(): void
    {
        $this->assertInstanceOf(Monitor::class, $this->monitor);
    }

    public function test_can_start_monitoring(): void
    {
        $this->monitor->start();
        
        // Verifica que foi iniciado
        $this->assertTrue(true); // Placeholder
    }

    public function test_can_get_collector(): void
    {
        $requestCollector = $this->monitor->getCollector('request');
        
        $this->assertNotNull($requestCollector);
    }

    public function test_version_is_defined(): void
    {
        $version = Monitor::version();
        
        $this->assertIsString($version);
        $this->assertNotEmpty($version);
    }

    public function test_storage_is_accessible(): void
    {
        $storage = $this->monitor->getStorage();
        
        $this->assertNotNull($storage);
    }
}
