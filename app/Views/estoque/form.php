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

        <?php
            $notaImportada = session()->getFlashdata('notaImportada') ?? [];

            $action = $material
                ? base_url('/estoque/atualizar/' . $material['id'])
                : base_url('/estoque/salvar');

            $valorCampo = function ($campo) use ($material, $notaImportada) {
                return old($campo) ?? ($material[$campo] ?? ($notaImportada[$campo] ?? ''));
            };

            $formatarDecimal = function ($valor, $casas = 3) {
                if ($valor === '' || $valor === null) {
                    return number_format(0, $casas, ',', '.');
                }

                return number_format((float) $valor, $casas, ',', '.');
            };

            $tiposControle = [
                'unidade' => 'Unidade',
                'metro_linear' => 'Metro linear',
                'metro_quadrado' => 'Metro quadrado',
                'chapa' => 'Chapa inteira',
                'retalho' => 'Retalho / sobra',
                'servico_sem_estoque' => 'Servico sem estoque',
            ];

            $origens = [
                'manual' => 'Cadastro manual',
                'nota_compra' => 'Nota de compra',
                'ajuste' => 'Ajuste de estoque',
                'retalho' => 'Retalho / sobra',
            ];

            $tipoControleAtual = $valorCampo('tipo_controle') ?: 'unidade';
            $origemAtual = $valorCampo('origem') ?: 'manual';
        ?>

        <?php if (!$material): ?>
            <form action="<?= base_url('/estoque/importar-nota') ?>" method="post" enctype="multipart/form-data" class="card card-dashboard mb-4">
                <?= csrf_field() ?>

                <div class="card-body">
                    <h5 class="fw-bold mb-2">Importar nota fiscal</h5>
                    <p class="text-muted mb-3">
                        Envie o XML da NF-e ou o HTML baixado no portal da nota para preencher os dados principais do material.
                    </p>

                    <div class="row g-3 align-items-end">
                        <div class="col-md-9">
                            <label class="form-label">Arquivo da nota (.xml, .html ou .htm)</label>
                            <input
                                type="file"
                                name="arquivo_nota"
                                class="form-control"
                                accept=".xml,.html,.htm,text/xml,application/xml,text/html"
                                required
                            >
                            <small class="text-muted">Limite de 2 MB. Revise os dados antes de cadastrar no estoque.</small>
                        </div>

                        <div class="col-md-3">
                            <button type="submit" class="btn btn-outline-dark w-100">
                                Ler nota
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>

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
                            <label class="form-label">Controle *</label>
                            <select name="tipo_controle" class="form-select" required>
                                <?php foreach ($tiposControle as $valor => $label): ?>
                                    <option value="<?= $valor ?>" <?= $tipoControleAtual === $valor ? 'selected' : '' ?>>
                                        <?= esc($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Origem</label>
                            <select name="origem" class="form-select">
                                <?php foreach ($origens as $valor => $label): ?>
                                    <option value="<?= $valor ?>" <?= $origemAtual === $valor ? 'selected' : '' ?>>
                                        <?= esc($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Espessura</label>
                            <input
                                type="text"
                                name="espessura"
                                class="form-control"
                                value="<?= esc($valorCampo('espessura')) ?>"
                                placeholder="Exemplo: 8mm"
                            >
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Largura</label>
                            <input
                                type="text"
                                name="largura"
                                class="form-control money"
                                value="<?= esc($formatarDecimal($valorCampo('largura'))) ?>"
                                placeholder="0,000"
                            >
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Altura</label>
                            <input
                                type="text"
                                name="altura"
                                class="form-control money"
                                value="<?= esc($formatarDecimal($valorCampo('altura'))) ?>"
                                placeholder="0,000"
                            >
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Comprimento</label>
                            <input
                                type="text"
                                name="comprimento"
                                class="form-control money"
                                value="<?= esc($formatarDecimal($valorCampo('comprimento'))) ?>"
                                placeholder="0,000"
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

                        <div class="col-md-3">
                            <label class="form-label">Lote</label>
                            <input
                                type="text"
                                name="lote"
                                class="form-control"
                                value="<?= esc($valorCampo('lote')) ?>"
                                placeholder="Lote interno ou fornecedor"
                            >
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Localizacao</label>
                            <input
                                type="text"
                                name="localizacao"
                                class="form-control"
                                value="<?= esc($valorCampo('localizacao')) ?>"
                                placeholder="Exemplo: estoque principal, prateleira A"
                            >
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Numero da NF</label>
                            <input
                                type="text"
                                name="nf_numero"
                                class="form-control"
                                value="<?= esc($valorCampo('nf_numero')) ?>"
                                placeholder="Exemplo: 12345"
                            >
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Data da compra</label>
                            <input
                                type="date"
                                name="data_compra"
                                class="form-control"
                                value="<?= esc($valorCampo('data_compra')) ?>"
                            >
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Chave de acesso da NF</label>
                            <input
                                type="text"
                                name="nf_chave_acesso"
                                class="form-control"
                                maxlength="60"
                                value="<?= esc($valorCampo('nf_chave_acesso')) ?>"
                                placeholder="44 digitos da chave de acesso"
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
