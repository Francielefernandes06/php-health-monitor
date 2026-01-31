<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Tests\Unit;

use PHPHealth\Monitor\Monitor;
use PHPUnit\Framework\TestCase;

class MonitorCoreTest extends TestCase
{
    public function test_version_and_getters(): void
    {
        $monitor = new Monitor();
        $this->assertIsString(Monitor::version());
        $this->assertNotNull($monitor->getStorage());
        $this->assertNotNull($monitor->getCollector('request'));
    }

    public function test_start_and_stop(): void
    {
        $monitor = new Monitor();
        $monitor->start();
        restore_error_handler();
        restore_exception_handler();
        $this->assertTrue(true);
        $monitor->stop();
        restore_error_handler();
        restore_exception_handler();
        $this->assertTrue(true);
    }
}
