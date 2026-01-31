# Instalação no Laravel

## Instalação

### 1. Instalar via Composer

```bash
composer require phphealth/monitor
```

### 2. Publicar Configuração (Opcional)

```bash
php artisan vendor:publish --tag=health-monitor-config
```

Ou use o comando de instalação:

```bash
php artisan health-monitor:install
```

### 3. Configurar (Opcional)

Edite `config/health-monitor.php` conforme necessário:

```php
return [
    'enabled' => env('HEALTH_MONITOR_ENABLED', true),
    
    'storage' => [
        'driver' => 'sqlite',
        'database_path' => storage_path('health-monitor/monitor.db'),
    ],
    
    'collectors' => [
        'request' => [
            'enabled' => true,
            'slow_threshold' => 1000, // ms
            'ignore_routes' => [
                'health-monitor/*',
                'horizon/*',
                'telescope/*',
            ],
        ],
        'database' => [
            'enabled' => true,
            'slow_query_threshold' => 100, // ms
        ],
    ],
    
    'dashboard' => [
        'enabled' => true,
        'path' => '/health-monitor',
        'auth' => [
            'enabled' => true,
            'username' => env('HEALTH_MONITOR_USERNAME', 'admin'),
            'password' => env('HEALTH_MONITOR_PASSWORD', 'admin'),
        ],
    ],
];
```

### 4. Configurar Variáveis de Ambiente

Adicione ao `.env`:

```env
HEALTH_MONITOR_ENABLED=true
HEALTH_MONITOR_USERNAME=admin
HEALTH_MONITOR_PASSWORD=sua-senha-segura
```

## Uso

### Acesso ao Dashboard

Acesse: `http://seu-app.test/health-monitor`

Credenciais padrão:
- **Usuário:** admin
- **Senha:** admin (altere imediatamente!)

### Comandos Artisan

```bash
# Ver status
php artisan health-monitor:status

# Limpar dados antigos
php artisan health-monitor:cleanup --days=7

# Instalar/configurar
php artisan health-monitor:install
```

### Usar no Código

```php
use PHPHealth\Monitor\Integrations\Laravel\Facades\HealthMonitor;

// Acessar o monitor
$monitor = app(\PHPHealth\Monitor\Monitor::class);

// Ou via facade
HealthMonitor::getStorage()->retrieve();

// Adicionar contexto
$collector = HealthMonitor::getCollector('request');
```

## Monitoramento Automático

O monitor captura automaticamente:

✅ **Requisições HTTP**
- Tempo de resposta
- Uso de memória
- Status codes
- URIs

✅ **Queries do Eloquent**
- SQL executado
- Tempo de execução
- Detecção de queries lentas

✅ **Erros e Exceções**
- Stack traces
- Contexto completo

## Ignorar Rotas

Para ignorar certas rotas do monitoramento:

```php
// config/health-monitor.php
'collectors' => [
    'request' => [
        'ignore_routes' => [
            'health-monitor/*',
            'api/webhooks/*',
            '_debugbar/*',
        ],
    ],
],
```

## Desabilitar em Ambientes Específicos

```php
// config/health-monitor.php
'enabled' => env('HEALTH_MONITOR_ENABLED', !app()->environment('testing')),
```

Ou no `.env`:

```env
# Desabilitar em local
HEALTH_MONITOR_ENABLED=false
```

## Middleware Manual (Opcional)

Se preferir controle manual do middleware:

```php
// app/Http/Kernel.php
protected $middlewareGroups = [
    'web' => [
        // ... outros middlewares
        \PHPHealth\Monitor\Integrations\Laravel\Middleware\MonitorRequests::class,
    ],
];
```

## Usando com MySQL

```php
// config/health-monitor.php
'storage' => [
    'driver' => 'mysql',
    'connection' => [
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_DATABASE', 'health_monitor'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
    ],
],
```

## Troubleshooting

### ServiceProvider não encontrado

Se receber erro sobre ServiceProvider não encontrado:

```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Dashboard não carrega

Verifique:
1. `config/health-monitor.php` existe
2. `'dashboard.enabled' => true`
3. Rota não está em conflito

### Queries não aparecem

Certifique-se que:
1. `'collectors.database.enabled' => true`
2. Usando conexões do Laravel (DB facade ou Eloquent)
3. Middleware está ativo

## Performance

O monitor adiciona overhead mínimo:
- ~2-5ms por requisição
- ~10MB memória adicional

Para alto tráfego, considere:

```php
'performance' => [
    'sampling_rate' => 10, // Monitorar apenas 10%
],
```

## Próximos Passos

- Configure alertas (em breve)
- Explore o dashboard
- Otimize queries lentas identificadas
- Configure cleanup automático

## Suporte

- 📖 [Documentação Completa](https://github.com/Francielefernandes06/php-health-monitor)
- 🐛 [Reportar Bug](https://github.com/Francielefernandes06/php-health-monitor/issues)
- 💬 [Discussões](https://github.com/Francielefernandes06/php-health-monitor/discussions)