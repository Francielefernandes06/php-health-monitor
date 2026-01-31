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
    private bool $monitorEnabled = true;

    /** @var array<int, array<string, mixed>> */
    private array $queries = [];

    /** @var \PHPHealth\Monitor\Support\MonitoredPDO|null */
    private $pdo = null;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->monitorEnabled = $config->get('collectors.database.enabled', true);
    }

    public function start(): void
    {
        if ($this->started) {
            return;
        }

        $this->started = true;
        $this->monitorEnabled = $this->config->get('collectors.database.enabled', true);
    }

    public function stop(): void
    {
        $this->started = false;
        $this->pdo = null;
    }

    /**
     * @return array<string, mixed>
     */
    public function collect(): array
    {
        if (! $this->started) {
            return [];
        }

        $slowThreshold = $this->config->get('collectors.database.slow_query_threshold', 100);

        return [
            'total_queries' => count($this->queries),
            'slow_queries' => array_filter($this->queries, fn ($q) => $q['duration'] > $slowThreshold),
            'queries' => $this->queries,
        ];
    }

    public function reset(): void
    {
        $this->queries = [];
        $this->started = false;
        $this->pdo = null;
    }

    /**
     * Registra uma query executada
     *
     * @param array<string, mixed> $queryData
     */
    public function addQuery(array $queryData): void
    {
        if (! $this->started || ! $this->monitorEnabled) {
            return;
        }
        $this->queries[] = $queryData;
    }

    /**
     * Cria uma instância de MonitoredPDO para monitorar queries
     */
    public function createMonitoredPDO($dsn, $username = null, $passwd = null, $options = []): ?\PHPHealth\Monitor\Support\MonitoredPDO
    {
        if (! $this->monitorEnabled) {
            return new \PDO($dsn, $username, $passwd, $options);
        }
        $this->pdo = new \PHPHealth\Monitor\Support\MonitoredPDO($dsn, $username, $passwd, $options, $this, true);

        return $this->pdo;
    }

    /**
     * Permite habilitar/desabilitar monitoramento
     */
    public function setMonitorEnabled(bool $enabled): void
    {
        $this->monitorEnabled = $enabled;
        if ($this->pdo) {
            $this->pdo->setMonitorEnabled($enabled);
        }
    }
}
