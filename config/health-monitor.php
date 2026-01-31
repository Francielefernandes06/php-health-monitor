<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enable Monitoring
    |--------------------------------------------------------------------------
    |
    | Master switch to enable/disable all monitoring.
    |
    */
    'enabled' => env('HEALTH_MONITOR_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Storage Configuration
    |--------------------------------------------------------------------------
    |
    | Configuração do storage de dados. SQLite é usado por padrão para
    | simplicidade, mas MySQL e PostgreSQL são suportados.
    |
    */
    'storage' => [
        'driver' => env('HEALTH_MONITOR_STORAGE_DRIVER', 'sqlite'),
        'database_path' => env('HEALTH_MONITOR_DB_PATH', storage_path('health-monitor.db')),
        
        // Dias para manter os dados antes da limpeza automática
        'cleanup_days' => env('HEALTH_MONITOR_CLEANUP_DAYS', 7),
        
        // Configuração para MySQL/PostgreSQL
        'connection' => [
            'host' => env('HEALTH_MONITOR_DB_HOST', 'localhost'),
            'port' => env('HEALTH_MONITOR_DB_PORT', 3306),
            'database' => env('HEALTH_MONITOR_DB_NAME', 'health_monitor'),
            'username' => env('HEALTH_MONITOR_DB_USER', 'root'),
            'password' => env('HEALTH_MONITOR_DB_PASS', ''),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Collectors Configuration
    |--------------------------------------------------------------------------
    |
    | Configuração dos coletores de métricas. Você pode habilitar/desabilitar
    | coletores específicos ou ajustar seus thresholds.
    |
    */
    'collectors' => [
        'request' => [
            'enabled' => true,
            // Threshold em milissegundos para considerar uma requisição lenta
            'slow_threshold' => 1000,
        ],
        
        'database' => [
            'enabled' => true,
            // Threshold em milissegundos para considerar uma query lenta
            'slow_query_threshold' => 100,
        ],
        
        'error' => [
            'enabled' => true,
        ],
        
        'memory' => [
            'enabled' => true,
            // Threshold em bytes para alertar sobre uso de memória
            'high_usage_threshold' => 128 * 1024 * 1024, // 128MB
        ],
        
        'cache' => [
            'enabled' => false,
            // Monitora Redis, Memcached, etc
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Alerts Configuration
    |--------------------------------------------------------------------------
    |
    | Configure alertas para serem notificado sobre problemas de performance
    | ou erros em sua aplicação.
    |
    */
    'alerts' => [
        'enabled' => env('HEALTH_MONITOR_ALERTS_ENABLED', false),
        
        'rules' => [
            'slow_request' => [
                'enabled' => true,
                'threshold' => 1000, // ms
                'min_occurrences' => 5, // Em 5 minutos
                'channels' => ['email', 'slack'],
            ],
            
            'high_error_rate' => [
                'enabled' => true,
                'threshold' => 5, // porcentagem
                'window' => 300, // 5 minutos
                'channels' => ['slack'],
            ],
            
            'memory_leak' => [
                'enabled' => true,
                'threshold' => 256 * 1024 * 1024, // 256MB
                'channels' => ['email'],
            ],
        ],
        
        'channels' => [
            'email' => [
                'to' => env('HEALTH_MONITOR_ALERT_EMAIL', 'dev@example.com'),
                'from' => env('MAIL_FROM_ADDRESS', 'monitor@example.com'),
            ],
            
            'slack' => [
                'webhook_url' => env('HEALTH_MONITOR_SLACK_WEBHOOK'),
                'channel' => env('HEALTH_MONITOR_SLACK_CHANNEL', '#monitoring'),
                'username' => 'Health Monitor',
            ],
            
            'webhook' => [
                'url' => env('HEALTH_MONITOR_WEBHOOK_URL'),
                'method' => 'POST',
                'headers' => [],
            ],
        ],
        
        // Evita spam de alertas
        'debounce' => [
            'enabled' => true,
            'time' => 300, // 5 minutos
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard Configuration
    |--------------------------------------------------------------------------
    |
    | Configuração do dashboard web de visualização.
    |
    */
    'dashboard' => [
        'enabled' => env('HEALTH_MONITOR_DASHBOARD_ENABLED', true),
        'path' => env('HEALTH_MONITOR_DASHBOARD_PATH', '/health-monitor'),
        
        'auth' => [
            'enabled' => true,
            'username' => env('HEALTH_MONITOR_USERNAME', 'admin'),
            'password' => env('HEALTH_MONITOR_PASSWORD', 'admin'), // ALTERE ISSO!
        ],
        
        // Refresh automático em segundos (0 = desabilitado)
        'auto_refresh' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    |
    | Ajustes para minimizar o impacto do monitor na performance.
    |
    */
    'performance' => [
        // Tamanho do buffer antes de persistir dados
        'buffer_size' => 100,
        
        // Processamento assíncrono (requer extensão)
        'async' => false,
        
        // Amostragem: apenas monitora X% das requisições
        'sampling_rate' => 100, // 100% = todas
    ],

    /*
    |--------------------------------------------------------------------------
    | Privacy Configuration
    |--------------------------------------------------------------------------
    |
    | Configurações de privacidade e segurança.
    |
    */
    'privacy' => [
        // Anonimiza IPs (192.168.1.1 -> 192.168.1.xxx)
        'anonymize_ip' => false,
        
        // Sanitiza queries SQL (remove valores)
        'sanitize_sql' => true,
        
        // Remove dados sensíveis de user agents
        'sanitize_user_agent' => false,
    ],
];