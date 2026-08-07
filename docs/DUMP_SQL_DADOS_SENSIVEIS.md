# Auditoria do dump SQL

Arquivo auditado:

`database/lunna_gestor.sql`

## Situacao

O dump original nao foi alterado, apagado nem sobrescrito.

O arquivo contem estrutura do banco e dados carregados para teste/desenvolvimento. Alguns registros parecem ficticios, mas ha dados que nao devem ser tratados como anonimos sem confirmacao.

## Dados sensiveis identificados

Foram encontrados:

- nomes de cliente e usuarios;
- e-mails, incluindo e-mails pessoais e corporativos;
- telefone e WhatsApp;
- CPF;
- CEP, rua, numero, complemento, bairro, cidade e estado;
- responsavel de agenda;
- hashes de senha em `usuarios.senha`;
- historico operacional e financeiro vinculado a cliente/pedido.

## Recomendacao de versionamento

O dump original deve ficar fora do Git.

O `.gitignore` foi ajustado para ignorar:

- `/database/*.sql`;
- `/database/*.sql.gz`;
- `/database/*.zip`.

Se algum dump ja tiver sido versionado em outro ambiente, ele deve ser removido do controle de versao com cuidado, sem apagar o arquivo local de backup.

## Estrategia para dump anonimizado

Criar um novo arquivo separado, por exemplo:

`database/lunna_gestor_exemplo_anonimizado.sql`

Esse arquivo deve:

- manter a mesma estrutura de tabelas, indices e FKs;
- substituir nomes por valores claramente ficticios com prefixo `[TESTE]`;
- trocar e-mails por dominios de teste, como `@teste.com` ou `example.com`;
- trocar CPF/CNPJ, telefones, WhatsApp, CEP e enderecos por dados ficticios;
- trocar hashes de senha por hashes gerados para senhas de teste conhecidas;
- manter variedade de status para orcamentos, pedidos, agenda e pagamentos;
- nao conter dados reais de clientes, usuarios ou atendimentos.

## Cuidados

Nao sobrescrever `database/lunna_gestor.sql` sem aprovacao explicita.

Antes de criar um dump anonimizado a partir do banco real, fazer backup do estado atual e conferir manualmente os registros exportados.
