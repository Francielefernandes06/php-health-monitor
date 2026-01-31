<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Tests\Unit;

use PHPHealth\Monitor\Collectors\ErrorCollector;
use PHPHealth\Monitor\Support\Config;
use PHPUnit\Framework\TestCase;

class ErrorCollectorTest extends TestCase
{
    public function test_start_stop_and_collect(): void
    {
        $config = new Config([
            'collectors' => [
                'error' => [
                    'enabled' => true,
                ],
            ],
        ]);
        $collector = new ErrorCollector($config);
        $collector->start();
        $result = $collector->collect();
        $this->assertIsArray($result);
        $collector->stop();
        // Remove handlers se definidos
        @restore_error_handler();
        @restore_exception_handler();
        $this->assertTrue(true);
    }

    public function test_reset(): void
    {
        $config = new Config([
            'collectors' => [
                'error' => [
                    'enabled' => true,
                ],
            ],
        ]);
        $collector = new ErrorCollector($config);
        $collector->start();
        $collector->reset();
        $result = $collector->collect();
        $this->assertIsArray($result);
        restore_error_handler();
        restore_exception_handler();
    }
}
