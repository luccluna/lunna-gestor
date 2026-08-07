<?= view('templates/header', ['title' => $title ?? 'Produto/Serviço | Lunna Gestor']) ?>

<div class="layout">

    <?= view('templates/sidebar') ?>

    <main class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">
                    <?= $produto ? 'Editar produto/serviço' : 'Novo produto/serviço' ?>
                </h2>
                <p class="text-muted mb-0">
                    Cadastre produtos, serviços e bases de cálculo para orçamentos
                </p>
            </div>

            <a href="<?= base_url('/produtos') ?>" class="btn btn-outline-dark">
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
            $action = $produto 
                ? base_url('/produtos/atualizar/' . $produto['id']) 
                : base_url('/produtos/salvar');

            $valorCampo = function ($campo) use ($produto) {
                return old($campo) ?? ($produto[$campo] ?? '');
            };

            $formatarMoeda = function ($valor) {
                if ($valor === '' || $valor === null) {
                    return '0,00';
                }

                return number_format((float) $valor, 2, ',', '.');
            };
        ?>

        <form action="<?= $action ?>" method="post">
            <?= csrf_field() ?>

            <div class="card card-dashboard mb-4">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">Dados do produto/serviço</h5>

                    <div class="row g-3">

                        <div class="col-md-8">
                            <label class="form-label">Nome *</label>
                            <input 
                                type="text" 
                                name="nome" 
                                class="form-control" 
                                value="<?= esc($valorCampo('nome')) ?>" 
                                placeholder="Exemplo: Box Blindex vidro temperado 8mm"
                                required
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Categoria</label>
                            <select name="categoria_id" class="form-select">
                                <option value="">Sem categoria</option>

                                <?php foreach ($categorias as $categoria): ?>
                                    <option 
                                        value="<?= $categoria['id'] ?>"
                                        <?= (string) $valorCampo('categoria_id') === (string) $categoria['id'] ? 'selected' : '' ?>
                                    >
                                        <?= esc($categoria['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tipo *</label>
                            <select name="tipo" class="form-select" required>
                                <?php $tipoAtual = $valorCampo('tipo') ?: 'produto'; ?>

                                <option value="produto" <?= $tipoAtual === 'produto' ? 'selected' : '' ?>>
                                    Produto
                                </option>

                                <option value="servico" <?= $tipoAtual === 'servico' ? 'selected' : '' ?>>
                                    Serviço
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Unidade de cálculo *</label>
                            <select name="unidade_calculo" class="form-select" required>
                                <?php $unidadeAtual = $valorCampo('unidade_calculo') ?: 'm2'; ?>

                                <option value="m2" <?= $unidadeAtual === 'm2' ? 'selected' : '' ?>>
                                    m²
                                </option>

                                <option value="metro_linear" <?= $unidadeAtual === 'metro_linear' ? 'selected' : '' ?>>
                                    Metro linear
                                </option>

                                <option value="unidade" <?= $unidadeAtual === 'unidade' ? 'selected' : '' ?>>
                                    Unidade
                                </option>

                                <option value="servico_fechado" <?= $unidadeAtual === 'servico_fechado' ? 'selected' : '' ?>>
                                    Serviço fechado
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Valor base</label>
                            <input 
                                type="text" 
                                name="valor_base" 
                                class="form-control money" 
                                value="<?= esc($formatarMoeda($valorCampo('valor_base'))) ?>"
                                placeholder="0,00"
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Custo base</label>
                            <input 
                                type="text" 
                                name="custo_base" 
                                class="form-control money" 
                                value="<?= esc($formatarMoeda($valorCampo('custo_base'))) ?>"
                                placeholder="0,00"
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Margem de lucro (%)</label>
                            <input 
                                type="text" 
                                name="margem_lucro" 
                                class="form-control money" 
                                value="<?= esc($formatarMoeda($valorCampo('margem_lucro'))) ?>"
                                placeholder="0,00"
                            >
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Descrição</label>
                            <textarea 
                                name="descricao" 
                                class="form-control" 
                                rows="3"
                                placeholder="Descrição que poderá aparecer no orçamento"
                            ><?= esc($valorCampo('descricao')) ?></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Observações internas</label>
                            <textarea 
                                name="observacoes" 
                                class="form-control" 
                                rows="3"
                                placeholder="Exemplo: confirmar espessura antes da produção, produto depende de fornecedor..."
                            ><?= esc($valorCampo('observacoes')) ?></textarea>
                        </div>

                    </div>

                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url('/produtos') ?>" class="btn btn-outline-secondary">
                    Cancelar
                </a>

                <button type="submit" class="btn btn-dark">
                    <?= $produto ? 'Salvar alterações' : 'Cadastrar produto/serviço' ?>
                </button>
            </div>

        </form>

    </main>

</div>

<?= view('templates/footer') ?>