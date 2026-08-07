# Checklist de Testes e Auditoria — Lunna Gestor

## Objetivo

Este documento registra:

- funcionalidades já testadas manualmente;
- permissões já validadas;
- ações críticas já verificadas;
- problemas encontrados na auditoria técnica;
- correções ainda pendentes.

A fonte de verdade das permissões é:

`app/Helpers/permissao_helper.php`

O estado real do código sempre prevalece sobre esta documentação.

---

# 1. Autenticação

## Login

- [x] Login com usuário válido
- [x] Bloqueio de e-mail inválido
- [x] Bloqueio de senha inválida
- [x] Login consultando banco de dados
- [x] Senhas verificadas com `password_verify`
- [x] Senhas cadastradas com `password_hash`
- [x] Usuário inativo impedido de autenticar
- [x] Sessão criada após login
- [x] Último acesso registrado
- [x] Logout funcionando

## Segurança pendente

- [ ] Revisar se logout deve continuar em GET ou migrar para POST
- [ ] Criar teste automatizado de autenticação

---

# 2. Usuários

## Cadastro e manutenção

- [x] Listagem de usuários
- [x] Cadastro de usuário
- [x] E-mail duplicado tratado
- [x] E-mail de usuário removido detectado com `withDeleted()`
- [x] Edição de usuário
- [x] Alteração de senha
- [x] Confirmação de senha
- [x] Perfil de acesso
- [x] Usuário ativo/inativo
- [x] Exclusão lógica
- [x] Usuário não pode excluir a própria conta logada

## Permissões

- [x] Somente administrador acessa o módulo de usuários
- [x] Menu Usuários oculto para perfis sem acesso
- [x] Acesso direto a `/usuarios` bloqueado para perfil sem permissão

## Auditoria pendente

- [x] Revisar granularidade por ação no controller de usuários
- [ ] Criar testes automatizados do módulo

---

# 3. Perfis de acesso

Perfis existentes:

- administrador
- gerente
- vendedor
- financeiro
- medidor
- instalador

## Administrador

- [x] Vê todos os menus
- [x] Acessa todos os módulos
- [x] Pode executar todas as ações permitidas pelo sistema

## Gerente

- [x] Não vê Usuários
- [x] Não acessa `/usuarios` diretamente
- [x] Acessa módulos gerenciais
- [x] Pode alterar status de pedidos
- [x] Pode criar, editar e concluir agenda
- [x] Pode criar, editar e marcar pagamentos como pagos
- [x] Não recebe permissões críticas de exclusão quando elas não estão previstas no helper

## Vendedor

- [x] Vê Dashboard
- [x] Vê Clientes
- [x] Vê Orçamentos
- [x] Vê Pedidos
- [x] Não vê Produtos e Serviços
- [x] Não vê Agenda
- [x] Não vê Pagamentos
- [x] Não vê Usuários
- [x] Não altera status de pedido
- [x] Não exclui pedido
- [x] Não registra pagamento
- [x] Não agenda medição/instalação

## Financeiro

- [x] Vê Dashboard
- [x] Vê Pedidos
- [x] Vê Pagamentos
- [x] Cria pagamento
- [x] Edita pagamento
- [x] Marca pagamento como pago
- [x] Não exclui pagamento
- [x] Não acessa Agenda
- [x] Não acessa Usuários

## Medidor

- [x] Vê Dashboard
- [x] Vê Clientes
- [x] Vê Pedidos
- [x] Vê Agenda
- [x] Cria agendamento
- [x] Edita agendamento
- [x] Conclui agendamento permitido
- [x] Não exclui agendamento
- [x] Não altera livremente status de pedido
- [x] Não acessa Pagamentos

## Instalador

- [x] Vê Dashboard
- [x] Vê Pedidos
- [x] Vê Agenda
- [x] Conclui agendamento permitido
- [x] Não cria agendamento
- [x] Não edita agendamento
- [x] Não exclui agendamento
- [x] Não exclui pedido
- [x] Não altera status de pedido
- [x] Não registra pagamento
- [x] Não acessa Usuários

---

# 4. Clientes

## Funcionalidades

- [x] Listagem
- [x] Busca
- [x] Cadastro
- [x] Edição
- [x] Exclusão lógica

## Auditoria encontrada

- [x] `index` deve exigir `visualizar`
- [x] `novo` e `salvar` devem exigir `criar`
- [x] `editar` e `atualizar` devem exigir `editar`
- [x] `excluir` deve exigir `excluir`
- [x] Botões das views devem respeitar `temAcao()`
- [ ] Testar acesso direto às rotas de ação com perfis restritos

---

# 5. Produtos e Serviços

## Funcionalidades

- [x] Listagem
- [x] Cadastro
- [x] Edição
- [x] Exclusão lógica
- [x] Categorias
- [x] Produto
- [x] Serviço
- [x] Cálculo por m²
- [x] Cálculo por metro linear
- [x] Cálculo por unidade
- [x] Serviço fechado

## Auditoria encontrada

- [x] `index` deve exigir `visualizar`
- [x] `novo` e `salvar` devem exigir `criar`
- [x] `editar` e `atualizar` devem exigir `editar`
- [x] `excluir` deve exigir `excluir`
- [x] Botões das views devem respeitar `temAcao()`
- [ ] Testar rotas diretas com perfis restritos

---

# 6. Estoque

## Funcionalidades

- [x] Cadastro de materiais
- [x] Vínculo opcional com produto comercial
- [x] Controle de saldo atual
- [x] Estoque mínimo
- [x] Alerta de baixo estoque
- [x] Entrada de mercadorias
- [x] Saída de mercadorias
- [x] Saída vinculada opcionalmente a pedido
- [x] Histórico de movimentação
- [ ] Testar cadastro completo autenticado no navegador
- [ ] Testar entrada e saída com material real
- [ ] Testar bloqueio de saída maior que saldo

## Segurança HTTP

- [x] Cadastro usa POST
- [x] Atualização usa POST
- [x] Movimentação usa POST
- [x] Exclusão lógica usa POST
- [x] POST sem token CSRF foi rejeitado
- [x] Acesso sem login redireciona/bloqueia

---

# 7. Orçamentos

## Funcionalidades

- [x] Listagem
- [x] Busca
- [x] Filtro por status
- [x] Criação
- [x] Itens dinâmicos
- [x] Cálculo por m²
- [x] Cálculo por metro linear
- [x] Cálculo por unidade
- [x] Serviço fechado
- [x] Subtotal
- [x] Desconto
- [x] Total
- [x] Visualização
- [x] Geração de PDF
- [x] Aprovação
- [x] Conversão em pedido
- [x] Proteção contra pedido duplicado para o mesmo orçamento

## Auditoria encontrada

- [x] `index` e `ver` devem exigir `visualizar`
- [x] `novo` e `salvar` devem exigir `criar`
- [x] `pdf` deve exigir `pdf`
- [x] `aprovar` deve exigir `aprovar`
- [x] `excluir` deve exigir `excluir`
- [x] Ações de edição devem exigir `editar`, caso existam
- [x] Botões das views devem respeitar `temAcao()`
- [ ] Testar acesso direto às rotas

---

# 8. Pedidos

## Funcionalidades

- [x] Pedido gerado a partir de orçamento aprovado
- [x] Número sequencial
- [x] Status operacional
- [x] Histórico de status
- [x] Integração com agenda
- [x] Integração com pagamentos
- [x] Resumo financeiro

## Permissões

- [x] Visualização protegida por ação
- [x] Alteração de status protegida por ação
- [x] Exclusão protegida por ação
- [x] Botões protegidos por permissão
- [x] Vendedor não altera status
- [x] Instalador não altera status
- [x] Financeiro apenas visualiza pedido

## Segurança HTTP

- [x] Alteração de status usa POST
- [x] Exclusão usa POST
- [x] GET direto para exclusão retorna rota não encontrada

---

# 9. Agenda

## Tipos testados

- [x] Medição
- [x] Instalação
- [x] Manutenção
- [x] Retorno
- [x] Entrega
- [x] Visita comercial

## Status testados

- [x] Agendado
- [x] Confirmado
- [x] Em rota
- [x] Em andamento
- [x] Concluído
- [x] Reagendado
- [x] Cancelado

## Regras de botão Concluir

- [x] Agendado mostra Concluir
- [x] Confirmado mostra Concluir
- [x] Em rota mostra Concluir
- [x] Em andamento mostra Concluir
- [x] Reagendado mostra Concluir
- [x] Concluído não mostra Concluir
- [x] Cancelado não mostra Concluir

## Permissões

- [x] Visualizar protegida
- [x] Criar protegida
- [x] Editar protegida
- [x] Concluir protegida
- [x] Excluir protegida
- [x] Instalador pode concluir
- [x] Instalador não cria
- [x] Instalador não edita
- [x] Instalador não exclui

## Segurança HTTP

- [x] Concluir usa POST
- [x] Excluir usa POST
- [x] Formulários críticos possuem `csrf_field()`

---

# 10. Pagamentos

## Tipos testados

- [x] Entrada
- [x] Parcela
- [x] Saldo final
- [x] Pagamento único
- [x] Outro

## Status testados

- [x] Pendente
- [x] Pago
- [x] Atrasado
- [x] Cancelado

## Regras do botão Pago

- [x] Pendente mostra Pago
- [x] Atrasado mostra Pago
- [x] Pago não mostra Pago
- [x] Cancelado não mostra Pago

## Permissões

- [x] Visualizar protegida
- [x] Criar protegida
- [x] Editar protegida
- [x] Marcar pago protegida
- [x] Excluir protegida
- [x] Financeiro não exclui

## Segurança HTTP

- [x] Marcar pago usa POST
- [x] Excluir usa POST
- [x] GET direto para ação crítica retorna rota não encontrada

## Auditoria encontrada

- [x] Remover atualização automática de pagamentos atrasados durante GET
- [x] Centralizar atualização de atrasados em rotina própria
- [x] Garantir idempotência da rotina
- [ ] Criar teste específico da atualização automática

---

# 11. Dashboard

## Indicadores

- [x] Clientes cadastrados
- [x] Orçamentos pendentes
- [x] Orçamentos aprovados no mês
- [x] Pedidos em andamento
- [x] Instalações da semana
- [x] Pagamentos pendentes
- [x] Pagamentos atrasados
- [x] Recebido no mês
- [x] Valor a receber
- [x] Faturamento previsto

## Auditoria encontrada

- [x] Remover atualização de pagamentos atrasados durante GET do dashboard
- [x] Garantir que dashboard seja somente leitura

---

# 12. Segurança HTTP e CSRF

## Já implementado nas views

- [x] Ações críticas migradas de links GET para formulários POST
- [x] Exclusões críticas usam POST
- [x] Aprovação de orçamento usa POST
- [x] Concluir agenda usa POST
- [x] Marcar pagamento como pago usa POST
- [x] Formulários críticos possuem `csrf_field()`
- [x] GET direto em ações POST retorna 404

## Auditoria crítica pendente

- [x] Mapear todas as rotas POST
- [x] Mapear todos os formulários POST
- [x] Confirmar `csrf_field()` em todos os formulários
- [x] Ativar corretamente o filtro CSRF global
- [x] Confirmar rejeição de POST sem token válido
- [ ] Testar todos os formulários após ativação
- [ ] Confirmar que nenhum POST legítimo foi quebrado

---

# 13. Banco de Dados

## Estrutura existente

- [x] usuarios
- [x] clientes
- [x] categorias_servicos
- [x] produtos_servicos
- [x] orcamentos
- [x] orcamento_itens
- [x] pedidos
- [x] pedido_status_historico
- [x] agenda
- [x] pagamentos

## Pendências

- [ ] Criar backup formal da base estável
- [x] Documentar procedimento de restauração
- [ ] Anonimizar dump antes de versionar ou compartilhar
- [x] Criar migrations
- [ ] Testar migrations em banco vazio
- [ ] Criar seeds de dados fictícios
- [ ] Separar dados reais de dados de teste
- [ ] Remover credenciais e informações sensíveis de dumps versionados

---

# 14. Massa de Teste

- [x] Usuários por perfil
- [x] Orçamentos em vários status
- [x] Pedidos em vários status
- [x] Agenda em todos os status
- [x] Pagamentos em todos os status

## Pendências

- [ ] Garantir uso consistente do prefixo `[TESTE]`
- [ ] Garantir que todos os usuários de teste existam e estejam ativos
- [ ] Criar seed reproduzível
- [ ] Evitar dados potencialmente reais no dump compartilhado

---

# 15. Riscos Técnicos Identificados

- [ ] Corrigir permissões por ação incompletas
- [ ] Ativar e validar CSRF
- [ ] Remover mudanças de estado em GET
- [ ] Anonimizar dump SQL
- [ ] Criar migrations
- [ ] Criar rotina de backup/restauração
- [x] Revisar geração concorrente de números sequenciais
- [x] Revisar impacto de soft delete com relacionamentos
- [ ] Criar testes automatizados

---

# 16. Critérios Antes de Produção

O sistema não deve ser considerado pronto para produção enquanto estes itens não forem concluídos:

- [ ] Permissões por ação auditadas em todos os controllers
- [ ] Todas as views protegidas conforme a matriz
- [ ] CSRF ativo e testado
- [ ] Nenhuma mudança de estado causada por GET
- [ ] Dump sem dados sensíveis
- [ ] Backup e restauração testados
- [ ] Migrations existentes
- [ ] Dados de teste separados
- [ ] Ambiente de produção configurado
- [ ] Erros detalhados desativados em produção
- [ ] Credenciais fora do repositório
- [ ] Teste final de regressão concluído
