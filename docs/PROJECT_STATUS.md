# Estado atual — Lunna Gestor

## Resumo

O Lunna Gestor é um sistema administrativo desenvolvido para uma vidraçaria.

O sistema cobre o fluxo:

Cliente
→ Orçamento
→ Aprovação
→ Pedido
→ Agenda
→ Instalação
→ Pagamentos
→ Finalização

---

## Ambiente

Sistema operacional:

Windows

Ambiente:

XAMPP

Projeto:

C:\xampp\htdocs\lunna-gestor

Servidor:

php spark serve

URL:

http://localhost:8080

Banco:

lunna_gestor

---

## Stack

- PHP 8.2+
- CodeIgniter 4
- MySQL/MariaDB
- Bootstrap 5
- JavaScript puro
- Dompdf

---

## Módulos concluídos

### Autenticação

- login por banco de dados;
- logout;
- sessões;
- password_hash;
- password_verify;
- controle de usuário ativo;
- registro de último acesso.

### Usuários

Perfis:

- administrador;
- gerente;
- vendedor;
- financeiro;
- medidor;
- instalador.

Funcionalidades:

- criar;
- editar;
- alterar senha;
- ativar/desativar;
- excluir logicamente;
- impedir exclusão do próprio usuário logado.

### Clientes

- listagem;
- busca;
- cadastro;
- edição;
- exclusão lógica.

### Produtos e serviços

- categorias;
- produtos;
- serviços;
- tipos de cálculo;
- valores;
- custos;
- margem.

### Estoque

- cadastro de materiais;
- vínculo opcional com produto comercial;
- fornecedor;
- localização;
- saldo atual;
- estoque mínimo;
- alerta de baixo estoque;
- entrada de mercadorias;
- saída de mercadorias vinculada opcionalmente a pedido;
- documento/nota da movimentação;
- histórico de entradas e saídas.

### Orçamentos

- criação;
- cliente;
- itens dinâmicos;
- cálculo por m²;
- metro linear;
- unidade;
- serviço fechado;
- subtotal;
- desconto;
- total;
- status;
- PDF;
- aprovação.

### Pedidos

- geração a partir de orçamento aprovado;
- status operacional;
- histórico de status;
- integração com agenda;
- integração financeira.

### Agenda

Tipos:

- medição;
- instalação;
- manutenção;
- retorno;
- entrega;
- visita comercial.

Status:

- agendado;
- confirmado;
- em rota;
- em andamento;
- concluído;
- reagendado;
- cancelado.

### Pagamentos

Tipos:

- entrada;
- parcela;
- saldo final;
- pagamento único;
- outro.

Status:

- pendente;
- pago;
- atrasado;
- cancelado.

Formas:

- Pix;
- dinheiro;
- cartão de débito;
- cartão de crédito;
- boleto;
- transferência;
- cheque;
- outro.

### Dashboard

Indicadores reais:

- clientes cadastrados;
- orçamentos pendentes;
- aprovados no mês;
- pedidos em andamento;
- instalações da semana;
- pagamentos pendentes;
- pagamentos atrasados;
- materiais em baixo estoque;
- recebido no mês;
- valor a receber;
- faturamento previsto.

---

## Segurança já implementada

- autenticação por banco;
- senha com hash;
- sessão;
- permissões por perfil;
- permissões por ação;
- proteção no controller;
- proteção visual nas views;
- POST em ações críticas;
- CSRF.

---

## Permissões

A fonte de verdade é:

app/Helpers/permissao_helper.php

Existe diferença entre:

- visualizar;
- criar;
- editar;
- alterar status;
- concluir;
- marcar como pago;
- excluir.

---

## Testes concluídos

Perfis testados com sucesso:

- administrador;
- gerente;
- vendedor;
- financeiro;
- medidor;
- instalador.

Foram testados:

- menus;
- acesso direto por URL;
- permissões por módulo;
- permissões por ação;
- botões visíveis;
- botões ocultos;
- rotas POST;
- bloqueio de GET para ações críticas.

---

## Massa de teste

Existem registros identificados por:

[TESTE]

Existem usuários com:

@teste.com

A massa inclui:

- todos os status de orçamento;
- todos os status de pedido;
- todos os status de agenda;
- todos os status de pagamento;
- todos os perfis de usuário.

---

## Histórico técnico importante

Durante o desenvolvimento ocorreu corrupção do MySQL/InnoDB.

Erro anterior:

Table doesn't exist in engine

e problema de tablespace.

O ambiente foi recuperado reconstruindo a pasta data do MySQL e recriando o banco.

Por isso, alterações de banco devem sempre ser precedidas por backup.

---

## Estado atual

O sistema está funcional.

As permissões foram validadas.

Ações críticas foram convertidas de GET para POST.

A base atual deve ser considerada um ponto estável.

---

## Próximas etapas recomendadas

1. backup e restauração;
2. exportação organizada do banco;
3. migrations;
4. documentação de instalação;
5. limpeza dos dados de teste;
6. preparação para produção;
7. relatórios;
8. melhorias visuais.
