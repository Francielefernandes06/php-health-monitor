<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Tests\Unit;

use PHPHealth\Monitor\Monitor;
use PHPHealth\Monitor\Support\Config;
use PHPUnit\Framework\TestCase;

class MonitorTest extends TestCase
{
    public function test_monitored_pdo_captures_queries(): void
    {
        $config = new Config([
            'collectors' => [
                'database' => [
                    'enabled' => true,
                ],
            ],
        ]);
        $collector = new \PHPHealth\Monitor\Collectors\DatabaseCollector($config);
        $collector->start();
        $pdo = $collector->createMonitoredPDO('sqlite::memory:');
        $pdo->exec('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT)');
        $pdo->exec("INSERT INTO users (name) VALUES ('Alice')");
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([1]);
        $queries = $collector->collect()['queries'];
        $this->assertGreaterThanOrEqual(3, count($queries));
        $this->assertStringContainsString('SELECT * FROM users', $queries[2]['sql']);
        $this->assertEquals([1], $queries[2]['bindings']);
        $this->assertIsInt($queries[2]['duration']);
    }
    private Monitor $monitor;

    protected function setUp(): void
    {
        $dbPath = __DIR__ . '/../Fixtures/health-monitor-test.db';
        if (file_exists($dbPath)) {
            unlink($dbPath);
        }
        $this->monitor = new Monitor([
            'storage' => [
                'driver' => 'sqlite',
                'database_path' => $dbPath,
            ],
        ]);
    }

    protected function tearDown(): void
    {
        $dbPath = __DIR__ . '/../Fixtures/health-monitor-test.db';
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
        restore_error_handler();
        restore_exception_handler();
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
