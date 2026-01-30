# Changelog

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Semantic Versioning](https://semver.org/lang/pt-BR/).

## [Unreleased]

### Planejado
- Suporte para PostgreSQL
- Health checks automáticos
- Alertas via Webhook
- API REST completa
- Integração com WordPress
- Monitoramento de cache (Redis/Memcached)

## [1.0.0-dev] - Em Desenvolvimento

### Adicionado
- Estrutura base do projeto
- Monitor principal com sistema de collectors
- RequestCollector para monitoramento de requisições HTTP
- DatabaseCollector para queries SQL
- ErrorCollector para erros e exceções
- SQLiteStorage para persistência de dados
- Sistema de configuração flexível
- Testes unitários básicos
- Documentação inicial
- Integração com GitHub Actions (CI)
- Padrões de código (PSR-12)
- Análise estática com PHPStan

### Filosofia
- Zero configuração para começar
- Overhead mínimo (< 5ms)
- Privacy-first (dados ficam na sua infraestrutura)
- Framework agnostic

## Tipos de Mudanças

- `Added` - para novas funcionalidades
- `Changed` - para mudanças em funcionalidades existentes
- `Deprecated` - para funcionalidades que serão removidas
- `Removed` - para funcionalidades removidas
- `Fixed` - para correções de bugs
- `Security` - para vulnerabilidades de segurança
