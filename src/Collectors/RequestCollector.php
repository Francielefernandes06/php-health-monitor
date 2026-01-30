<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Collectors;

use PHPHealth\Monitor\Contracts\CollectorInterface;
use PHPHealth\Monitor\Support\Config;

/**
 * Coletor de dados de requisições HTTP
 */
class RequestCollector implements CollectorInterface
{
    private Config $config;
    private float $startTime;
    private int $startMemory;
    private bool $started = false;

    /** @var array<string, mixed> */
    private array $data = [];

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function start(): void
    {
        if ($this->started) {
            return;
        }

        $this->started = true;
        $this->startTime = microtime(true);
        $this->startMemory = memory_get_usage(true);

        $this->data = [
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'CLI',
            'uri' => $_SERVER['REQUEST_URI'] ?? 'cli',
            'ip' => $this->getClientIp(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'timestamp' => time(),
        ];
    }

    public function stop(): void
    {
        $this->started = false;
    }

    /**
     * @return array<string, mixed>
     */
    public function collect(): array
    {
        if (!$this->started) {
            return [];
        }

        $duration = (microtime(true) - $this->startTime) * 1000; // ms
        $memoryUsed = memory_get_usage(true) - $this->startMemory;

        $this->data['duration'] = round($duration, 2);
        $this->data['memory'] = $memoryUsed;
        $this->data['memory_peak'] = memory_get_peak_usage(true);
        $this->data['status_code'] = http_response_code() ?: 200;
        $this->data['is_slow'] = $duration > $this->config->get('collectors.request.slow_threshold', 1000);

        return $this->data;
    }

    public function reset(): void
    {
        $this->data = [];
        $this->started = false;
    }

    /**
     * Obtém o IP do cliente de forma segura
     */
    private function getClientIp(): ?string
    {
        $keys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        ];

        foreach ($keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                // Pega o primeiro IP se houver múltiplos
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }

                return trim($ip);
            }
        }

        return null;
    }
}
