<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Tests\Unit;

use PHPHealth\Monitor\Collectors\DatabaseCollector;
use PHPHealth\Monitor\Support\Config;
use PHPHealth\Monitor\Support\MonitoredPDO;
use PHPUnit\Framework\TestCase;

class MonitoredPDOTest extends TestCase
{
    public function test_query_and_exec_are_monitored(): void
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
        $pdo = new MonitoredPDO('sqlite::memory:', null, null, [], $collector, true);
        $pdo->exec('CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT)');
        $pdo->exec("INSERT INTO test (name) VALUES ('foo')");
        $pdo->query('SELECT * FROM test');
        $queries = $collector->collect()['queries'];
        $this->assertGreaterThanOrEqual(3, count($queries));
        $this->assertEquals('CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT)', $queries[0]['sql']);
        $this->assertEquals('SELECT * FROM test', $queries[2]['sql']);
    }

    public function test_prepare_and_execute_are_monitored(): void
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
        $pdo = new MonitoredPDO('sqlite::memory:', null, null, [], $collector, true);
        $pdo->exec('CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT)');
        $stmt = $pdo->prepare('INSERT INTO test (name) VALUES (?)');
        $stmt->execute(['bar']);
        $queries = $collector->collect()['queries'];
        $this->assertStringContainsString('INSERT INTO test (name) VALUES', $queries[1]['sql']);
        $this->assertEquals(['bar'], $queries[1]['bindings']);
    }

    public function test_disable_monitoring(): void
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
        $pdo = new MonitoredPDO('sqlite::memory:', null, null, [], $collector, false);
        $pdo->exec('CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT)');
        $queries = $collector->collect()['queries'];
        $this->assertCount(0, $queries);
    }
}
