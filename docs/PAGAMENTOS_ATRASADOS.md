# Rotina de pagamentos atrasados

## Objetivo

Atualizar pagamentos vencidos de forma controlada, sem executar alteracoes durante o carregamento de paginas GET.

## Comando manual

Execute na raiz do projeto:

```bash
php spark pagamentos:atualizar-atrasados
```

Em ambiente XAMPP, caso o PHP do sistema nao tenha as extensoes do MySQL, use o PHP do XAMPP:

```bash
C:\xampp\php\php.exe spark pagamentos:atualizar-atrasados
```

## Regra aplicada

A rotina atualiza somente registros da tabela `pagamentos` que atendem a todos os criterios:

- `ativo = 1`;
- `status = pendente`;
- `data_vencimento` anterior a data atual.

Pagamentos pagos, cancelados, ja atrasados ou inativos nao sao alterados.

## Idempotencia

O comando pode ser executado varias vezes. Depois que um pagamento muda de `pendente` para `atrasado`, ele deixa de atender ao filtro da proxima execucao.

## Agendamento futuro

Ainda nao ha cron ou tarefa do Windows configurada.

Quando o sistema estiver em servidor definitivo, a rotina pode ser agendada para rodar uma vez por dia, por exemplo:

- Windows: Agendador de Tarefas chamando o PHP do XAMPP ou PHP do servidor.
- Linux: cron chamando `php spark pagamentos:atualizar-atrasados`.

Antes de agendar, confirmar o caminho correto do PHP, permissao de escrita em `writable/` e acesso ao banco `lunna_gestor`.
