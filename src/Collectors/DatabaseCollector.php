<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Collectors;

use PHPHealth\Monitor\Contracts\CollectorInterface;
use PHPHealth\Monitor\Support\Config;

/**
 * Coletor de queries de banco de dados
 */
class DatabaseCollector implements CollectorInterface
{
    private Config $config;
    private bool $started = false;

    /** @var array<int, array<string, mixed>> */
    private array $queries = [];

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
        // TODO: Implementar hook no PDO para capturar queries
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

        $slowThreshold = $this->config->get('collectors.database.slow_query_threshold', 100);

        return [
            'total_queries' => count($this->queries),
            'slow_queries' => array_filter($this->queries, fn($q) => $q['duration'] > $slowThreshold),
            'queries' => $this->queries,
        ];
    }

    public function reset(): void
    {
        $this->queries = [];
        $this->started = false;
    }

    /**
     * Registra uma query executada
     *
     * @param array<string, mixed> $queryData
     */
    public function addQuery(array $queryData): void
    {
        if (!$this->started) {
            return;
        }

        $this->queries[] = $queryData;
    }
}
