<?= view('templates/header', ['title' => $title ?? 'Categoria | Lunna Gestor']) ?>

<div class="layout">

    <?= view('templates/sidebar') ?>

    <main class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">
                    <?= $categoria ? 'Editar categoria' : 'Nova categoria' ?>
                </h2>
                <p class="text-muted mb-0">
                    Agrupe produtos e serviços para facilitar orçamentos e filtros
                </p>
            </div>

            <a href="<?= base_url('/produtos/categorias') ?>" class="btn btn-outline-dark">
                Voltar
            </a>
        </div>

        <?php if (session()->getFlashdata('erros')): ?>
            <div class="alert alert-danger">
                <strong>Verifique os campos abaixo:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach (session()->getFlashdata('erros') as $erro): ?>
                        <li><?= esc($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php
            $action = $categoria
                ? base_url('/produtos/categorias/atualizar/' . $categoria['id'])
                : base_url('/produtos/categorias/salvar');

            $valorCampo = function ($campo) use ($categoria) {
                return old($campo) ?? ($categoria[$campo] ?? '');
            };
        ?>

        <form action="<?= $action ?>" method="post">
            <?= csrf_field() ?>

            <div class="card card-dashboard mb-4">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">Dados da categoria</h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nome *</label>
                            <input
                                type="text"
                                name="nome"
                                class="form-control"
                                value="<?= esc($valorCampo('nome')) ?>"
                                placeholder="Exemplo: Vidros temperados"
                                required
                            >
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Descrição</label>
                            <textarea
                                name="descricao"
                                class="form-control"
                                rows="4"
                                placeholder="Exemplo: produtos usados em box, portas, fechamento de áreas e divisórias."
                            ><?= esc($valorCampo('descricao')) ?></textarea>
                        </div>
                    </div>

                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url('/produtos/categorias') ?>" class="btn btn-outline-secondary">
                    Cancelar
                </a>

                <button type="submit" class="btn btn-dark">
                    <?= $categoria ? 'Salvar alterações' : 'Cadastrar categoria' ?>
                </button>
            </div>
        </form>

    </main>

</div>

<?= view('templates/footer') ?>
