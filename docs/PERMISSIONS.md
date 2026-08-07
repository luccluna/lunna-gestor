# Matriz de permissões — Lunna Gestor

A fonte de verdade do sistema é:

app/Helpers/permissao_helper.php

Este documento é apenas referência.

---

## Administrador

Acesso total.

Pode:

- visualizar;
- criar;
- editar;
- alterar status;
- concluir;
- marcar pagamento como pago;
- excluir;
- administrar usuários.

---

## Gerente

Pode acessar:

- dashboard;
- clientes;
- produtos e serviços;
- estoque;
- orçamentos;
- pedidos;
- agenda;
- pagamentos.

Não acessa:

- usuários.

Pode executar operações gerenciais.

Estoque:

- visualizar;
- criar material;
- editar material;
- registrar entradas e saidas.

Exclusões críticas permanecem restritas quando não liberadas no helper.

---

## Vendedor

Pode acessar:

- dashboard;
- clientes;
- orçamentos;
- pedidos.

Clientes:

- visualizar;
- criar;
- editar.

Orçamentos:

- visualizar;
- criar;
- editar;
- gerar PDF.

Pedidos:

- visualizar.

Não pode:

- excluir pedidos;
- alterar status de pedidos;
- acessar agenda;
- acessar pagamentos;
- acessar usuários.

---

## Financeiro

Pode acessar:

- dashboard;
- pedidos;
- pagamentos.

Pedidos:

- visualizar.

Pagamentos:

- visualizar;
- criar;
- editar;
- marcar como pago.

Não pode:

- excluir pagamentos;
- alterar pedidos;
- acessar agenda;
- acessar usuários.

---

## Medidor

Pode acessar:

- dashboard;
- clientes;
- pedidos;
- agenda.

Clientes:

- visualizar.

Pedidos:

- visualizar.

Agenda:

- visualizar;
- criar;
- editar;
- concluir.

Não pode:

- excluir agenda;
- alterar status de pedido;
- acessar pagamentos;
- acessar usuários.

---

## Instalador

Pode acessar:

- dashboard;
- pedidos;
- agenda.

Pedidos:

- visualizar.

Agenda:

- visualizar;
- concluir.

Não pode:

- criar agendamento;
- editar agendamento;
- excluir agendamento;
- excluir pedido;
- alterar status do pedido;
- registrar pagamento;
- acessar usuários.

---

## Regra crítica

Esconder botão não é segurança.

Toda permissão precisa ser verificada também no controller.

Exemplo:

```php
if ($redirect = bloquearSemPermissao('pedidos', 'excluir')) {
    return $redirect;
}
