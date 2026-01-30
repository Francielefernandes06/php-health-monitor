# Documentação - PHP Health Monitor

## Índice

1. [Instalação](#instalação)
2. [Configuração](#configuração)
3. [Uso Básico](#uso-básico)
4. [Collectors](#collectors)
5. [Storage](#storage)
6. [Dashboard](#dashboard)
7. [Alertas](#alertas)
8. [Integrações](#integrações)
9. [API](#api)
10. [Performance](#performance)

## Instalação

### Requisitos

- PHP >= 7.4
- Extensão PDO
- Extensão JSON

### Via Composer

```bash
composer require phphealth/monitor
```

### Instalação Manual

1. Clone o repositório:
```bash
git clone https://github.com/seu-usuario/php-health-monitor.git
```

2. Instale as dependências:
```bash
composer install
```

## Configuração

### PHP Puro

```php
<?php
require_once 'vendor/autoload.php';

use PHPHealth\Monitor\Monitor;

$monitor = new Monitor([
    'storage' => [
        'driver' => 'sqlite',
        'database_path' => __DIR__ . '/storage/health-monitor.db',
    ],
]);

$monitor->start();
```

### Laravel

```bash
php artisan vendor:publish --provider="PHPHealth\Monitor\Laravel\HealthMonitorServiceProvider"
php artisan migrate
```

Configure em `config/health-monitor.php`:

```php
return [
    'storage' => [
        'driver' => 'mysql',
        'connection' => 'mysql',
    ],
];
```

### Symfony

```bash
php bin/console health-monitor:install
```

## Uso Básico

### Monitoramento Automático

Uma vez instalado, o monitoramento é **completamente automático**:

```php
// Isso é tudo que você precisa!
$monitor = new Monitor();
$monitor->start();

// Sua aplicação roda normalmente
// O monitor captura automaticamente:
// - Tempo de resposta
// - Queries SQL
// - Erros e exceções
// - Uso de memória
```

### Monitoramento Manual

Para casos específicos, você pode adicionar dados manualmente:

```php
// Registrar uma query SQL
$dbCollector = $monitor->getCollector('database');
$dbCollector->addQuery([
    'sql' => 'SELECT * FROM users',
    'duration' => 45.5, // ms
]);

// Adicionar contexto customizado
$monitor->addContext('user_id', auth()->id());
```

## Collectors

### Request Collector

Captura informações sobre requisições HTTP:

- Método HTTP
- URI
- Status code
- Tempo de resposta
- Uso de memória
- IP do cliente

```php
'collectors' => [
    'request' => [
        'enabled' => true,
        'slow_threshold' => 1000, // ms
    ],
],
```

### Database Collector

Monitora queries SQL:

- SQL executado
- Tempo de execução
- Detecção de N+1
- Queries sem índices

```php
'collectors' => [
    'database' => [
        'enabled' => true,
        'slow_query_threshold' => 100, // ms
    ],
],
```

### Error Collector

Captura erros e exceções:

- Erros PHP (E_ERROR, E_WARNING, etc)
- Exceções não tratadas
- Stack trace completo
- Contexto da aplicação

```php
'collectors' => [
    'error' => [
        'enabled' => true,
    ],
],
```

## Storage

### SQLite (Padrão)

Configuração zero, ideal para começar:

```php
'storage' => [
    'driver' => 'sqlite',
    'database_path' => '/path/to/health-monitor.db',
],
```

### MySQL/PostgreSQL

Para aplicações de alta carga:

```php
'storage' => [
    'driver' => 'mysql',
    'connection' => [
        'host' => 'localhost',
        'database' => 'health_monitor',
        'username' => 'root',
        'password' => 'secret',
    ],
],
```

### Limpeza Automática

```php
'storage' => [
    'cleanup_days' => 7, // Remove dados com mais de 7 dias
],
```

## Dashboard

### Acessando

Navegue para: `http://seu-dominio.com/health-monitor`

### Autenticação

```php
'dashboard' => [
    'auth' => [
        'enabled' => true,
        'username' => 'admin',
        'password' => 'senha-segura',
    ],
],
```

### Funcionalidades

- **Overview**: Métricas principais em tempo real
- **Requests**: Lista de requisições com filtros
- **Slow Queries**: Queries mais lentas
- **Errors**: Timeline de erros
- **Stats**: Estatísticas agregadas

## Alertas

### Configuração

```php
'alerts' => [
    'enabled' => true,
    
    'rules' => [
        'slow_request' => [
            'threshold' => 1000,
            'channels' => ['email', 'slack'],
        ],
    ],
    
    'channels' => [
        'email' => [
            'to' => 'dev@example.com',
        ],
        'slack' => [
            'webhook_url' => 'https://hooks.slack.com/...',
        ],
    ],
],
```

### Canais Disponíveis

- Email
- Slack
- Webhook personalizado

## Integrações

### Laravel

Middleware automático, service provider, Artisan commands:

```bash
php artisan health-monitor:status
php artisan health-monitor:cleanup
```

### Symfony

Bundle com comandos console e integração com Doctrine.

### WordPress

Plugin com admin panel integrado.

## API

### Endpoints REST

```
GET  /api/health-monitor/requests
GET  /api/health-monitor/queries
GET  /api/health-monitor/errors
GET  /api/health-monitor/stats
POST /api/health-monitor/cleanup
```

### Exemplo

```bash
curl http://seu-dominio.com/api/health-monitor/requests?limit=10
```

## Performance

### Overhead

- Tempo adicional: < 5ms por requisição
- Memória adicional: < 10MB

### Otimizações

```php
'performance' => [
    'buffer_size' => 100,
    'async' => true,
    'sampling_rate' => 50, // Monitora apenas 50% das requisições
],
```

### Sampling

Para aplicações de altíssimo tráfego:

```php
'performance' => [
    'sampling_rate' => 10, // Apenas 10%
],
```

## Troubleshooting

### Problema: Banco de dados não foi criado

**Solução**: Verifique permissões de escrita no diretório.

### Problema: Dashboard não carrega

**Solução**: Verifique se a rota está registrada e se o caminho está correto.

### Problema: Queries não são capturadas

**Solução**: Certifique-se de que está usando PDO e que o collector está habilitado.

## Contribuindo

Veja [CONTRIBUTING.md](../CONTRIBUTING.md) para detalhes.

## Licença

MIT License - veja [LICENSE](../LICENSE).
