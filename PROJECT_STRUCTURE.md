# Estrutura do Projeto PHP Health Monitor

```
php-health-monitor/
│
├── .github/                          # GitHub específico
│   ├── workflows/
│   │   └── ci.yml                   # GitHub Actions CI/CD
│   ├── ISSUE_TEMPLATE/
│   │   ├── bug_report.md            # Template para bugs
│   │   └── feature_request.md       # Template para features
│   └── PULL_REQUEST_TEMPLATE.md     # Template para PRs
│
├── config/                           # Configurações
│   └── health-monitor.php           # Configuração principal
│
├── database/                         # Database related
│   ├── migrations/                  # Migrations (futuro)
│   └── seeds/                       # Seeds (futuro)
│
├── docs/                             # Documentação
│   ├── README.md                    # Documentação principal
│   ├── images/                      # Screenshots e imagens
│   └── examples/                    # Exemplos de uso
│       └── basic-usage.php          # Exemplo básico
│
├── public/                           # Assets públicos
│   └── dashboard/                   # Dashboard web
│       ├── index.html               # Interface principal
│       ├── css/                     # Estilos (futuro)
│       ├── js/                      # JavaScript (futuro)
│       └── assets/                  # Imagens, ícones (futuro)
│
├── src/                              # Código fonte principal
│   ├── Collectors/                  # Coletores de métricas
│   │   ├── RequestCollector.php    # Coleta dados de requisições
│   │   ├── DatabaseCollector.php   # Coleta queries SQL
│   │   └── ErrorCollector.php      # Coleta erros/exceções
│   │
│   ├── Storage/                     # Engines de armazenamento
│   │   └── SQLiteStorage.php       # Implementação SQLite
│   │
│   ├── Analyzers/                   # Análise de dados (futuro)
│   │
│   ├── Dashboard/                   # Controllers do dashboard (futuro)
│   │
│   ├── Alerts/                      # Sistema de alertas (futuro)
│   │
│   ├── Integrations/                # Integrações com frameworks
│   │   ├── Laravel/                # Laravel (futuro)
│   │   ├── Symfony/                # Symfony (futuro)
│   │   └── WordPress/              # WordPress (futuro)
│   │
│   ├── Support/                     # Classes auxiliares
│   │   └── Config.php              # Gerenciador de configuração
│   │
│   ├── Contracts/                   # Interfaces
│   │   ├── StorageInterface.php    # Interface de storage
│   │   └── CollectorInterface.php  # Interface de collector
│   │
│   └── Monitor.php                  # Classe principal
│
├── tests/                            # Testes
│   ├── Unit/                        # Testes unitários
│   │   └── MonitorTest.php         # Testes do Monitor
│   ├── Integration/                 # Testes de integração (futuro)
│   └── Fixtures/                    # Dados de teste (futuro)
│
├── .gitignore                        # Arquivos ignorados pelo Git
├── .php-cs-fixer.php                # Configuração PHP CS Fixer
├── CHANGELOG.md                      # Log de mudanças
├── composer.json                     # Dependências PHP
├── CONTRIBUTING.md                   # Guia de contribuição
├── LICENSE                           # Licença MIT
├── phpstan.neon                      # Configuração PHPStan
├── phpunit.xml                       # Configuração PHPUnit
├── QUICKSTART.md                     # Guia de início rápido
├── README.md                         # README principal
└── PROJECT_STRUCTURE.md              # Este arquivo
```

## Convenções

### Nomenclatura
- **Classes**: PascalCase (ex: `RequestCollector.php`)
- **Métodos**: camelCase (ex: `getCollector()`)
- **Constantes**: UPPER_CASE (ex: `DEFAULT_THRESHOLD`)
- **Propriedades**: camelCase (ex: `$startTime`)

### Namespaces
```
PHPHealth\Monitor\                    # Base
├── Collectors\                       # Coletores
├── Storage\                          # Storage
├── Analyzers\                        # Analisadores
├── Dashboard\                        # Dashboard
├── Alerts\                           # Alertas
├── Integrations\{Framework}\         # Integrações
├── Support\                          # Classes auxiliares
└── Contracts\                        # Interfaces
```

### Padrões de Código
- **PSR-12**: Coding Style
- **PSR-4**: Autoloading
- **Strict Types**: Sempre declare `declare(strict_types=1);`
- **Type Hints**: Sempre use type hints
- **Return Types**: Sempre declare tipos de retorno
- **DocBlocks**: Documente métodos públicos e complexos

### Testes
- **Cobertura mínima**: 80%
- **Nomenclatura**: `test_should_do_something()`
- **Arrange-Act-Assert**: Estrutura padrão
- **Testes unitários**: src/Collectors, src/Storage, etc
- **Testes de integração**: Fluxos completos

## Workflow de Desenvolvimento

### 1. Nova Feature
```bash
git checkout -b feature/minha-feature
# Desenvolva
composer test
composer cs-fix
git commit -m "feat: adiciona minha feature"
git push origin feature/minha-feature
# Abra PR
```

### 2. Correção de Bug
```bash
git checkout -b fix/meu-bug
# Corrija
composer test
composer cs-fix
git commit -m "fix: corrige meu bug"
git push origin fix/meu-bug
# Abra PR
```

### 3. Antes de Commitar
```bash
composer test          # Roda testes
composer phpstan       # Análise estática
composer cs-check      # Verifica padrão de código
composer cs-fix        # Corrige padrão de código
```

## Próximos Passos de Desenvolvimento

### Fase 1 - MVP (Atual)
- [x] Estrutura base
- [x] RequestCollector
- [x] DatabaseCollector
- [x] ErrorCollector
- [x] SQLiteStorage
- [ ] Dashboard funcional
- [ ] Testes completos

### Fase 2 - Expansão
- [ ] MySQL/PostgreSQL Storage
- [ ] Alertas (Email, Slack)
- [ ] Integração Laravel
- [ ] API REST
- [ ] Health Checks

### Fase 3 - Avançado
- [ ] Integração Symfony
- [ ] Plugin WordPress
- [ ] Cache monitoring
- [ ] Async processing
- [ ] Advanced analytics

## Métricas de Qualidade

### Objetivos
- **Cobertura de Testes**: ≥ 80%
- **PHPStan Level**: 8
- **Bugs Críticos**: 0
- **Performance Overhead**: < 5ms
- **Documentação**: 100% das APIs públicas

### CI/CD
- Testes em PHP 7.4, 8.0, 8.1, 8.2, 8.3
- Análise estática obrigatória
- Code style obrigatório
- Cobertura de testes reportada

## Dependências

### Produção (Required)
- PHP >= 7.4
- ext-pdo
- ext-json

### Desenvolvimento (Dev)
- phpunit/phpunit
- phpstan/phpstan
- squizlabs/php_codesniffer
- friendsofphp/php-cs-fixer

### Opcionais (Sugeridas)
- ext-redis (para cache monitoring)
- ext-memcached (para cache monitoring)

## Contribuindo

Veja [CONTRIBUTING.md](CONTRIBUTING.md) para guia completo de contribuição.
