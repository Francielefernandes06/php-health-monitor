<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Contracts;

/**
 * Interface para coletores de métricas
 */
interface CollectorInterface
{
    /**
     * Inicia a coleta de dados
     */
    public function start(): void;

    /**
     * Para a coleta de dados
     */
    public function stop(): void;

    /**
     * Coleta os dados capturados
     *
     * @return array<string, mixed>
     */
    public function collect(): array;

    /**
     * Reseta o estado do collector
     */
    public function reset(): void;
}
