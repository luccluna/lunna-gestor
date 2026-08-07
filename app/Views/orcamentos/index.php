<?= view('templates/header', ['title' => $title ?? 'Orçamentos | Lunna Gestor']) ?>

<div class="layout">

    <?= view('templates/sidebar') ?>

    <main class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Orçamentos</h2>
                <p class="text-muted mb-0">Gerencie propostas comerciais, medições e negociações</p>
            </div>

            <?php if (temAcao('orcamentos', 'criar')): ?>
                <a href="<?= base_url('/orcamentos/novo') ?>" class="btn btn-dark">
                    Novo orçamento
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
                <form method="get" action="<?= base_url('/orcamentos') ?>" class="row g-2">

                    <div class="col-md-7">
                        <input 
                            type="text" 
                            name="busca" 
                            class="form-control" 
                            placeholder="Buscar por número, cliente ou WhatsApp"
                            value="<?= esc($busca ?? '') ?>"
                        >
                    </div>

                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">Todos os status</option>

                            <?php
                                $statusOptions = [
                                    'novo' => 'Novo',
                                    'aguardando_medicao' => 'Aguardando medição',
                                    'em_elaboracao' => 'Em elaboração',
                                    'enviado' => 'Enviado',
                                    'em_negociacao' => 'Em negociação',
                                    'aprovado' => 'Aprovado',
                                    'recusado' => 'Recusado',
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
                                <th>Número</th>
                                <th>Cliente</th>
                                <th>Data</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($orcamentos)): ?>
                                <?php foreach ($orcamentos as $orcamento): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($orcamento['numero']) ?></strong>
                                        </td>

                                        <td>
                                            <?= esc($orcamento['cliente_nome']) ?>
                                            <?php if (!empty($orcamento['cliente_whatsapp'])): ?>
                                                <br>
                                                <small class="text-muted"><?= esc($orcamento['cliente_whatsapp']) ?></small>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?= date('d/m/Y', strtotime($orcamento['data_orcamento'])) ?>
                                        </td>

                                        <td>
                                            <?php
                                                $statusLabels = [
                                                    'novo' => 'Novo',
                                                    'aguardando_medicao' => 'Aguardando medição',
                                                    'em_elaboracao' => 'Em elaboração',
                                                    'enviado' => 'Enviado',
                                                    'em_negociacao' => 'Em negociação',
                                                    'aprovado' => 'Aprovado',
                                                    'recusado' => 'Recusado',
                                                    'cancelado' => 'Cancelado'
                                                ];
                                            ?>

                                            <span class="badge bg-secondary">
                                                <?= esc($statusLabels[$orcamento['status']] ?? $orcamento['status']) ?>
                                            </span>
                                        </td>

                                        <td>
                                            <strong>R$ <?= number_format($orcamento['total'], 2, ',', '.') ?></strong>
                                        </td>

                                        <td class="text-end">
                                            <a 
                                                href="<?= base_url('/orcamentos/ver/' . $orcamento['id']) ?>" 
                                                class="btn btn-sm btn-outline-dark"
                                            >
                                                Ver
                                            </a>

                                            <?php if (temAcao('orcamentos', 'pdf')): ?>
                                                <a 
                                                    href="<?= base_url('/orcamentos/pdf/' . $orcamento['id']) ?>" 
                                                    class="btn btn-sm btn-outline-secondary"
                                                    target="_blank"
                                                >
                                                    PDF
                                                </a>
                                            <?php endif; ?>

                                            <?php if (temAcao('orcamentos', 'excluir')): ?>
                                                <form 
                                                    action="<?= base_url('/orcamentos/excluir/' . $orcamento['id']) ?>" 
                                                    method="post" 
                                                    class="d-inline"
                                                    onsubmit="return confirm('Tem certeza que deseja remover este orçamento?')"
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
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Nenhum orçamento encontrado.
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
