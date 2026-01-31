<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Tests\Unit;

use PHPHealth\Monitor\Monitor;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class MonitorPrivateTest extends TestCase
{
    public function test_create_storage_returns_sqlite(): void
    {
        $monitor = new Monitor([
            'storage' => [
                'driver' => 'sqlite',
            ],
        ]);
        $ref = new ReflectionClass($monitor);
        $method = $ref->getMethod('createStorage');
        $method->setAccessible(true);
        $storage = $method->invoke($monitor);
        $this->assertNotNull($storage);
        $this->assertStringContainsString('SQLite', get_class($storage));
    }

    public function test_register_collectors(): void
    {
        $monitor = new Monitor([
            'collectors' => [
                'request' => ['enabled' => true],
                'database' => ['enabled' => true],
                'error' => ['enabled' => true],
            ],
        ]);
        $ref = new ReflectionClass($monitor);
        $method = $ref->getMethod('registerCollectors');
        $method->setAccessible(true);
        $method->invoke($monitor);
        $collectorsProp = $ref->getProperty('collectors');
        $collectorsProp->setAccessible(true);
        $collectors = $collectorsProp->getValue($monitor);
        $this->assertArrayHasKey('request', $collectors);
        $this->assertArrayHasKey('database', $collectors);
        $this->assertArrayHasKey('error', $collectors);
    }
}
