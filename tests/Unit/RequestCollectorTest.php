<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Tests\Unit;

use PHPHealth\Monitor\Collectors\RequestCollector;
use PHPHealth\Monitor\Support\Config;
use PHPUnit\Framework\TestCase;

class RequestCollectorTest extends TestCase
{
    public function test_start_stop_and_collect(): void
    {
        $config = new Config([
            'collectors' => [
                'request' => [
                    'enabled' => true,
                ],
            ],
        ]);
        $collector = new RequestCollector($config);
        $collector->start();
        $result = $collector->collect();
        $this->assertIsArray($result);
        $collector->stop();
        $this->assertTrue(true);
    }

    public function test_reset(): void
    {
        $config = new Config([
            'collectors' => [
                'request' => [
                    'enabled' => true,
                ],
            ],
        ]);
        $collector = new RequestCollector($config);
        $collector->start();
        $collector->reset();
        $result = $collector->collect();
        $this->assertIsArray($result);
    }
}
