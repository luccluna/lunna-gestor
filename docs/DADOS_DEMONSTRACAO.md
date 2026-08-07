# Dados de demonstracao

## Objetivo

Esta massa de dados foi criada para apresentar as principais funcionalidades do Lunna Gestor sem usar dados reais.

Todos os registros principais usam:

- prefixo `[TESTE DEMO]`;
- e-mails `@teste.com`;
- numeros `ORC-DEMO-*` e `PED-DEMO-*`.

## Como recriar ou atualizar

Execute na raiz do projeto:

```bash
C:\xampp\php\php.exe spark demo:criar-dados
```

O comando atualiza registros de demonstracao conhecidos e nao apaga dados reais.

Antes da primeira execucao foi criado backup em:

```text
C:\xampp\htdocs\lunna-gestor\writable\backups\lunna_gestor_backup_20260709_212518_antes_dados_demo.sql
```

## Usuarios de demonstracao

Senha para todos:

```text
Teste@123
```

- `demo.admin@teste.com` - administrador
- `demo.gerente@teste.com` - gerente
- `demo.vendedor@teste.com` - vendedor
- `demo.financeiro@teste.com` - financeiro
- `demo.medidor@teste.com` - medidor
- `demo.instalador@teste.com` - instalador

## Roteiro sugerido de apresentacao

1. Acessar o Dashboard e mostrar indicadores gerais.
2. Abrir Clientes e mostrar clientes ficticios.
3. Abrir Produtos e Servicos e mostrar itens `[TESTE DEMO]`.
4. Abrir Estoque e mostrar materiais, saldo e alerta de baixo estoque.
5. Abrir Orcamentos e mostrar:
   - `ORC-DEMO-2026-0001` aprovado;
   - `ORC-DEMO-2026-0002` em negociacao;
   - `ORC-DEMO-2026-0003` aguardando medicao.
6. Abrir Pedidos e mostrar `PED-DEMO-2026-0001` em producao.
7. Abrir Agenda e mostrar medicao e instalacao agendadas.
8. Abrir Pagamentos e mostrar entrada paga e saldo final pendente.

## Observacoes

- A massa e ficticia e serve somente para demonstracao.
- Nao usar estes dados como dados reais de producao.
- Nao manter usuarios `@teste.com` ativos em ambiente de producao.
