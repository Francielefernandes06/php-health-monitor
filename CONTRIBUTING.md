# Contribuindo para PHP Health Monitor

Primeiramente, obrigado por considerar contribuir para o PHP Health Monitor! É graças a pessoas como você que este projeto pode ajudar a comunidade PHP.

## 🤝 Como Posso Contribuir?

### Reportando Bugs

Antes de criar um issue sobre um bug:

1. **Verifique** se o bug já foi reportado
2. **Colete** informações relevantes:
   - Versão do PHP
   - Versão do PHP Health Monitor
   - Sistema operacional
   - Framework (se aplicável)
   - Passos para reproduzir
   - Comportamento esperado vs observado

### Sugerindo Melhorias

Adoramos receber sugestões! Abra um issue com:

- Descrição clara da melhoria
- Justificativa (por que seria útil?)
- Exemplos de uso
- Possíveis implementações (opcional)

### Pull Requests

#### Processo

1. Fork o repositório
2. Clone seu fork: `git clone https://github.com/seu-usuario/php-health-monitor.git`
3. Crie uma branch: `git checkout -b feature/minha-feature`
4. Faça suas alterações
5. Adicione testes (muito importante!)
6. Execute os testes: `composer test`
7. Execute o linter: `composer cs-check`
8. Commit: `git commit -m "feat: adiciona minha feature"`
9. Push: `git push origin feature/minha-feature`
10. Abra um Pull Request

#### Padrões de Código

Seguimos o PSR-12. Para verificar:

```bash
composer cs-check
```

Para corrigir automaticamente:

```bash
composer cs-fix
```

#### Padrões de Commit

Usamos [Conventional Commits](https://www.conventionalcommits.org/):

- `feat:` nova funcionalidade
- `fix:` correção de bug
- `docs:` mudanças na documentação
- `style:` formatação, ponto e vírgula, etc
- `refactor:` refatoração de código
- `test:` adição de testes
- `chore:` manutenção

Exemplos:
```
feat: adiciona suporte para PostgreSQL
fix: corrige memory leak no collector
docs: atualiza guia de instalação
```

#### Testes

Todo código novo **deve** incluir testes. Nosso objetivo é manter cobertura > 80%.

```bash
# Rodar todos os testes
composer test

# Rodar com cobertura
composer test-coverage
```

#### Documentação

Se sua mudança afeta a API pública ou adiciona nova funcionalidade:

1. Atualize o README.md
2. Adicione exemplos em `docs/examples/`
3. Atualize a documentação técnica em `docs/`

## 🏗️ Estrutura do Projeto

```
php-health-monitor/
├── src/                    # Código fonte
│   ├── Collectors/        # Coletores de métricas
│   ├── Storage/           # Engines de armazenamento
│   ├── Analyzers/         # Análise de dados
│   ├── Dashboard/         # Interface web
│   ├── Alerts/            # Sistema de alertas
│   └── Integrations/      # Integrações com frameworks
├── tests/                 # Testes unitários e integração
├── config/                # Arquivos de configuração
├── database/              # Migrations
├── docs/                  # Documentação
└── public/                # Assets públicos
```

## 🎯 Áreas que Precisam de Ajuda

- [ ] Suporte para mais bancos de dados
- [ ] Melhorias no dashboard
- [ ] Tradução da documentação
- [ ] Integrações com frameworks
- [ ] Otimizações de performance
- [ ] Mais collectors (Redis, API calls, etc)

## 💬 Código de Conduta

### Nossa Promessa

Estamos comprometidos em tornar a participação neste projeto uma experiência livre de assédio para todos, independentemente de idade, tamanho corporal, deficiência, etnia, identidade de gênero, nível de experiência, nacionalidade, aparência pessoal, raça, religião ou identidade e orientação sexual.

### Nossos Padrões

Exemplos de comportamento que contribuem para criar um ambiente positivo:

- Uso de linguagem acolhedora e inclusiva
- Respeito por diferentes pontos de vista e experiências
- Aceitação graciosa de críticas construtivas
- Foco no que é melhor para a comunidade
- Empatia com outros membros da comunidade

Exemplos de comportamento inaceitável:

- Uso de linguagem ou imagens sexualizadas
- Trolling, insultos ou comentários depreciativos
- Assédio público ou privado
- Publicar informações privadas de terceiros sem permissão
- Outras condutas consideradas inadequadas em um ambiente profissional

### Aplicação

Casos de comportamento abusivo, de assédio ou inaceitável podem ser reportados para [francielefernandes126@gmail.com]. Todas as reclamações serão revisadas e investigadas.

## 📚 Recursos Úteis

- [PSR-12: Coding Style Guide](https://www.php-fig.org/psr/psr-12/)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Conventional Commits](https://www.conventionalcommits.org/)

## 🙏 Agradecimentos

Seus contribuidores:

<!-- Será preenchido automaticamente -->

## ❓ Dúvidas?

Sinta-se à vontade para:

- Abrir uma [discussão](https://github.com/Francielefernandes06/php-health-monitor/discussions)
- Enviar email para [francielefernandes126@gmail.com]
- Perguntar no issue que está trabalhando

---

Obrigado novamente por sua contribuição! 🎉
