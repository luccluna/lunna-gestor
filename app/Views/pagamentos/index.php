<?= view('templates/header', ['title' => $title ?? 'Pagamentos | Lunna Gestor']) ?>

<div class="layout">

    <?= view('templates/sidebar') ?>

    <main class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Pagamentos</h2>
                <p class="text-muted mb-0">Controle valores recebidos, pendentes e atrasados</p>
            </div>

        <?php if (temAcao('pagamentos', 'criar')): ?>
            <a href="<?= base_url('/pagamentos/novo') ?>" class="btn btn-dark">
                Novo pagamento
            </a>
        <?php endif; ?>
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

        <div class="card card-dashboard mb-4">
            <div class="card-body">
                <form method="get" action="<?= base_url('/pagamentos') ?>" class="row g-2">

                    <div class="col-md-5">
                        <input 
                            type="text" 
                            name="busca" 
                            class="form-control" 
                            placeholder="Buscar por cliente, pedido ou descrição"
                            value="<?= esc($busca ?? '') ?>"
                        >
                    </div>

                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">Todos os status</option>

                            <?php
                                $statusOptions = [
                                    'pendente' => 'Pendente',
                                    'pago' => 'Pago',
                                    'atrasado' => 'Atrasado',
                                    'cancelado' => 'Cancelado'
                                ];
                            ?>

                            <?php foreach ($statusOptions as $valor => $label): ?>
                                <option value="<?= $valor ?>" <?= ($status ?? '') === $valor ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="forma_pagamento" class="form-select">
                            <option value="">Todas as formas</option>

                            <?php
                                $formas = [
                                    'pix' => 'Pix',
                                    'dinheiro' => 'Dinheiro',
                                    'cartao_debito' => 'Cartão débito',
                                    'cartao_credito' => 'Cartão crédito',
                                    'boleto' => 'Boleto',
                                    'transferencia' => 'Transferência',
                                    'cheque' => 'Cheque',
                                    'outro' => 'Outro'
                                ];
                            ?>

                            <?php foreach ($formas as $valor => $label): ?>
                                <option value="<?= $valor ?>" <?= ($formaPagamento ?? '') === $valor ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-outline-dark">
                            Filtrar
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <div class="card card-dashboard">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Vencimento</th>
                                <th>Descrição</th>
                                <th>Cliente</th>
                                <th>Pedido</th>
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
                                            <br>
                                            <small class="text-muted">
                                                <?= esc(ucfirst(str_replace('_', ' ', $pagamento['tipo']))) ?>
                                            </small>
                                        </td>

                                        <td>
                                            <?= esc($pagamento['cliente_nome']) ?>
                                            <?php if (!empty($pagamento['cliente_whatsapp'])): ?>
                                                <br>
                                                <small class="text-muted"><?= esc($pagamento['cliente_whatsapp']) ?></small>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?= esc($pagamento['pedido_numero']) ?>
                                        </td>

                                        <td>
                                            <?= esc($formas[$pagamento['forma_pagamento']] ?? $pagamento['forma_pagamento']) ?>
                                        </td>

                                        <td>
                                            <strong>R$ <?= number_format($pagamento['valor'], 2, ',', '.') ?></strong>
                                        </td>

                                        <td>
                                            <?php
                                                $statusClasses = [
                                                    'pendente' => 'bg-secondary',
                                                    'pago' => 'bg-success',
                                                    'atrasado' => 'bg-danger',
                                                    'cancelado' => 'bg-dark'
                                                ];
                                            ?>

                                            <span class="badge <?= esc($statusClasses[$pagamento['status']] ?? 'bg-secondary') ?>">
                                                <?= esc($statusOptions[$pagamento['status']] ?? $pagamento['status']) ?>
                                            </span>
                                        </td>

                                        <td class="text-end">
                                            <?php if (
                                                temAcao('pagamentos', 'marcar_pago') && 
                                                in_array($pagamento['status'], ['pendente', 'atrasado'])
                                            ): ?>
                                            <form 
                                                action="<?= base_url('/pagamentos/marcar-pago/' . $pagamento['id']) ?>" 
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

                                        <?php if (temAcao('pagamentos', 'excluir')): ?>
                                            <form 
                                                action="<?= base_url('/pagamentos/excluir/' . $pagamento['id']) ?>" 
                                                method="post" 
                                                class="d-inline"
                                                onsubmit="return confirm('Tem certeza que deseja remover este pagamento?')"
                                            >
                                                <?= csrf_field() ?>

                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    Excluir
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        Nenhum pagamento encontrado.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <?= $pager->links() ?>
                </div>

            </div>
        </div>

    </main>

</div>

<?= view('templates/footer') ?>