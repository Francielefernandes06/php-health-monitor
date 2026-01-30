#!/bin/bash

# Script de setup para desenvolvimento do PHP Health Monitor

echo "🏥 PHP Health Monitor - Setup de Desenvolvimento"
echo "================================================"
echo ""

# Verifica se o Composer está instalado
if ! command -v composer &> /dev/null; then
    echo "❌ Composer não encontrado. Por favor, instale o Composer primeiro."
    echo "   Visite: https://getcomposer.org/download/"
    exit 1
fi

echo "✅ Composer encontrado"

# Verifica versão do PHP
PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo "✅ PHP $PHP_VERSION"

# Instala dependências
echo ""
echo "📦 Instalando dependências..."
composer install

if [ $? -ne 0 ]; then
    echo "❌ Falha ao instalar dependências"
    exit 1
fi

echo "✅ Dependências instaladas"

# Cria diretórios necessários
echo ""
echo "📁 Criando diretórios..."
mkdir -p storage/logs
mkdir -p storage/cache
mkdir -p coverage
mkdir -p build

echo "✅ Diretórios criados"

# Cria arquivo .env de exemplo
if [ ! -f .env ]; then
    echo ""
    echo "📝 Criando arquivo .env..."
    cat > .env << 'EOF'
# PHP Health Monitor - Configuração Local

HEALTH_MONITOR_STORAGE_DRIVER=sqlite
HEALTH_MONITOR_DB_PATH=./storage/health-monitor.db
HEALTH_MONITOR_CLEANUP_DAYS=7

HEALTH_MONITOR_ALERTS_ENABLED=false
HEALTH_MONITOR_ALERT_EMAIL=dev@example.com

HEALTH_MONITOR_DASHBOARD_ENABLED=true
HEALTH_MONITOR_DASHBOARD_PATH=/health-monitor
HEALTH_MONITOR_USERNAME=admin
HEALTH_MONITOR_PASSWORD=admin

# Altere estas configurações para produção!
EOF
    echo "✅ Arquivo .env criado"
else
    echo "ℹ️  Arquivo .env já existe, não sobrescrevendo"
fi

# Executa testes
echo ""
echo "🧪 Executando testes..."
composer test

if [ $? -ne 0 ]; then
    echo "⚠️  Alguns testes falharam (isso é esperado no início do desenvolvimento)"
else
    echo "✅ Todos os testes passaram!"
fi

# Executa verificações de código
echo ""
echo "🔍 Verificando padrão de código..."
composer cs-check

if [ $? -ne 0 ]; then
    echo "⚠️  Algumas violações de código encontradas"
    echo "   Execute 'composer cs-fix' para corrigir automaticamente"
else
    echo "✅ Código está no padrão!"
fi

# Executa análise estática
echo ""
echo "🔬 Executando análise estática (PHPStan)..."
composer phpstan

if [ $? -ne 0 ]; then
    echo "⚠️  PHPStan encontrou alguns problemas"
else
    echo "✅ Análise estática passou!"
fi

echo ""
echo "================================================"
echo "✅ Setup completo!"
echo ""
echo "Próximos passos:"
echo "  1. Execute os testes: composer test"
echo "  2. Inicie o desenvolvimento: git checkout -b feature/sua-feature"
echo "  3. Leia CONTRIBUTING.md para guias de desenvolvimento"
echo ""
echo "Comandos úteis:"
echo "  composer test          - Executa testes"
echo "  composer test-coverage - Testes com cobertura"
echo "  composer phpstan       - Análise estática"
echo "  composer cs-check      - Verifica padrão de código"
echo "  composer cs-fix        - Corrige padrão de código"
echo ""
echo "Documentação:"
echo "  README.md              - Visão geral do projeto"
echo "  QUICKSTART.md          - Início rápido"
echo "  CONTRIBUTING.md        - Guia de contribuição"
echo "  docs/README.md         - Documentação completa"
echo ""
echo "Bom desenvolvimento! 🚀"
