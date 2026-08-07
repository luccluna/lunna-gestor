# Numeros sequenciais

## Formato preservado

- Orcamentos: `ORC-ANO-0001`
- Pedidos: `PED-ANO-0001`

## Risco identificado

O sistema gerava o proximo numero consultando o ultimo registro do ano e somando 1.

Em uso concorrente, duas requisicoes poderiam ler o mesmo ultimo numero antes de uma delas gravar. Nesse caso, ambas tentariam inserir o mesmo `numero`.

## Protecao existente

O dump e a migration possuem UNIQUE em:

- `orcamentos.numero`;
- `pedidos.numero`.

Isso impede duplicidade no banco.

## Ajuste aplicado

O controller de orcamentos agora tenta inserir com numero gerado e, se o banco retornar erro de duplicidade (`1062`), gera novamente e tenta outra vez.

Foram aplicadas ate 3 tentativas para:

- criacao de orcamento;
- criacao de pedido a partir da aprovacao do orcamento.

## Limite da solucao

Essa abordagem reduz o risco sem criar nova tabela de sequencias.

Para volume alto de uso simultaneo, a solucao mais robusta seria uma tabela de sequencias por ano e tipo, atualizada em transacao com bloqueio de linha. Isso exigiria mudanca estrutural no banco e deve ser feito somente com backup e migration testada.
