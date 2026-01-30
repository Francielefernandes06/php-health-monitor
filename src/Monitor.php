<?php

declare(strict_types=1);

namespace PHPHealth\Monitor;

use PHPHealth\Monitor\Collectors\RequestCollector;
use PHPHealth\Monitor\Collectors\DatabaseCollector;
use PHPHealth\Monitor\Collectors\ErrorCollector;
use PHPHealth\Monitor\Storage\StorageInterface;
use PHPHealth\Monitor\Storage\SQLiteStorage;
use PHPHealth\Monitor\Support\Config;

/**
 * PHP Health Monitor - Sistema de monitoramento principal
 */
class Monitor
{
    private Config $config;
    private StorageInterface $storage;
    private array $collectors = [];
    private bool $started = false;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [])
    {
        $this->config = new Config($config);
        $this->storage = $this->createStorage();
        $this->registerCollectors();
    }

    /**
     * Inicia o monitoramento
     */
    public function start(): void
    {
        if ($this->started) {
            return;
        }

        $this->started = true;

        // Registra shutdown handler para capturar dados no final da requisição
        register_shutdown_function([$this, 'shutdown']);

        // Inicia todos os collectors
        foreach ($this->collectors as $collector) {
            $collector->start();
        }
    }

    /**
     * Para o monitoramento
     */
    public function stop(): void
    {
        if (!$this->started) {
            return;
        }

        $this->started = false;

        // Para todos os collectors
        foreach ($this->collectors as $collector) {
            $collector->stop();
        }
    }

    /**
     * Chamado no shutdown - coleta e persiste dados
     */
    public function shutdown(): void
    {
        if (!$this->started) {
            return;
        }

        try {
            // Coleta dados de todos os collectors
            $data = [];
            foreach ($this->collectors as $name => $collector) {
                $data[$name] = $collector->collect();
            }

            // Persiste no storage
            $this->storage->store($data);
        } catch (\Throwable $e) {
            // Log silencioso - não deve quebrar a aplicação
            error_log("PHP Health Monitor Error: " . $e->getMessage());
        }
    }

    /**
     * Obtém um collector específico
     */
    public function getCollector(string $name): ?object
    {
        return $this->collectors[$name] ?? null;
    }

    /**
     * Obtém o storage
     */
    public function getStorage(): StorageInterface
    {
        return $this->storage;
    }

    /**
     * Cria a instância de storage baseada na configuração
     */
    private function createStorage(): StorageInterface
    {
        $driver = $this->config->get('storage.driver', 'sqlite');

        return match ($driver) {
            'sqlite' => new SQLiteStorage($this->config),
            default => throw new \InvalidArgumentException("Storage driver '{$driver}' not supported"),
        };
    }

    /**
     * Registra os collectors padrão
     */
    private function registerCollectors(): void
    {
        if ($this->config->get('collectors.request.enabled', true)) {
            $this->collectors['request'] = new RequestCollector($this->config);
        }

        if ($this->config->get('collectors.database.enabled', true)) {
            $this->collectors['database'] = new DatabaseCollector($this->config);
        }

        if ($this->config->get('collectors.error.enabled', true)) {
            $this->collectors['error'] = new ErrorCollector($this->config);
        }
    }

    /**
     * Versão do PHP Health Monitor
     */
    public static function version(): string
    {
        return '1.0.0-dev';
    }
}
