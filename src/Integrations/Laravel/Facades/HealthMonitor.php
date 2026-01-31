<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Integrations\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void start()
 * @method static void stop()
 * @method static mixed getCollector(string $name)
 * @method static \PHPHealth\Monitor\Contracts\StorageInterface getStorage()
 * 
 * @see \PHPHealth\Monitor\Monitor
 */
class HealthMonitor extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'health-monitor';
    }
}