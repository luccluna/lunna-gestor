# Repositorio Git

## Objetivo

Manter o codigo do Lunna Gestor versionado com historico de alteracoes, permitindo corrigir defeitos, criar melhorias e publicar novas versoes com mais seguranca.

## O que fica no Git

- Codigo PHP do sistema.
- Views, controllers, models, helpers, commands e migrations.
- Assets publicos do site e do sistema.
- Documentacao tecnica.
- Arquivos de exemplo, como `.env.example`.

## O que nao fica no Git

- `.env` com credenciais reais.
- Dumps SQL reais em `database/*.sql`.
- Backups em `writable/backups`.
- Logs, sessoes, cache e debugbar.
- Pasta `vendor`, que deve ser recriada com Composer.
- Briefings originais com dados de contato reais do cliente.

## Fluxo recomendado

1. Fazer alteracoes primeiro no ambiente local.
2. Testar login, telas afetadas e comandos relacionados.
3. Conferir `git status`.
4. Criar commit com uma mensagem clara.
5. Enviar para o repositorio remoto privado.
6. Publicar na hospedagem somente uma versao aprovada.

## Mudancas com banco

Quando houver migration ou SQL:

1. Fazer backup do banco antes.
2. Testar localmente.
3. Subir codigo.
4. Rodar migration no servidor.
5. Validar telas principais.

## Comandos uteis

```bash
git status
git add .
git commit -m "Mensagem objetiva"
git remote -v
git push origin main
```

## Primeiro commit local

O primeiro commit deve representar a versao base estavel antes de conectar ao GitHub.
