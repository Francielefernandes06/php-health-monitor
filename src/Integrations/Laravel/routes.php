<?php

use Illuminate\Support\Facades\Route;

// Dashboard routes
Route::prefix(config('health-monitor.dashboard.path', 'health-monitor'))
    ->middleware('web')
    ->group(function () {
        // Dashboard UI
        Route::get('/', function () {
            return view('health-monitor::dashboard');
        })->name('health-monitor.dashboard');

        // API endpoints
        Route::prefix('api')->group(function () {
            Route::get('/stats', function () {
                $monitor = app(\PHPHealth\Monitor\Monitor::class);
                $storage = $monitor->getStorage();

                // TODO: Implement real stats
                return response()->json([
                    'success' => true,
                    'data' => [
                        'avg_response_time' => 245,
                        'requests_per_min' => 127,
                        'error_rate' => 0.8,
                        'memory_avg' => 45000000,
                    ],
                ]);
            })->name('health-monitor.api.stats');

            Route::get('/requests', function () {
                try {
                    $monitor = app(\PHPHealth\Monitor\Monitor::class);
                    $storage = $monitor->getStorage();
                    
                    $limit = request()->get('limit', 50);
                    $requests = $storage->retrieve(['limit' => $limit]);
                    
                    \Log::info('Requests retrieved:', [
                        'count' => count($requests),
                        'data' => $requests
                    ]);
                    
                    return view('health-monitor::api.requests', [
                        'requests' => $requests ?? [],
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Error in requests endpoint: ' . $e->getMessage());
                    return view('health-monitor::api.requests', [
                        'requests' => [],
                    ]);
                }
            })->name('health-monitor.api.requests');
        });
    });