<?= view('templates/header', ['title' => $title ?? 'Pagamento | Lunna Gestor']) ?>

<div class="layout">

    <?= view('templates/sidebar') ?>

    <main class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">
                    <?= $pagamento ? 'Editar pagamento' : 'Novo pagamento' ?>
                </h2>
                <p class="text-muted mb-0">
                    Registre entradas, parcelas e saldos de pedidos
                </p>
            </div>

            <a href="<?= base_url('/pagamentos') ?>" class="btn btn-outline-dark">
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
            $action = $pagamento 
                ? base_url('/pagamentos/atualizar/' . $pagamento['id']) 
                : base_url('/pagamentos/salvar');

            $valorCampo = function ($campo) use ($pagamento, $pedido) {
                if (old($campo) !== null) {
                    return old($campo);
                }

                if ($pagamento && array_key_exists($campo, $pagamento)) {
                    return $pagamento[$campo];
                }

                if ($pedido) {
                    if ($campo === 'pedido_id') {
                        return $pedido['id'];
                    }

                    if ($campo === 'valor') {
                        return $pedido['total'];
                    }

                    if ($campo === 'descricao') {
                        return 'Pagamento do pedido ' . $pedido['numero'];
                    }
                }

                return '';
            };

            $formatarMoeda = function ($valor) {
                if ($valor === '' || $valor === null) {
                    return '0,00';
                }

                return number_format((float) $valor, 2, ',', '.');
            };

            $tipos = [
                'entrada' => 'Entrada',
                'parcela' => 'Parcela',
                'saldo_final' => 'Saldo final',
                'pagamento_unico' => 'Pagamento único',
                'outro' => 'Outro'
            ];

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

            $statusOptions = [
                'pendente' => 'Pendente',
                'pago' => 'Pago',
                'atrasado' => 'Atrasado',
                'cancelado' => 'Cancelado'
            ];

            $tipoAtual = $valorCampo('tipo') ?: 'entrada';
            $formaAtual = $valorCampo('forma_pagamento') ?: 'pix';
            $statusAtual = $valorCampo('status') ?: 'pendente';
        ?>

        <?php if ($pedido): ?>
            <div class="alert alert-info">
                Pagamento vinculado ao pedido 
                <strong><?= esc($pedido['numero']) ?></strong>
                do cliente 
                <strong><?= esc($pedido['cliente_nome']) ?></strong>.
                Total do pedido:
                <strong>R$ <?= number_format($pedido['total'], 2, ',', '.') ?></strong>.
            </div>
        <?php endif; ?>

        <form action="<?= $action ?>" method="post">
            <?= csrf_field() ?>

            <div class="card card-dashboard mb-4">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">Dados do pagamento</h5>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Pedido *</label>
                            <select name="pedido_id" class="form-select" required>
                                <option value="">Selecione o pedido</option>

                                <?php foreach ($pedidos as $item): ?>
                                    <option 
                                        value="<?= $item['id'] ?>"
                                        <?= (string) $valorCampo('pedido_id') === (string) $item['id'] ? 'selected' : '' ?>
                                    >
                                        <?= esc($item['numero']) ?> - <?= esc($item['cliente_nome']) ?> 
                                        — R$ <?= number_format($item['total'], 2, ',', '.') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Descrição *</label>
                            <input 
                                type="text" 
                                name="descricao" 
                                class="form-control" 
                                required
                                value="<?= esc($valorCampo('descricao')) ?>"
                                placeholder="Exemplo: Entrada do pedido PED-2026-0001"
                            >
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tipo *</label>
                            <select name="tipo" class="form-select" required>
                                <?php foreach ($tipos as $valor => $label): ?>
                                    <option value="<?= $valor ?>" <?= $tipoAtual === $valor ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Forma de pagamento *</label>
                            <select name="forma_pagamento" class="form-select" required>
                                <?php foreach ($formas as $valor => $label): ?>
                                    <option value="<?= $valor ?>" <?= $formaAtual === $valor ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Valor *</label>
                            <input 
                                type="text" 
                                name="valor" 
                                class="form-control money" 
                                required
                                value="<?= esc($formatarMoeda($valorCampo('valor'))) ?>"
                                placeholder="0,00"
                            >
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Vencimento *</label>
                            <input 
                                type="date" 
                                name="data_vencimento" 
                                class="form-control"
                                required
                                value="<?= esc($valorCampo('data_vencimento') ?: date('Y-m-d')) ?>"
                            >
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Status *</label>
                            <select name="status" class="form-select" required>
                                <?php foreach ($statusOptions as $valor => $label): ?>
                                    <option value="<?= $valor ?>" <?= $statusAtual === $valor ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Data de pagamento</label>
                            <input 
                                type="date" 
                                name="data_pagamento" 
                                class="form-control"
                                value="<?= esc($valorCampo('data_pagamento')) ?>"
                            >
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Observações</label>
                            <textarea 
                                name="observacoes" 
                                class="form-control" 
                                rows="4"
                                placeholder="Exemplo: cliente pagou via Pix, aguardando compensação, combinar restante na instalação..."
                            ><?= esc($valorCampo('observacoes')) ?></textarea>
                        </div>

                    </div>

                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url('/pagamentos') ?>" class="btn btn-outline-secondary">
                    Cancelar
                </a>

                <button type="submit" class="btn btn-dark">
                    <?= $pagamento ? 'Salvar alterações' : 'Registrar pagamento' ?>
                </button>
            </div>

        </form>

    </main>

</div>

<?= view('templates/footer') ?>