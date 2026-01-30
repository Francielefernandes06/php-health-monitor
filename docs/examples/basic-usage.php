<?php

/**
 * Exemplo de uso básico do PHP Health Monitor
 * 
 * Este arquivo demonstra como integrar o monitor em uma aplicação PHP pura.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use PHPHealth\Monitor\Monitor;

// Cria instância do monitor com configurações customizadas
$monitor = new Monitor([
    'storage' => [
        'driver' => 'sqlite',
        'database_path' => __DIR__ . '/../storage/health-monitor.db',
    ],
    'collectors' => [
        'request' => [
            'enabled' => true,
            'slow_threshold' => 500, // 500ms
        ],
        'database' => [
            'enabled' => true,
            'slow_query_threshold' => 50, // 50ms
        ],
    ],
]);

// Inicia o monitoramento
$monitor->start();

// Sua aplicação roda normalmente aqui
echo "PHP Health Monitor está ativo!\n";

// Simula algum processamento
sleep(1);

// Simula uma query lenta
$dbCollector = $monitor->getCollector('database');
if ($dbCollector) {
    $dbCollector->addQuery([
        'sql' => 'SELECT * FROM users WHERE created_at > ?',
        'duration' => 150, // ms
    ]);
}

// O monitoramento coleta dados automaticamente no shutdown
// Não precisa chamar nada manualmente!

echo "Dados coletados e armazenados automaticamente.\n";
echo "Acesse o dashboard em: http://localhost/health-monitor\n";
