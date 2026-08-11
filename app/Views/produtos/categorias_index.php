<?= view('templates/header', ['title' => $title ?? 'Categorias | Lunna Gestor']) ?>

<div class="layout">

    <?= view('templates/sidebar') ?>

    <main class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Categorias</h2>
                <p class="text-muted mb-0">Organize os produtos e serviços usados nos orçamentos</p>
            </div>

            <div class="d-flex gap-2">
                <a href="<?= base_url('/produtos') ?>" class="btn btn-outline-dark">
                    Produtos e serviços
                </a>

                <?php if (temAcao('produtos', 'criar')): ?>
                    <a href="<?= base_url('/produtos/categorias/nova') ?>" class="btn btn-dark">
                        Nova categoria
                    </a>
                <?php endif; ?>
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

        <div class="card card-dashboard mb-4">
            <div class="card-body">
                <form method="get" action="<?= base_url('/produtos/categorias') ?>" class="row g-2">
                    <div class="col-md-10">
                        <input
                            type="text"
                            name="busca"
                            class="form-control"
                            placeholder="Buscar por nome ou descrição"
                            value="<?= esc($busca ?? '') ?>"
                        >
                    </div>

                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-outline-dark">
                            Buscar
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
                                <th>Categoria</th>
                                <th>Descrição</th>
                                <th>Itens vinculados</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($categorias)): ?>
                                <?php foreach ($categorias as $categoria): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($categoria['nome']) ?></strong>
                                        </td>

                                        <td>
                                            <?= esc($categoria['descricao'] ?: '-') ?>
                                        </td>

                                        <td>
                                            <?= (int) ($categoria['produtos_vinculados'] ?? 0) ?>
                                        </td>

                                        <td class="text-end">
                                            <?php if (temAcao('produtos', 'editar')): ?>
                                                <a
                                                    href="<?= base_url('/produtos/categorias/editar/' . $categoria['id']) ?>"
                                                    class="btn btn-sm btn-outline-dark"
                                                >
                                                    Editar
                                                </a>
                                            <?php endif; ?>

                                            <?php if (temAcao('produtos', 'excluir')): ?>
                                                <form
                                                    action="<?= base_url('/produtos/categorias/excluir/' . $categoria['id']) ?>"
                                                    method="post"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Tem certeza que deseja remover esta categoria?')"
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
                                    <td colspan="4" class="text-center text-muted py-4">
                                        Nenhuma categoria encontrada.
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
