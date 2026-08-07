<?= view('templates/header', ['title' => $title ?? 'Material | Lunna Gestor']) ?>

<div class="layout">

    <?= view('templates/sidebar') ?>

    <main class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">
                    <?= $material ? 'Editar material' : 'Novo material' ?>
                </h2>
                <p class="text-muted mb-0">Cadastre materiais fisicos usados em compras, pedidos e instalacoes</p>
            </div>

            <a href="<?= base_url('/estoque') ?>" class="btn btn-outline-dark">
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
            $action = $material
                ? base_url('/estoque/atualizar/' . $material['id'])
                : base_url('/estoque/salvar');

            $valorCampo = function ($campo) use ($material) {
                return old($campo) ?? ($material[$campo] ?? '');
            };

            $formatarDecimal = function ($valor, $casas = 3) {
                if ($valor === '' || $valor === null) {
                    return number_format(0, $casas, ',', '.');
                }

                return number_format((float) $valor, $casas, ',', '.');
            };
        ?>

        <form action="<?= $action ?>" method="post">
            <?= csrf_field() ?>

            <div class="card card-dashboard mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Dados do material</h5>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nome *</label>
                            <input
                                type="text"
                                name="nome"
                                class="form-control"
                                value="<?= esc($valorCampo('nome')) ?>"
                                placeholder="Exemplo: Vidro temperado 8mm incolor"
                                required
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Produto vinculado</label>
                            <select name="produto_servico_id" class="form-select">
                                <option value="">Sem vinculo comercial</option>
                                <?php foreach ($produtos as $produto): ?>
                                    <option
                                        value="<?= $produto['id'] ?>"
                                        <?= (string) $valorCampo('produto_servico_id') === (string) $produto['id'] ? 'selected' : '' ?>
                                    >
                                        <?= esc($produto['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Unidade *</label>
                            <input
                                type="text"
                                name="unidade_medida"
                                class="form-control"
                                value="<?= esc($valorCampo('unidade_medida') ?: 'unidade') ?>"
                                placeholder="m2, unidade, chapa, metro"
                                required
                            >
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Estoque minimo</label>
                            <input
                                type="text"
                                name="estoque_minimo"
                                class="form-control money"
                                value="<?= esc($formatarDecimal($valorCampo('estoque_minimo'))) ?>"
                                placeholder="0,000"
                            >
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Custo unitario</label>
                            <input
                                type="text"
                                name="custo_unitario"
                                class="form-control money"
                                value="<?= esc($formatarDecimal($valorCampo('custo_unitario'), 2)) ?>"
                                placeholder="0,00"
                            >
                        </div>

                        <?php if (!$material): ?>
                            <div class="col-md-3">
                                <label class="form-label">Saldo inicial</label>
                                <input
                                    type="text"
                                    name="saldo_atual"
                                    class="form-control money"
                                    value="<?= esc($formatarDecimal($valorCampo('saldo_atual'))) ?>"
                                    placeholder="0,000"
                                >
                            </div>
                        <?php else: ?>
                            <div class="col-md-3">
                                <label class="form-label">Saldo atual</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?= esc($formatarDecimal($material['saldo_atual'])) ?>"
                                    readonly
                                >
                            </div>
                        <?php endif; ?>

                        <div class="col-md-6">
                            <label class="form-label">Fornecedor</label>
                            <input
                                type="text"
                                name="fornecedor"
                                class="form-control"
                                value="<?= esc($valorCampo('fornecedor')) ?>"
                                placeholder="Nome do fornecedor"
                            >
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Localizacao</label>
                            <input
                                type="text"
                                name="localizacao"
                                class="form-control"
                                value="<?= esc($valorCampo('localizacao')) ?>"
                                placeholder="Exemplo: estoque principal, prateleira A"
                            >
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Descricao</label>
                            <textarea name="descricao" class="form-control" rows="3"><?= esc($valorCampo('descricao')) ?></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Observacoes internas</label>
                            <textarea name="observacoes" class="form-control" rows="3"><?= esc($valorCampo('observacoes')) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url('/estoque') ?>" class="btn btn-outline-secondary">
                    Cancelar
                </a>

                <button type="submit" class="btn btn-dark">
                    <?= $material ? 'Salvar alteracoes' : 'Cadastrar material' ?>
                </button>
            </div>
        </form>

    </main>

</div>

<?= view('templates/footer') ?>
