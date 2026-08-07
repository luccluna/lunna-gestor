<?= view('templates/header', ['title' => $title ?? 'Estoque | Lunna Gestor']) ?>

<div class="layout">

    <?= view('templates/sidebar') ?>

    <main class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Estoque</h2>
                <p class="text-muted mb-0">Controle materiais, entradas, saidas e alertas de reposicao</p>
            </div>

            <?php if (temAcao('estoque', 'criar')): ?>
                <a href="<?= base_url('/estoque/novo') ?>" class="btn btn-dark">
                    Novo material
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

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card card-dashboard h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase fw-bold">Materiais ativos</small>
                        <h3 class="fw-bold mb-0"><?= esc($totalMateriais) ?></h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-dashboard h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase fw-bold">Baixo estoque</small>
                        <h3 class="fw-bold mb-0"><?= esc($materiaisBaixos) ?></h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-dashboard h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase fw-bold">Valor estimado</small>
                        <h3 class="fw-bold mb-0">R$ <?= number_format($valorEstoque, 2, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-dashboard mb-4">
            <div class="card-body">
                <form method="get" action="<?= base_url('/estoque') ?>" class="row g-2">
                    <div class="col-md-7">
                        <input
                            type="text"
                            name="busca"
                            class="form-control"
                            placeholder="Buscar por material, fornecedor, localizacao ou produto vinculado"
                            value="<?= esc($busca ?? '') ?>"
                        >
                    </div>

                    <div class="col-md-3">
                        <select name="alerta" class="form-select">
                            <option value="">Todos os materiais</option>
                            <option value="baixo" <?= ($alerta ?? '') === 'baixo' ? 'selected' : '' ?>>
                                Somente baixo estoque
                            </option>
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

        <div class="card card-dashboard mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Material</th>
                                <th>Fornecedor</th>
                                <th>Local</th>
                                <th>Saldo</th>
                                <th>Minimo</th>
                                <th>Custo</th>
                                <th>Status</th>
                                <th class="text-end">Acoes</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($materiais)): ?>
                                <?php foreach ($materiais as $material): ?>
                                    <?php
                                        $saldo = (float) $material['saldo_atual'];
                                        $minimo = (float) $material['estoque_minimo'];
                                        $baixo = $saldo <= $minimo;
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($material['nome']) ?></strong>
                                            <?php if (!empty($material['produto_nome'])): ?>
                                                <br>
                                                <small class="text-muted">Produto: <?= esc($material['produto_nome']) ?></small>
                                            <?php endif; ?>
                                        </td>

                                        <td><?= esc($material['fornecedor'] ?: '-') ?></td>
                                        <td><?= esc($material['localizacao'] ?: '-') ?></td>
                                        <td>
                                            <strong><?= number_format($saldo, 3, ',', '.') ?></strong>
                                            <small class="text-muted"><?= esc($material['unidade_medida']) ?></small>
                                        </td>
                                        <td><?= number_format($minimo, 3, ',', '.') ?></td>
                                        <td>R$ <?= number_format((float) $material['custo_unitario'], 2, ',', '.') ?></td>
                                        <td>
                                            <?php if ($baixo): ?>
                                                <span class="badge bg-danger">Baixo estoque</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Disponivel</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <?php if (temAcao('estoque', 'movimentar')): ?>
                                                <a href="<?= base_url('/estoque/movimentar/' . $material['id']) ?>" class="btn btn-sm btn-outline-dark">
                                                    Entrada/Saida
                                                </a>
                                            <?php endif; ?>

                                            <?php if (temAcao('estoque', 'editar')): ?>
                                                <a href="<?= base_url('/estoque/editar/' . $material['id']) ?>" class="btn btn-sm btn-outline-secondary">
                                                    Editar
                                                </a>
                                            <?php endif; ?>

                                            <?php if (temAcao('estoque', 'excluir')): ?>
                                                <form
                                                    action="<?= base_url('/estoque/excluir/' . $material['id']) ?>"
                                                    method="post"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Tem certeza que deseja remover este material do estoque?')"
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
                                        Nenhum material encontrado no estoque.
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

        <div class="card card-dashboard">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Historico recente</h5>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Material</th>
                                <th>Tipo</th>
                                <th>Quantidade</th>
                                <th>Pedido</th>
                                <th>Documento</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($historico)): ?>
                                <?php foreach ($historico as $movimentacao): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($movimentacao['data_movimentacao'])) ?></td>
                                        <td><?= esc($movimentacao['material_nome']) ?></td>
                                        <td>
                                            <?php if ($movimentacao['tipo'] === 'entrada'): ?>
                                                <span class="badge bg-success">Entrada</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Saida</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= number_format((float) $movimentacao['quantidade'], 3, ',', '.') ?></td>
                                        <td><?= esc($movimentacao['pedido_numero'] ?? '-') ?></td>
                                        <td><?= esc($movimentacao['documento'] ?: '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Nenhuma movimentacao registrada.
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
