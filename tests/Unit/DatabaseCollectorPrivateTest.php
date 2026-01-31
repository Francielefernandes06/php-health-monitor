<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Tests\Unit;

use PHPHealth\Monitor\Collectors\DatabaseCollector;
use PHPHealth\Monitor\Support\Config;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class DatabaseCollectorPrivateTest extends TestCase
{
    public function test_private_properties_and_methods(): void
    {
        $config = new Config([
            'collectors' => [
                'database' => [
                    'enabled' => true,
                ],
            ],
        ]);
        $collector = new DatabaseCollector($config);
        $ref = new ReflectionClass($collector);
        $queriesProp = $ref->getProperty('queries');
        $queriesProp->setAccessible(true);
        $queriesProp->setValue($collector, [['sql' => 'SELECT 1', 'duration' => 10]]);
        $method = $ref->getMethod('setMonitorEnabled');
        $method->invoke($collector, true); // Garante monitoramento habilitado
        $method = $ref->getMethod('createMonitoredPDO');
        $pdo = $method->invoke($collector, 'sqlite::memory:');
        $this->assertInstanceOf('PHPHealth\Monitor\Support\MonitoredPDO', $pdo);
    }
}
