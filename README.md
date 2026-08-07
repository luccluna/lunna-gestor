# Lunna Gestor

Sistema administrativo para vidraçarias, desenvolvido em PHP 8.2+, CodeIgniter 4, MySQL/MariaDB, Bootstrap 5, JavaScript puro e Dompdf.

O sistema cobre o fluxo operacional:

Cliente → Orçamento → Aprovação → Pedido → Agenda → Instalação → Pagamento → Finalização

## Módulos

- Autenticação e perfis de acesso.
- Usuários.
- Clientes.
- Categorias, produtos e serviços.
- Orçamentos com itens dinâmicos e PDF.
- Pedidos com histórico de status.
- Agenda vinculada a pedidos.
- Pagamentos e resumo financeiro.
- Dashboard com indicadores reais.
- Checklist administrativo de primeiros passos.

## Ambiente local

```bash
composer install
php spark serve
```

URL local:

```text
http://localhost:8080
```

Banco padrão:

```text
lunna_gestor
```

## Hospedagem

Consulte `docs/HOSPEDAGEM_PRODUCAO.md` antes de publicar.

Pontos essenciais:

- Copiar `.env.example` para `.env`.
- Configurar `CI_ENVIRONMENT = production`.
- Apontar o domínio para a pasta `public`.
- Configurar o banco em `database.default.*`.
- Garantir escrita na pasta `writable`.
- Agendar `php spark pagamentos:atualizar-atrasados`.
- Fazer backup antes de remover massa de teste.

## Segurança

- Não versionar `.env`.
- Não publicar dumps reais do banco.
- Não manter usuários `@teste.com` em produção.
- Não remover validações de permissão ou CSRF.
- Toda ação crítica deve usar POST.
