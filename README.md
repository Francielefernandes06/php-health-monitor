# PHP Health Monitor 🏥

[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Tests](https://img.shields.io/badge/tests-passing-brightgreen.svg)]()
[![Tests](https://github.com/Francielefernandes06/php-health-monitor/workflows/CI/badge.svg)](https://github.com/Francielefernandes06/php-health-monitor/actions)

> Sistema de monitoramento de saúde e performance para aplicações PHP - gratuito, leve e auto-hospedado.

## 🎯 Motivação

A maioria dos desenvolvedores PHP não tem acesso a ferramentas robustas de APM como New Relic ou Datadog por questões de custo. **PHP Health Monitor** democratiza a observabilidade, oferecendo monitoramento profissional totalmente gratuito.

## ✨ Funcionalidades

- 📊 **Monitoramento de Performance** - Tempo de resposta, throughput, requisições lentas
- 🗄️ **Análise de Queries SQL** - Detecção de queries lentas, N+1, queries sem índices
- 💾 **Monitoramento de Memória** - Memory leaks, uso excessivo
- 🐛 **Captura de Erros** - Erros e exceções com contexto completo
- 🚀 **Cache Metrics** - Hit/miss rate para Redis e Memcached
- 💚 **Health Checks** - Verificação automática de serviços
- 🔔 **Alertas** - Notificações via Email, Webhook, Slack
- 📈 **Dashboard Web** - Interface intuitiva para visualização

## 🚀 Instalação

### Via Composer (recomendado)

```bash
composer require phphealth/monitor
```

### Configuração Rápida

#### Laravel

```bash
php artisan vendor:publish --provider="PHPHealth\Monitor\Laravel\HealthMonitorServiceProvider"
php artisan migrate
```

#### Symfony

```bash
php bin/console health-monitor:install
```

#### PHP Puro

```php
<?php
require_once 'vendor/autoload.php';

use PHPHealth\Monitor\Monitor;

$monitor = new Monitor([
    'storage' => 'sqlite',
    'database_path' => __DIR__ . '/storage/health-monitor.db',
]);

$monitor->start();
```

## 📖 Uso Básico

### Monitoramento Automático

Uma vez instalado, o monitoramento é **completamente automático**. Não precisa modificar seu código.

### Acessar Dashboard

Navegue para: `http://seu-dominio.com/health-monitor`

Credenciais padrão:
- **Usuário:** admin
- **Senha:** admin (altere imediatamente!)

### Configurar Alertas

```php
// config/health-monitor.php

return [
    'alerts' => [
        'slow_request' => [
            'enabled' => true,
            'threshold' => 1000, // ms
            'channels' => ['email', 'slack'],
        ],
        'high_error_rate' => [
            'enabled' => true,
            'threshold' => 5, // porcentagem
            'channels' => ['slack'],
        ],
    ],
    
    'channels' => [
        'email' => [
            'to' => 'dev@example.com',
        ],
        'slack' => [
            'webhook_url' => env('SLACK_WEBHOOK_URL'),
        ],
    ],
];
```

## 📊 Screenshots

### Dashboard Principal
![Dashboard](docs/images/dashboard.png)

### Análise de Requisições
![Requests](docs/images/requests.png)

### Queries SQL
![Queries](docs/images/queries.png)

## 🏗️ Arquitetura

```
┌─────────────┐
│  Aplicação  │
└──────┬──────┘
       │
       ▼
┌─────────────┐
│  Collector  │ ◄── Intercepta requisições, queries, erros
└──────┬──────┘
       │
       ▼
┌─────────────┐
│   Buffer    │ ◄── Armazena temporariamente (performance)
└──────┬──────┘
       │
       ▼
┌─────────────┐
│   Storage   │ ◄── SQLite/MySQL/PostgreSQL
└──────┬──────┘
       │
       ├──────────┐
       ▼          ▼
┌──────────┐  ┌─────────┐
│ Analyzer │  │  Alerts │
└────┬─────┘  └────┬────┘
     │             │
     ▼             ▼
┌──────────────────────┐
│     Dashboard        │
└──────────────────────┘
```

## 🎯 Roadmap

### v1.0.0 - MVP (Em Desenvolvimento)
- [x] Estrutura base do projeto
- [x] Documentação inicial
- [ ] Collector de requisições HTTP
- [ ] Collector de queries SQL (MySQL)
- [ ] Storage SQLite
- [ ] Dashboard básico
- [ ] Testes unitários

### v1.1.0 - Expansão
- [ ] Collector de erros/exceções
- [ ] Monitoramento de memória
- [ ] Gráficos e estatísticas
- [ ] Alertas por email
- [ ] Integração Laravel

### v1.2.0 - Avançado
- [ ] Suporte PostgreSQL
- [ ] Health checks
- [ ] Alertas Slack/Webhook
- [ ] Integrações Symfony e WordPress
- [ ] API REST

## 🤝 Contribuindo

Contribuições são muito bem-vindas! Veja [CONTRIBUTING.md](CONTRIBUTING.md) para detalhes.

### Como Contribuir

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/MinhaFeature`)
3. Commit suas mudanças (`git commit -m 'Adiciona MinhaFeature'`)
4. Push para a branch (`git push origin feature/MinhaFeature`)
5. Abra um Pull Request

## 📝 Requisitos

- PHP >= 7.4
- Extensões: PDO, JSON
- Opcionais: Redis, Memcached (para monitoramento de cache)

## 🔒 Segurança

Se você descobrir alguma vulnerabilidade de segurança, por favor envie um email para security@phphealth.dev ao invés de usar o issue tracker.

## 📄 Licença

Este projeto está sob a licença MIT. Veja [LICENSE](LICENSE) para mais detalhes.

## 🙏 Agradecimentos

- Inspirado em ferramentas como New Relic, Datadog e Laravel Telescope
- Comunidade PHP por todo o suporte

## 📞 Suporte

- 📖 [Documentação Completa](docs/README.md)
- 💬 [Discussões no GitHub](https://github.com/seu-usuario/php-health-monitor/discussions)
- 🐛 [Reportar Bug](https://github.com/seu-usuario/php-health-monitor/issues)

---

Feito com ❤️ para a comunidade PHP
