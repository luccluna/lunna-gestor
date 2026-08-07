# Lunna Gestor — Instruções para agentes

## Projeto

Sistema administrativo web para a Lunna Vidraçaria.

O sistema gerencia o fluxo comercial, operacional e financeiro da empresa:

Cliente
→ Orçamento
→ Aprovação
→ Pedido
→ Agenda
→ Instalação
→ Pagamento
→ Finalização

---

## Stack obrigatória

- PHP 8.2+
- CodeIgniter 4
- MySQL/MariaDB
- Bootstrap 5
- JavaScript puro
- Dompdf

Não trocar a stack sem autorização explícita.

Não adicionar frameworks JavaScript como React, Vue ou Angular sem autorização.

---

## Ambiente local

Projeto:

C:\xampp\htdocs\lunna-gestor

Servidor de desenvolvimento:

php spark serve

URL:

http://localhost:8080

Banco:

lunna_gestor

Ambiente:

XAMPP no Windows

---

## Estado atual do sistema

O sistema já possui:

- autenticação por banco de dados;
- senhas com password_hash e password_verify;
- usuários;
- perfis de acesso;
- clientes;
- categorias de produtos e serviços;
- produtos e serviços;
- orçamentos;
- itens dinâmicos de orçamento;
- cálculos por m²;
- cálculos por metro linear;
- cálculos por unidade;
- cálculos por serviço fechado;
- PDF de orçamento com Dompdf;
- aprovação de orçamento;
- conversão de orçamento em pedido;
- pedidos;
- histórico de status de pedidos;
- agenda;
- integração entre agenda e pedido;
- pagamentos;
- pagamentos pendentes;
- pagamentos atrasados;
- pagamentos pagos;
- pagamentos cancelados;
- resumo financeiro por pedido;
- dashboard com dados reais;
- permissões por módulo;
- permissões por ação;
- proteção de controllers;
- proteção de botões nas views;
- ações críticas usando POST;
- proteção CSRF.

---

## Perfis existentes

- administrador
- gerente
- vendedor
- financeiro
- medidor
- instalador

---

## Fonte de verdade das permissões

As permissões estão centralizadas em:

app/Helpers/permissao_helper.php

Nunca confiar apenas em esconder botões nas views.

Toda ação protegida deve ser validada também no controller.

Exemplos:

temPermissao('pedidos')

temAcao('pedidos', 'visualizar')

temAcao('pedidos', 'alterar_status')

temAcao('pedidos', 'excluir')

bloquearSemPermissao('pedidos', 'excluir')

---

## Regra de segurança crítica

Acesso ao módulo não significa permissão para alterar dados.

Exemplo:

O instalador pode visualizar pedidos, mas não pode:

- excluir pedidos;
- alterar livremente o status;
- registrar pagamentos;
- acessar usuários.

---

## Ações críticas

Ações que alteram dados não devem usar GET.

Usar POST para:

- excluir;
- aprovar orçamento;
- concluir agenda;
- marcar pagamento como pago;
- outras ações destrutivas ou de mudança de estado.

Formulários POST devem usar:

<?= csrf_field() ?>

---

## Banco de dados

Principais tabelas:

- usuarios
- clientes
- categorias_servicos
- produtos_servicos
- orcamentos
- orcamento_itens
- pedidos
- pedido_status_historico
- agenda
- pagamentos

---

## Dados de teste

A massa de teste usa:

[TESTE]

nos nomes e descrições.

Os usuários fictícios usam:

@teste.com

Nunca apagar registros reais usando filtros genéricos.

Antes de qualquer limpeza, conferir exatamente os registros afetados.

---

## Histórico importante

O projeto já sofreu anteriormente corrupção do MySQL/InnoDB no ambiente XAMPP.

O banco precisou ser reconstruído.

Portanto:

- não executar alterações destrutivas sem backup;
- não remover arquivos internos do MySQL;
- não alterar tablespaces manualmente;
- antes de mudanças estruturais, exportar o banco.

---

## Regras de trabalho

Antes de alterar código:

1. Ler integralmente os arquivos envolvidos.
2. Entender o fluxo atual.
3. Verificar controllers, models, views e rotas relacionadas.
4. Apresentar um plano curto.
5. Fazer mudanças pequenas e focadas.
6. Executar validações.
7. Informar os arquivos alterados.
8. Informar qualquer alteração necessária no banco.

---

## Regras de código

- Preservar o padrão atual do projeto.
- Usar português nos nomes já estabelecidos em português.
- Não reescrever módulos inteiros sem necessidade.
- Não duplicar lógica existente.
- Reutilizar Models existentes.
- Reutilizar o helper de permissões.
- Não remover validações de acesso.
- Não remover csrf_field().
- Não criar dependências sem necessidade.
- Não alterar a stack sem autorização.

---

## Git

Antes de mudanças grandes:

- conferir git status;
- criar commit da versão estável;
- revisar o diff após alterações.

Nunca versionar:

.env

Nunca expor:

- senhas;
- credenciais;
- dados reais de clientes.

---

## Regra para banco de dados

Antes de:

- apagar dados;
- alterar tabelas;
- adicionar colunas;
- mudar relacionamentos;
- executar operações em massa;

o agente deve:

1. explicar o impacto;
2. informar se há risco;
3. fornecer SQL claro;
4. recomendar backup;
5. preferir alterações reversíveis.

---

## Prioridade atual

O sistema está funcional e as permissões foram testadas com sucesso.

Próximas prioridades:

1. backup e restauração;
2. documentação do banco;
3. migrations;
4. limpeza controlada da massa de teste;
5. preparação para produção;
6. relatórios;
7. melhorias visuais finais.