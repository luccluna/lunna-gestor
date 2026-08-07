# Hospedagem e produção

Checklist para publicar o Lunna Gestor em uma hospedagem PHP com MySQL/MariaDB.

## Requisitos

- PHP 8.2 ou superior.
- MySQL ou MariaDB.
- Composer disponível no ambiente de preparação.
- Extensões PHP ativas: mysqli, intl, mbstring, json, curl, dom, gd, zip.
- Raiz pública do domínio apontando para a pasta `public`.

## Preparação dos arquivos

1. Enviar o projeto para a hospedagem sem versionar `.env`.
2. Executar `composer install --no-dev --optimize-autoloader`.
3. Copiar `.env.example` para `.env` no servidor.
4. Ajustar `CI_ENVIRONMENT = production`.
5. Ajustar `app.baseURL` para o domínio real.
6. Configurar os dados reais do banco em `database.default.*`.
7. Gerar uma chave segura para `encryption.key`.
8. Garantir permissão de escrita em `writable`.

## Banco de dados

1. Criar o banco `lunna_gestor` ou o nome definido no `.env`.
2. Importar o dump aprovado ou executar as migrations planejadas.
3. Confirmar que existem usuários administradores ativos.
4. Fazer backup antes de remover massa de teste.

## Rotina financeira

Configure no agendador da hospedagem uma execução diária:

```bash
php spark pagamentos:atualizar-atrasados
```

Essa rotina mantém pagamentos vencidos com status `atrasado`.

## Verificações antes de liberar

- Login funcionando em produção.
- CSRF ativo nos formulários.
- Orçamento com PDF gerando corretamente.
- Aprovação de orçamento criando pedido.
- Agenda vinculada ao pedido.
- Pagamento registrado e marcado como pago.
- Backup exportado e testado.

## Segurança

- Não publicar `.env`.
- Não deixar `CI_ENVIRONMENT = development` em produção.
- Não apontar o domínio para a raiz do projeto; apontar para `public`.
- Não manter usuários fictícios com `@teste.com`.
- Não manter dados com `[TESTE]` misturados aos dados reais.
- Manter backup externo à hospedagem.
