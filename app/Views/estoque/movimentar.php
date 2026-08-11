<?= view('templates/header', ['title' => $title ?? 'Movimentar estoque | Lunna Gestor']) ?>

<div class="layout">

    <?= view('templates/sidebar') ?>

    <main class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Entrada/Saida de estoque</h2>
                <p class="text-muted mb-0">Registre compras, notas de entrada e saidas vinculadas a pedidos</p>
            </div>

            <a href="<?= base_url('/estoque') ?>" class="btn btn-outline-dark">
                Voltar
            </a>
        </div>

        <?php if (session()->getFlashdata('erro')): ?>
            <div class="alert alert-danger">
                <?= session()->getFlashdata('erro') ?>
            </div>
        <?php endif; ?>

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
            $tiposControle = [
                'unidade' => 'Unidade',
                'metro_linear' => 'Metro linear',
                'metro_quadrado' => 'Metro quadrado',
                'chapa' => 'Chapa inteira',
                'retalho' => 'Retalho / sobra',
                'servico_sem_estoque' => 'Servico sem estoque',
            ];

            $origens = [
                'manual' => 'Manual',
                'nota_compra' => 'Nota de compra',
                'pedido' => 'Pedido',
                'ajuste' => 'Ajuste',
                'retalho' => 'Retalho / sobra',
            ];
        ?>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card card-dashboard h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase fw-bold">Material</small>
                        <h4 class="fw-bold mt-2"><?= esc($material['nome']) ?></h4>

                        <hr>

                        <div class="mb-3">
                            <small class="text-muted d-block">Saldo atual</small>
                            <strong class="fs-4">
                                <?= number_format((float) $material['saldo_atual'], 3, ',', '.') ?>
                                <?= esc($material['unidade_medida']) ?>
                            </strong>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">Controle</small>
                            <strong><?= esc($tiposControle[$material['tipo_controle'] ?? 'unidade'] ?? ($material['tipo_controle'] ?? 'Unidade')) ?></strong>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">Estoque minimo</small>
                            <strong><?= number_format((float) $material['estoque_minimo'], 3, ',', '.') ?></strong>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">Fornecedor</small>
                            <strong><?= esc($material['fornecedor'] ?: '-') ?></strong>
                        </div>

                        <div>
                            <small class="text-muted d-block">Lote / NF</small>
                            <strong><?= esc($material['lote'] ?: '-') ?></strong>
                            <?php if (!empty($material['nf_numero'])): ?>
                                <br>
                                <small class="text-muted">NF <?= esc($material['nf_numero']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <form action="<?= base_url('/estoque/registrar-movimentacao/' . $material['id']) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="card card-dashboard">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">Movimentacao</h5>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Tipo *</label>
                                    <?php $tipoAtual = old('tipo') ?: 'entrada'; ?>
                                    <select name="tipo" class="form-select" required>
                                        <option value="entrada" <?= $tipoAtual === 'entrada' ? 'selected' : '' ?>>
                                            Entrada
                                        </option>
                                        <option value="saida" <?= $tipoAtual === 'saida' ? 'selected' : '' ?>>
                                            Saida
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Origem *</label>
                                    <?php $origemAtual = old('origem') ?: 'manual'; ?>
                                    <select name="origem" class="form-select" required>
                                        <?php foreach ($origens as $valor => $label): ?>
                                            <option value="<?= $valor ?>" <?= $origemAtual === $valor ? 'selected' : '' ?>>
                                                <?= esc($label) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Quantidade *</label>
                                    <input
                                        type="text"
                                        name="quantidade"
                                        class="form-control money"
                                        value="<?= esc(old('quantidade') ?? '0,000') ?>"
                                        placeholder="0,000"
                                        required
                                    >
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Data *</label>
                                    <input
                                        type="date"
                                        name="data_movimentacao"
                                        class="form-control"
                                        value="<?= esc(old('data_movimentacao') ?? date('Y-m-d')) ?>"
                                        required
                                    >
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Pedido vinculado</label>
                                    <select name="pedido_id" class="form-select">
                                        <option value="">Sem pedido vinculado</option>
                                        <?php foreach ($pedidos as $pedido): ?>
                                            <?php $selecionado = (string) (old('pedido_id') ?? $pedidoSelecionado ?? '') === (string) $pedido['id']; ?>
                                            <option value="<?= $pedido['id'] ?>" <?= $selecionado ? 'selected' : '' ?>>
                                                <?= esc($pedido['numero'] . ' - ' . $pedido['cliente_nome']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">Use principalmente em saidas de mercadoria para pedido.</small>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Documento/nota</label>
                                    <input
                                        type="text"
                                        name="documento"
                                        class="form-control"
                                        value="<?= esc(old('documento')) ?>"
                                        placeholder="NF, recibo..."
                                    >
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Numero da NF</label>
                                    <input
                                        type="text"
                                        name="nf_numero"
                                        class="form-control"
                                        value="<?= esc(old('nf_numero') ?? $material['nf_numero'] ?? '') ?>"
                                        placeholder="Exemplo: 12345"
                                    >
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Fornecedor</label>
                                    <input
                                        type="text"
                                        name="fornecedor"
                                        class="form-control"
                                        value="<?= esc(old('fornecedor') ?? $material['fornecedor'] ?? '') ?>"
                                        placeholder="Fornecedor da compra"
                                    >
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Lote</label>
                                    <input
                                        type="text"
                                        name="lote"
                                        class="form-control"
                                        value="<?= esc(old('lote') ?? $material['lote'] ?? '') ?>"
                                        placeholder="Lote ou referencia"
                                    >
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Custo unitario</label>
                                    <input
                                        type="text"
                                        name="custo_unitario"
                                        class="form-control money"
                                        value="<?= esc(old('custo_unitario') ?? number_format((float) $material['custo_unitario'], 2, ',', '.')) ?>"
                                        placeholder="0,00"
                                    >
                                </div>

                                <div class="col-md-9">
                                    <label class="form-label">Chave de acesso da NF</label>
                                    <input
                                        type="text"
                                        name="nf_chave_acesso"
                                        class="form-control"
                                        maxlength="60"
                                        value="<?= esc(old('nf_chave_acesso') ?? $material['nf_chave_acesso'] ?? '') ?>"
                                        placeholder="44 digitos da chave de acesso"
                                    >
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Observacoes</label>
                                    <textarea
                                        name="observacoes"
                                        class="form-control"
                                        rows="4"
                                        placeholder="Exemplo: entrada por compra, saida para instalacao, atraso do fornecedor..."
                                    ><?= esc(old('observacoes')) ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="<?= base_url('/estoque') ?>" class="btn btn-outline-secondary">
                            Cancelar
                        </a>

                        <button type="submit" class="btn btn-dark">
                            Registrar movimentacao
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </main>

</div>

<?= view('templates/footer') ?>
