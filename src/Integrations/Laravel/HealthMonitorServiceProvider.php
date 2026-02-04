<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Integrations\Laravel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;
use PHPHealth\Monitor\Monitor;
use PHPHealth\Monitor\Support\Config;

class HealthMonitorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../../../config/health-monitor.php',
            'health-monitor'
        );

        // Register singleton
        $this->app->singleton(Monitor::class, function ($app) {
            $config = new Config($app['config']->get('health-monitor', []));
            return new Monitor($config->all());
        });

        // Alias
        $this->app->alias(Monitor::class, 'health-monitor');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

        // Publish config
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../../config/health-monitor.php' => config_path('health-monitor.php'),
            ], 'health-monitor-config');

            // Publish migrations (future)
            // $this->publishes([
            //     __DIR__ . '/../../../database/migrations' => database_path('migrations'),
            // ], 'health-monitor-migrations');

            // Register commands
            $this->commands([
                Console\InstallCommand::class,
                Console\StatusCommand::class,
                Console\CleanupCommand::class,
            ]);
        }

        // Register views do dashboard
        $this->loadViewsFrom(__DIR__ . '/../../../resources/views', 'health-monitor');

        // Register middleware
        if (config('health-monitor.enabled', true)) {
            $this->registerMiddleware();
        }

        // Register routes
        if (config('health-monitor.dashboard.enabled', true)) {
            $this->registerRoutes();
        }

        // Start monitoring
        if (config('health-monitor.enabled', true) && !$this->app->runningInConsole()) {
            $monitor = $this->app->make(Monitor::class);
            $monitor->start();
        }
    }

    /**
     * Register middleware
     */
    protected function registerMiddleware(): void
    {
        $kernel = $this->app->make(Kernel::class);
        
        // Add middleware to global stack
        $kernel->pushMiddleware(Middleware\MonitorRequests::class);
    }

    /**
     * Register routes
     */
    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
    }
}