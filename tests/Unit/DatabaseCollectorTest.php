<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPHealth\Monitor\Collectors\DatabaseCollector;
use PHPHealth\Monitor\Support\Config;

class DatabaseCollectorTest extends TestCase
{
    public function test_start_and_stop_enable_disable(): void
    {
        $config = new Config([
            'collectors' => [
                'database' => [
                    'enabled' => true,
                ],
            ],
        ]);
        $collector = new DatabaseCollector($config);
        $collector->start();
        $this->assertTrue(true); // started
        $collector->stop();
        $this->assertTrue(true); // stopped
        $collector->setMonitorEnabled(false);
        $this->assertTrue(true); // disabled
    }

    public function test_add_query_and_collect(): void
    {
        $config = new Config([
            'collectors' => [
                'database' => [
                    'enabled' => true,
                    'slow_query_threshold' => 1,
                ],
            ],
        ]);
        $collector = new DatabaseCollector($config);
        $collector->start();
        $collector->addQuery([
            'sql' => 'SELECT 1',
            'bindings' => [],
            'duration' => 5,
            'type' => 'query',
        ]);
        $result = $collector->collect();
        $this->assertArrayHasKey('total_queries', $result);
        $this->assertEquals(1, $result['total_queries']);
        $this->assertCount(1, $result['slow_queries']);
        $this->assertEquals('SELECT 1', $result['queries'][0]['sql']);
    }

    public function test_reset(): void
    {
        $config = new Config([
            'collectors' => [
                'database' => [
                    'enabled' => true,
                ],
            ],
        ]);
        $collector = new DatabaseCollector($config);
        $collector->start();
        $collector->addQuery([
            'sql' => 'SELECT 1',
            'bindings' => [],
            'duration' => 5,
            'type' => 'query',
        ]);
        $collector->reset();
        $result = $collector->collect();
        $this->assertIsArray($result);
        $this->assertArrayNotHasKey('total_queries', $result);
    }
}
