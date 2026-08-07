<?= view('templates/header', ['title' => $title ?? 'Produtos e Serviços | Lunna Gestor']) ?>

<div class="layout">

    <?= view('templates/sidebar') ?>

    <main class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Produtos e Serviços</h2>
                <p class="text-muted mb-0">Cadastre os serviços vendidos pela Lunna Vidraçaria</p>
            </div>

            <?php if (temAcao('produtos', 'criar')): ?>
                <a href="<?= base_url('/produtos/novo') ?>" class="btn btn-dark">
                    Novo produto/serviço
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
                <form method="get" action="<?= base_url('/produtos') ?>" class="row g-2">
                    <div class="col-md-10">
                        <input 
                            type="text" 
                            name="busca" 
                            class="form-control" 
                            placeholder="Buscar por nome, categoria ou tipo"
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
                                <th>Produto/Serviço</th>
                                <th>Categoria</th>
                                <th>Tipo</th>
                                <th>Unidade</th>
                                <th>Valor base</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($produtos)): ?>
                                <?php foreach ($produtos as $produto): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($produto['nome']) ?></strong>
                                            <?php if (!empty($produto['descricao'])): ?>
                                                <br>
                                                <small class="text-muted"><?= esc($produto['descricao']) ?></small>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?= esc($produto['categoria_nome'] ?? '-') ?>
                                        </td>

                                        <td>
                                            <?= $produto['tipo'] === 'produto' ? 'Produto' : 'Serviço' ?>
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
                                            <?= esc($unidades[$produto['unidade_calculo']] ?? $produto['unidade_calculo']) ?>
                                        </td>

                                        <td>
                                            R$ <?= number_format($produto['valor_base'], 2, ',', '.') ?>
                                        </td>

                                        <td class="text-end">
                                            <?php if (temAcao('produtos', 'editar')): ?>
                                                <a 
                                                    href="<?= base_url('/produtos/editar/' . $produto['id']) ?>" 
                                                    class="btn btn-sm btn-outline-dark"
                                                >
                                                    Editar
                                                </a>
                                            <?php endif; ?>

                                            <?php if (temAcao('produtos', 'excluir')): ?>
                                                <form 
                                                    action="<?= base_url('/produtos/excluir/' . $produto['id']) ?>" 
                                                    method="post" 
                                                    class="d-inline"
                                                    onsubmit="return confirm('Tem certeza que deseja remover este item?')"
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
                                        Nenhum produto ou serviço encontrado.
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
