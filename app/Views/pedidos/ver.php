<?= view('templates/header', ['title' => $title ?? 'Pedido | Lunna Gestor']) ?>

<div class="layout">

    <?= view('templates/sidebar') ?>

    <main class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Pedido <?= esc($pedido['numero']) ?></h2>
                <p class="text-muted mb-0">
                    Gerado a partir do orçamento <?= esc($pedido['orcamento_numero']) ?>
                </p>
            </div>

            <div class="d-flex gap-2">
                <a href="<?= base_url('/pedidos') ?>" class="btn btn-outline-dark">
                    Voltar
                </a>

                <a 
                    href="<?= base_url('/agenda/novo?pedido_id=' . $pedido['id']) ?>" 
                    class="btn btn-success"
                >
                    Agendar medição/instalação
                </a>

            <?php if (temAcao('pagamentos', 'criar')): ?>
                <a 
                    href="<?= base_url('/pagamentos/novo?pedido_id=' . $pedido['id']) ?>" 
                    class="btn btn-sm btn-outline-success"
                >
                    Registrar pagamento
                </a>
            <?php endif; ?>

                <a href="<?= base_url('/orcamentos/ver/' . $pedido['orcamento_id']) ?>" class="btn btn-dark">
                    Ver orçamento
                </a>
            </div>
        </div>

        <?php if (session()->getFlashdata('sucesso')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('sucesso') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('erro')): ?>
            <div class="alert alert-danger">
                <?= session()->getFlashdata('erro') ?>
            </div>
        <?php endif; ?>

        <div class="row g-4 mb-4">

            <div class="col-md-8">
                <div class="card card-dashboard h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Dados do cliente</h5>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted">Cliente</small>
                                <h5 class="fw-bold mb-1"><?= esc($pedido['cliente_nome']) ?></h5>
                                <p class="mb-0"><?= esc($pedido['cpf_cnpj'] ?: '-') ?></p>
                            </div>

                            <div class="col-md-6">
                                <small class="text-muted">Contato</small>
                                <p class="mb-0">
                                    WhatsApp: <?= esc($pedido['whatsapp'] ?: '-') ?><br>
                                    Telefone: <?= esc($pedido['telefone'] ?: '-') ?><br>
                                    E-mail: <?= esc($pedido['email'] ?: '-') ?>
                                </p>
                            </div>

                            <div class="col-md-12">
                                <small class="text-muted">Endereço</small>
                                <p class="mb-0">
                                    <?= esc($pedido['endereco'] ?: '-') ?>
                                    <?= !empty($pedido['cliente_numero']) ? ', ' . esc($pedido['cliente_numero']) : '' ?>
                                    <br>
                                    <?= esc($pedido['bairro'] ?: '') ?>
                                    <?= !empty($pedido['cidade']) ? ' - ' . esc($pedido['cidade']) : '' ?>/<?= esc($pedido['estado'] ?: '') ?>
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-dashboard h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Status do pedido</h5>

                        <?php
                            $statusLabels = [
                                'aprovado' => 'Aprovado',
                                'aguardando_entrada' => 'Aguardando entrada',
                                'aguardando_material' => 'Aguardando material',
                                'em_producao' => 'Em produção',
                                'pronto_para_instalacao' => 'Pronto para instalação',
                                'instalacao_agendada' => 'Instalação agendada',
                                'em_instalacao' => 'Em instalação',
                                'instalado' => 'Instalado',
                                'finalizado' => 'Finalizado',
                                'cancelado' => 'Cancelado'
                            ];
                        ?>

                        <div class="mb-3">
                            <span class="badge bg-secondary fs-6">
                                <?= esc($statusLabels[$pedido['status']] ?? $pedido['status']) ?>
                            </span>
                        </div>

                        <?php if (temAcao('pedidos', 'alterar_status')): ?>
                        <form action="<?= base_url('/pedidos/status/' . $pedido['id']) ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label class="form-label">Alterar status</label>
                                <select name="status" class="form-select" required>
                                    <?php foreach ($statusLabels as $valor => $label): ?>
                                        <option value="<?= $valor ?>" <?= $pedido['status'] === $valor ? 'selected' : '' ?>>
                                            <?= $label ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Observação</label>
                                <textarea 
                                    name="observacao" 
                                    class="form-control" 
                                    rows="3"
                                    placeholder="Exemplo: material comprado, instalação agendada, cliente pediu alteração..."
                                ></textarea>
                            </div>

                            <button type="submit" class="btn btn-dark w-100">
                                Atualizar status
                            </button>
                        </form>
                        <?php else: ?>
                            <div class="alert alert-light border mb-0">
                                Seu perfil pode visualizar este pedido, mas não pode alterar o status.
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

        </div>

        <div class="card card-dashboard mb-4">
            <div class="card-body">

                <h5 class="fw-bold mb-3">Itens do pedido</h5>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Ambiente</th>
                                <th>Descrição</th>
                                <th>Medidas</th>
                                <th>Unidade</th>
                                <th>Qtd.</th>
                                <th>Valor unit.</th>
                                <th>Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($itens as $item): ?>
                                <tr>
                                    <td><?= esc($item['ambiente'] ?: '-') ?></td>

                                    <td>
                                        <strong><?= esc($item['descricao']) ?></strong>
                                        <?php if (!empty($item['observacoes'])): ?>
                                            <br>
                                            <small class="text-muted"><?= esc($item['observacoes']) ?></small>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if ($item['unidade_calculo'] === 'm2'): ?>
                                            <?= number_format($item['largura'], 2, ',', '.') ?> x 
                                            <?= number_format($item['altura'], 2, ',', '.') ?> =
                                            <?= number_format($item['area_m2'], 3, ',', '.') ?> m²
                                        <?php elseif ($item['unidade_calculo'] === 'metro_linear'): ?>
                                            <?= number_format($item['largura'], 2, ',', '.') ?> m linear
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php
                                            $unidades = [
                                                'm2' => 'm²',
                                                'metro_linear' => 'Metro linear',
                                                'unidade' => 'Unidade',
                                                'servico_fechado' => 'Serviço fechado'
                                            ];
                                        ?>
                                        <?= esc($unidades[$item['unidade_calculo']] ?? $item['unidade_calculo']) ?>
                                    </td>

                                    <td><?= number_format($item['quantidade'], 2, ',', '.') ?></td>

                                    <td>R$ <?= number_format($item['valor_unitario'], 2, ',', '.') ?></td>

                                    <td>
                                        <strong>R$ <?= number_format($item['valor_total'], 2, ',', '.') ?></strong>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <div class="row g-4">

        <div class="col-md-5">
            <div class="card card-dashboard h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Resumo financeiro</h5>

                        <a 
                            href="<?= base_url('/pagamentos/novo?pedido_id=' . $pedido['id']) ?>" 
                            class="btn btn-sm btn-outline-success"
                        >
                            Registrar pagamento
                        </a>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Total do pedido</span>
                        <strong>R$ <?= number_format($resumoFinanceiro['total_pedido'], 2, ',', '.') ?></strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Total pago</span>
                        <strong>R$ <?= number_format($resumoFinanceiro['total_pago'], 2, ',', '.') ?></strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Total pendente</span>
                        <strong>R$ <?= number_format($resumoFinanceiro['total_pendente'], 2, ',', '.') ?></strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2 text-danger">
                        <span>Total atrasado</span>
                        <strong>R$ <?= number_format($resumoFinanceiro['total_atrasado'], 2, ',', '.') ?></strong>
                    </div>

                    <?php if ($resumoFinanceiro['total_cancelado'] > 0): ?>
                        <div class="d-flex justify-content-between mb-2 text-muted">
                            <span>Total cancelado</span>
                            <strong>R$ <?= number_format($resumoFinanceiro['total_cancelado'], 2, ',', '.') ?></strong>
                        </div>
                    <?php endif; ?>

                    <hr>

                    <div class="d-flex justify-content-between fs-5">
                        <span>Saldo restante</span>
                        <strong>R$ <?= number_format($resumoFinanceiro['saldo_restante'], 2, ',', '.') ?></strong>
                    </div>

                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Progresso de pagamento</small>
                            <small class="text-muted">
                                <?= number_format($resumoFinanceiro['percentual_pago'], 0, ',', '.') ?>%
                            </small>
                        </div>

                        <div class="progress" style="height: 8px;">
                            <div 
                                class="progress-bar bg-success" 
                                role="progressbar" 
                                style="width: <?= number_format($resumoFinanceiro['percentual_pago'], 2, '.', '') ?>%;"
                                aria-valuenow="<?= number_format($resumoFinanceiro['percentual_pago'], 2, '.', '') ?>" 
                                aria-valuemin="0" 
                                aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    <hr>

                    <p class="mb-0">
                        <strong>Forma de pagamento combinada:</strong><br>
                        <?= esc($pedido['forma_pagamento'] ?: '-') ?>
                    </p>
                </div>
            </div>
        </div>

            <div class="col-md-7">
                <div class="card card-dashboard h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Histórico de status</h5>

                        <?php if (!empty($historico)): ?>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Status anterior</th>
                                            <th>Novo status</th>
                                            <th>Observação</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($historico as $item): ?>
                                            <tr>
                                                <td>
                                                    <?= date('d/m/Y H:i', strtotime($item['created_at'])) ?>
                                                </td>

                                                <td>
                                                    <?= $item['status_anterior'] ? esc($statusLabels[$item['status_anterior']] ?? $item['status_anterior']) : '-' ?>
                                                </td>

                                                <td>
                                                    <strong><?= esc($statusLabels[$item['status_novo']] ?? $item['status_novo']) ?></strong>
                                                </td>

                                                <td>
                                                    <?= esc($item['observacao'] ?: '-') ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">Nenhuma alteração registrada.</p>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

        </div>

        <div class="card card-dashboard mt-4">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">Pagamentos do pedido</h5>
                        <p class="text-muted mb-0">Entradas, parcelas e saldos vinculados a este pedido</p>
                    </div>

                <?php if (temAcao('pagamentos', 'criar')): ?>
                    <a 
                        href="<?= base_url('/pagamentos/novo?pedido_id=' . $pedido['id']) ?>" 
                        class="btn btn-dark"
                    >
                        Novo pagamento
                    </a>
                <?php endif; ?>
                </div>

                <?php
                    $statusPagamentoLabels = [
                        'pendente' => 'Pendente',
                        'pago' => 'Pago',
                        'atrasado' => 'Atrasado',
                        'cancelado' => 'Cancelado'
                    ];

                    $statusPagamentoClasses = [
                        'pendente' => 'bg-secondary',
                        'pago' => 'bg-success',
                        'atrasado' => 'bg-danger',
                        'cancelado' => 'bg-dark'
                    ];

                    $formasPagamento = [
                        'pix' => 'Pix',
                        'dinheiro' => 'Dinheiro',
                        'cartao_debito' => 'Cartão débito',
                        'cartao_credito' => 'Cartão crédito',
                        'boleto' => 'Boleto',
                        'transferencia' => 'Transferência',
                        'cheque' => 'Cheque',
                        'outro' => 'Outro'
                    ];

                    $tiposPagamento = [
                        'entrada' => 'Entrada',
                        'parcela' => 'Parcela',
                        'saldo_final' => 'Saldo final',
                        'pagamento_unico' => 'Pagamento único',
                        'outro' => 'Outro'
                    ];
                ?>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Vencimento</th>
                                <th>Descrição</th>
                                <th>Tipo</th>
                                <th>Forma</th>
                                <th>Valor</th>
                                <th>Status</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($pagamentos)): ?>
                                <?php foreach ($pagamentos as $pagamento): ?>
                                    <tr>
                                        <td>
                                            <strong><?= date('d/m/Y', strtotime($pagamento['data_vencimento'])) ?></strong>

                                            <?php if (!empty($pagamento['data_pagamento'])): ?>
                                                <br>
                                                <small class="text-muted">
                                                    Pago em <?= date('d/m/Y', strtotime($pagamento['data_pagamento'])) ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <strong><?= esc($pagamento['descricao']) ?></strong>

                                            <?php if (!empty($pagamento['observacoes'])): ?>
                                                <br>
                                                <small class="text-muted"><?= esc($pagamento['observacoes']) ?></small>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?= esc($tiposPagamento[$pagamento['tipo']] ?? $pagamento['tipo']) ?>
                                        </td>

                                        <td>
                                            <?= esc($formasPagamento[$pagamento['forma_pagamento']] ?? $pagamento['forma_pagamento']) ?>
                                        </td>

                                        <td>
                                            <strong>R$ <?= number_format($pagamento['valor'], 2, ',', '.') ?></strong>
                                        </td>

                                        <td>
                                            <span class="badge <?= esc($statusPagamentoClasses[$pagamento['status']] ?? 'bg-secondary') ?>">
                                                <?= esc($statusPagamentoLabels[$pagamento['status']] ?? $pagamento['status']) ?>
                                            </span>
                                        </td>

                                        <td class="text-end">
                                            <?php if (
                                                temAcao('pagamentos', 'marcar_pago') && 
                                                in_array($pagamento['status'], ['pendente', 'atrasado'])
                                            ): ?>
                                            <form 
                                                action="<?= base_url('/pagamentos/marcar-pago/' . $pagamento['id'] . '?pedido=1') ?>" 
                                                method="post" 
                                                class="d-inline"
                                                onsubmit="return confirm('Marcar este pagamento como pago?')"
                                            >
                                                <?= csrf_field() ?>

                                                <button type="submit" class="btn btn-sm btn-outline-success">
                                                    Pago
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if (temAcao('pagamentos', 'editar')): ?>
                                            <a 
                                                href="<?= base_url('/pagamentos/editar/' . $pagamento['id']) ?>" 
                                                class="btn btn-sm btn-outline-dark"
                                            >
                                                Editar
                                            </a>
                                        <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Nenhum pagamento registrado para este pedido.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </main>

</div>

<?= view('templates/footer') ?>