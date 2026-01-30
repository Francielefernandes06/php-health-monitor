# Guia de Início Rápido

## Instalação em 5 Minutos

### 1. Instale via Composer

```bash
composer require phphealth/monitor
```

### 2. Configure (Opcional)

Para PHP puro, crie um arquivo `bootstrap.php`:

```php
<?php
require_once 'vendor/autoload.php';

use PHPHealth\Monitor\Monitor;

$monitor = new Monitor();
$monitor->start();

// Sua aplicação continua aqui...
```

### 3. Use em Sua Aplicação

**Opção A: Laravel**

Publique a configuração:
```bash
php artisan vendor:publish --provider="PHPHealth\Monitor\Laravel\HealthMonitorServiceProvider"
```

O monitoramento já está ativo! 🎉

**Opção B: Symfony**

```bash
php bin/console health-monitor:install
```

**Opção C: WordPress**

Ative o plugin no painel admin.

**Opção D: PHP Puro**

Inclua o bootstrap no início da aplicação:

```php
require_once 'bootstrap.php';
```

### 4. Acesse o Dashboard

Navegue para: `http://seu-site.com/health-monitor`

Credenciais padrão:
- Usuário: `admin`
- Senha: `admin` (⚠️ ALTERE ISSO IMEDIATAMENTE!)

## Configuração Básica

### Ajustar Thresholds

```php
$monitor = new Monitor([
    'collectors' => [
        'request' => [
            'slow_threshold' => 500, // 500ms ao invés de 1000ms
        ],
    ],
]);
```

### Mudar Local do Banco

```php
$monitor = new Monitor([
    'storage' => [
        'database_path' => '/var/www/storage/monitor.db',
    ],
]);
```

### Ativar Alertas

```php
$monitor = new Monitor([
    'alerts' => [
        'enabled' => true,
        'channels' => [
            'email' => [
                'to' => 'dev@example.com',
            ],
        ],
    ],
]);
```

## Exemplos Práticos

### Monitorar Query Específica

```php
$start = microtime(true);

// Sua query
$users = $db->query("SELECT * FROM users WHERE active = 1");

// Registra a query
$duration = (microtime(true) - $start) * 1000;
$monitor->getCollector('database')->addQuery([
    'sql' => 'SELECT * FROM users WHERE active = 1',
    'duration' => $duration,
]);
```

### Adicionar Contexto

```php
$monitor->addContext('user_id', auth()->id());
$monitor->addContext('tenant_id', tenant()->id());
```

### Ignorar Rotas Específicas

```php
$monitor = new Monitor([
    'collectors' => [
        'request' => [
            'ignore_routes' => [
                '/health-check',
                '/api/ping',
            ],
        ],
    ],
]);
```

## Próximos Passos

1. ✅ Instale e configure
2. 📊 Explore o dashboard
3. 🔔 Configure alertas
4. 📖 Leia a [documentação completa](docs/README.md)
5. 🤝 [Contribua](CONTRIBUTING.md) com o projeto

## Precisa de Ajuda?

- 📖 [Documentação Completa](docs/README.md)
- 💬 [Discussões no GitHub](https://github.com/seu-usuario/php-health-monitor/discussions)
- 🐛 [Reportar Bug](https://github.com/seu-usuario/php-health-monitor/issues)

## Dicas de Performance

- Use SQLite para começar, MySQL para escalar
- Configure `sampling_rate` para alto tráfego
- Ative limpeza automática de dados antigos
- Use processamento assíncrono quando possível

```php
'performance' => [
    'sampling_rate' => 10, // Apenas 10% das requisições
    'async' => true,
],
'storage' => [
    'cleanup_days' => 7, // Remove dados com > 7 dias
],
```

---

**Pronto!** Você já está monitorando sua aplicação PHP! 🚀
