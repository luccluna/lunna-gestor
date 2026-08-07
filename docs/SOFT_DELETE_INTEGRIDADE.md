# Soft delete e integridade

## Escopo auditado

- clientes;
- produtos e servicos;
- orcamentos;
- pedidos;
- agenda;
- pagamentos;
- usuarios.

## Padrao atual

A maior parte dos Models usa:

- `useSoftDeletes = true`;
- `created_at`;
- `updated_at`;
- `deleted_at`.

Os controllers de exclusao tambem gravam `ativo = 0` antes de chamar `delete()`.

Na pratica, um registro removido fica:

- com `ativo = 0`;
- com `deleted_at` preenchido.

## Tabelas com soft delete

- `usuarios`
- `clientes`
- `categorias_servicos`
- `produtos_servicos`
- `orcamentos`
- `orcamento_itens`
- `pedidos`
- `agenda`
- `pagamentos`

## Tabela sem soft delete

- `pedido_status_historico`

Essa tabela preserva historico operacional e usa FK com `ON DELETE CASCADE` para o pedido.

## FKs relevantes

- `orcamento_itens.orcamento_id` usa `ON DELETE CASCADE`.
- `pedido_status_historico.pedido_id` usa `ON DELETE CASCADE`.
- `produtos_servicos.categoria_id`, `orcamento_itens.produto_servico_id` e `agenda.pedido_id` usam `ON DELETE SET NULL`.
- Clientes, orcamentos, pedidos e pagamentos usam FKs restritivas quando nao ha regra explicita de delete.

Como as exclusoes do sistema sao logicas, as FKs normalmente permanecem integras.

## Riscos observados

- Listagens usam `ativo = 1`, entao registros removidos somem das telas principais.
- Historicos podem depender de joins com registros inativos/deletados logicamente.
- Excluir logicamente um cliente, orcamento ou pedido nao apaga fisicamente o registro, mas pode dificultar acesso pela listagem comum.
- Como `ativo` e `deleted_at` duplicam a nocao de remocao, qualquer futura restauracao precisa reverter ambos.

## Diagnostico

Nao foi aplicada mudanca de estrategia.

O comportamento atual e coerente com exclusao logica e preservacao historica, desde que:

- telas de listagem continuem filtrando registros ativos;
- telas historicas busquem informacoes relacionadas sem depender de registros ainda ativos;
- restauracoes futuras tratem `ativo` e `deleted_at` juntos;
- exclusoes fisicas continuem proibidas sem backup e aprovacao.

## Recomendacoes futuras

- Criar telas ou relatórios administrativos para consultar removidos, se necessário.
- Definir uma regra formal para reativacao de registros.
- Evitar hard delete em dados comerciais/financeiros.
- Criar testes manuais de visualizacao de pedido/orcamento quando cliente relacionado estiver inativo.
