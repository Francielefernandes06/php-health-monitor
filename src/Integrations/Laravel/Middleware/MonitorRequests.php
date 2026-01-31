<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Integrations\Laravel\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPHealth\Monitor\Monitor;
use Symfony\Component\HttpFoundation\Response;

class MonitorRequests
{
    protected Monitor $monitor;

    public function __construct(Monitor $monitor)
    {
        $this->monitor = $monitor;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if should monitor this request
        if (!$this->shouldMonitor($request)) {
            return $next($request);
        }

        // Listen to database queries
        $this->listenToQueries();

        // Process request
        $response = $next($request);

        return $response;
    }

    /**
     * Determine if request should be monitored
     */
    protected function shouldMonitor(Request $request): bool
    {
        $ignoredRoutes = config('health-monitor.collectors.request.ignore_routes', []);

        foreach ($ignoredRoutes as $pattern) {
            if ($request->is($pattern)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Listen to database queries
     */
    protected function listenToQueries(): void
    {
        if (!config('health-monitor.collectors.database.enabled', true)) {
            return;
        }

        DB::listen(function ($query) {
            $collector = $this->monitor->getCollector('database');
            
            if ($collector) {
                $collector->addQuery([
                    'sql' => $this->replaceBindings($query->sql, $query->bindings),
                    'duration' => $query->time,
                    'connection' => $query->connectionName,
                ]);
            }
        });
    }

    /**
     * Replace bindings in SQL
     */
    protected function replaceBindings(string $sql, array $bindings): string
    {
        if (empty($bindings)) {
            return $sql;
        }

        foreach ($bindings as $binding) {
            $value = is_numeric($binding) ? $binding : "'{$binding}'";
            $sql = preg_replace('/\?/', (string) $value, $sql, 1);
        }

        return $sql;
    }
}