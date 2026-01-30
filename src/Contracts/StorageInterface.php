<?php

declare(strict_types=1);

namespace PHPHealth\Monitor\Contracts;

/**
 * Interface para implementações de storage
 */
interface StorageInterface
{
    /**
     * Armazena dados de monitoramento
     *
     * @param array<string, mixed> $data
     */
    public function store(array $data): bool;

    /**
     * Recupera dados de monitoramento
     *
     * @param array<string, mixed> $filters
     * @return array<int, mixed>
     */
    public function retrieve(array $filters = []): array;

    /**
     * Remove dados antigos
     *
     * @param int $days Número de dias para manter
     */
    public function cleanup(int $days = 7): int;

    /**
     * Obtém estatísticas agregadas
     *
     * @param string $metric
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getStats(string $metric, array $filters = []): array;
}
