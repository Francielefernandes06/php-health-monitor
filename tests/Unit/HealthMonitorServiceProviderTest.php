<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Tests\Unit;

use PHPUnit\Framework\TestCase;

class HealthMonitorServiceProviderTest extends TestCase
{
    public function test_can_instantiate_service_provider(): void
    {
        if (! class_exists('PHPHealth\Monitor\Integrations\Laravel\HealthMonitorServiceProvider')) {
            $this->markTestSkipped('HealthMonitorServiceProvider not found');

            return;
        }
        $provider = new \PHPHealth\Monitor\Integrations\Laravel\HealthMonitorServiceProvider(null);
        $this->assertInstanceOf(\PHPHealth\Monitor\Integrations\Laravel\HealthMonitorServiceProvider::class, $provider);
    }
}
