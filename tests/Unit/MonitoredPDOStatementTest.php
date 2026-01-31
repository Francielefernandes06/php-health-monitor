<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Tests\Unit;

use PHPHealth\Monitor\Collectors\DatabaseCollector;
use PHPHealth\Monitor\Support\Config;
use PHPHealth\Monitor\Support\MonitoredPDO;
use PHPUnit\Framework\TestCase;

class MonitoredPDOStatementTest extends TestCase
{
    public function test_execute_and_interpolate_query(): void
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
        $stmt->execute(['baz']);
        $queries = $collector->collect()['queries'];
        $this->assertStringContainsString('baz', $queries[1]['sql']);
        $this->assertEquals(['baz'], $queries[1]['bindings']);
    }
}
