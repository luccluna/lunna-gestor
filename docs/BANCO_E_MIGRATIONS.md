# Banco de dados e migrations

## Fonte analisada

- `database/lunna_gestor.sql`
- Models em `app/Models`

## Tabelas

- `usuarios`: usuarios do sistema, perfis, hash de senha, ativo, ultimo acesso, timestamps e soft delete.
- `clientes`: cadastro de cliente, contato, documentos, endereco, ativo, timestamps e soft delete.
- `categorias_servicos`: categorias de produtos/servicos, ativo, timestamps e soft delete.
- `produtos_servicos`: produtos e servicos, categoria opcional, tipo, unidade de calculo, valores, ativo, timestamps e soft delete.
- `orcamentos`: cabecalho comercial, cliente, usuario, status, valores, observacoes, ativo, timestamps e soft delete.
- `orcamento_itens`: itens do orcamento, produto opcional, medidas, unidade, valores, timestamps e soft delete.
- `pedidos`: pedido gerado de orcamento, cliente, usuario, status operacional, valores, ativo, timestamps e soft delete.
- `pedido_status_historico`: historico de status do pedido, sem soft delete e sem timestamps automaticos.
- `agenda`: compromissos, cliente, pedido opcional, tipo, status, endereco, ativo, timestamps e soft delete.
- `pagamentos`: pagamentos de pedido/cliente, tipo, forma, status, valor, datas, ativo, timestamps e soft delete.
- `estoque_materiais`: cadastro de materiais fisicos, produto vinculado opcional, fornecedor, localizacao, saldo, estoque minimo, custo, ativo, timestamps e soft delete.
- `estoque_movimentacoes`: historico de entradas e saidas de estoque, material, pedido opcional, usuario, documento/nota, quantidade, saldo anterior e saldo posterior.

## Indices e constraints principais

- `usuarios.email`: unique.
- `orcamentos.numero`: unique.
- `pedidos.numero`: unique.
- FKs:
  - `produtos_servicos.categoria_id` -> `categorias_servicos.id` com `ON DELETE SET NULL`;
  - `orcamentos.cliente_id` -> `clientes.id`;
  - `orcamento_itens.orcamento_id` -> `orcamentos.id` com `ON DELETE CASCADE`;
  - `orcamento_itens.produto_servico_id` -> `produtos_servicos.id` com `ON DELETE SET NULL`;
  - `pedidos.cliente_id` -> `clientes.id`;
  - `pedidos.orcamento_id` -> `orcamentos.id`;
  - `pedido_status_historico.pedido_id` -> `pedidos.id` com `ON DELETE CASCADE`;
  - `agenda.cliente_id` -> `clientes.id`;
  - `agenda.pedido_id` -> `pedidos.id` com `ON DELETE SET NULL`;
- `pagamentos.cliente_id` -> `clientes.id`;
- `pagamentos.pedido_id` -> `pedidos.id`.
- `estoque_materiais.produto_servico_id` -> `produtos_servicos.id` com `ON DELETE SET NULL`;
- `estoque_movimentacoes.material_id` -> `estoque_materiais.id` com `ON DELETE CASCADE`;
- `estoque_movimentacoes.pedido_id` -> `pedidos.id` com `ON DELETE SET NULL`.

## Ordem das migrations

A migration inicial criada em:

`app/Database/Migrations/2026-07-04-010000_CreateLunnaGestorSchema.php`

cria as tabelas nesta ordem:

1. `usuarios`
2. `clientes`
3. `categorias_servicos`
4. `produtos_servicos`
5. `orcamentos`
6. `orcamento_itens`
7. `pedidos`
8. `pedido_status_historico`
9. `agenda`
10. `pagamentos`

O `down()` remove em ordem reversa para respeitar FKs.

A migration de estoque criada em:

`app/Database/Migrations/2026-07-09-010000_CreateEstoqueSchema.php`

adiciona:

1. `estoque_materiais`
2. `estoque_movimentacoes`

## Riscos

- A migration ainda nao foi executada em banco vazio.
- O dump atual contem dados; a migration cria somente estrutura, nao seeds.
- Antes de executar em qualquer ambiente, fazer backup e usar banco vazio de teste.
- A migration preserva o formato de ENUMs, FKs, soft deletes e timestamps observados no dump, mas deve ser comparada em banco vazio antes de producao.

## Como testar futuramente

1. Criar um banco vazio separado, por exemplo `lunna_gestor_migration_test`.
2. Configurar temporariamente o `.env` para esse banco.
3. Executar `php spark migrate`.
4. Conferir tabelas, indices, FKs e ENUMs.
5. Descartar o banco de teste depois da validacao.

Nao executar `migrate` no banco atual sem aprovacao e backup.
