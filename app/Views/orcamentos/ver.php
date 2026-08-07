<?= view('templates/header', ['title' => $title ?? 'Orçamento | Lunna Gestor']) ?>

<div class="layout">

    <?= view('templates/sidebar') ?>

    <main class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Orçamento <?= esc($orcamento['numero']) ?></h2>
                <p class="text-muted mb-0">Visualização completa da proposta</p>
            </div>

            <div class="d-flex gap-2">
                <a href="<?= base_url('/orcamentos') ?>" class="btn btn-outline-dark">
                    Voltar
                </a>

                <?php if ($orcamento['status'] !== 'aprovado' && temAcao('orcamentos', 'aprovar')): ?>
                    <form 
                        action="<?= base_url('/orcamentos/aprovar/' . $orcamento['id']) ?>" 
                        method="post" 
                        class="d-inline"
                        onsubmit="return confirm('Deseja aprovar este orçamento e gerar um pedido?')"
                    >
                        <?= csrf_field() ?>

                        <button type="submit" class="btn btn-success">
                            Aprovar e gerar pedido
                        </button>
                    </form>
                <?php endif; ?>

                <?php if (temAcao('orcamentos', 'pdf')): ?>
                    <a 
                        href="<?= base_url('/orcamentos/pdf/' . $orcamento['id']) ?>" 
                        class="btn btn-outline-dark"
                        target="_blank"
                    >
                        Gerar PDF
                    </a>
                <?php endif; ?>

                <button type="button" class="btn btn-dark" onclick="window.print()">
                    Imprimir
                </button>
            </div>
        </div>

        <?php if (session()->getFlashdata('sucesso')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('sucesso') ?>
            </div>
        <?php endif; ?>

        <div class="card card-dashboard mb-4">
            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-4">
                        <small class="text-muted">Cliente</small>
                        <h5 class="fw-bold mb-1"><?= esc($orcamento['cliente_nome']) ?></h5>
                        <p class="mb-0">
                            <?= esc($orcamento['whatsapp'] ?: $orcamento['telefone'] ?: '-') ?>
                        </p>
                    </div>

                    <div class="col-md-4">
                        <small class="text-muted">Endereço</small>
                        <p class="mb-0">
                            <?= esc($orcamento['endereco'] ?? '') ?>
                            <?= !empty($orcamento['cliente_numero']) ? ', ' . esc($orcamento['cliente_numero']) : '' ?>
                            <br>
                            <?= esc($orcamento['bairro'] ?? '') ?>
                            <?= !empty($orcamento['cidade']) ? ' - ' . esc($orcamento['cidade']) : '' ?>/<?= esc($orcamento['estado'] ?? '') ?>
                        </p>
                    </div>

                    <div class="col-md-4">
                        <small class="text-muted">Informações do orçamento</small>
                        <p class="mb-0">
                            Data: <?= date('d/m/Y', strtotime($orcamento['data_orcamento'])) ?><br>
                            Validade: <?= !empty($orcamento['validade']) ? date('d/m/Y', strtotime($orcamento['validade'])) : '-' ?><br>
                            Prazo: <?= esc($orcamento['prazo_entrega'] ?: '-') ?>
                        </p>
                    </div>

                </div>

            </div>
        </div>

        <div class="card card-dashboard mb-4">
            <div class="card-body">

                <h5 class="fw-bold mb-3">Itens do orçamento</h5>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Ambiente</th>
                                <th>Descrição</th>
                                <th>Medidas</th>
                                <th>Unidade</th>
                                <th>Qtd.</th>
                                <th>Valor unit.</th>
                                <th>Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($itens as $item): ?>
                                <tr>
                                    <td><?= esc($item['ambiente'] ?: '-') ?></td>

                                    <td>
                                        <strong><?= esc($item['descricao']) ?></strong>
                                        <?php if (!empty($item['observacoes'])): ?>
                                            <br>
                                            <small class="text-muted"><?= esc($item['observacoes']) ?></small>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if ($item['unidade_calculo'] === 'm2'): ?>
                                            <?= number_format($item['largura'], 2, ',', '.') ?> x 
                                            <?= number_format($item['altura'], 2, ',', '.') ?> =
                                            <?= number_format($item['area_m2'], 3, ',', '.') ?> m²
                                        <?php elseif ($item['unidade_calculo'] === 'metro_linear'): ?>
                                            <?= number_format($item['largura'], 2, ',', '.') ?> m linear
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
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
                                        <?= esc($unidades[$item['unidade_calculo']] ?? $item['unidade_calculo']) ?>
                                    </td>

                                    <td>
                                        <?= number_format($item['quantidade'], 2, ',', '.') ?>
                                    </td>

                                    <td>
                                        R$ <?= number_format($item['valor_unitario'], 2, ',', '.') ?>
                                    </td>

                                    <td>
                                        <strong>R$ <?= number_format($item['valor_total'], 2, ',', '.') ?></strong>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <div class="row g-4">

            <div class="col-md-7">
                <div class="card card-dashboard h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Observações</h5>

                        <p>
                            <strong>Para o cliente:</strong><br>
                            <?= nl2br(esc($orcamento['observacoes_cliente'] ?: '-')) ?>
                        </p>

                        <p class="mb-0">
                            <strong>Internas:</strong><br>
                            <?= nl2br(esc($orcamento['observacoes_internas'] ?: '-')) ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card card-dashboard h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Resumo financeiro</h5>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <strong>R$ <?= number_format($orcamento['subtotal'], 2, ',', '.') ?></strong>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Desconto</span>
                            <strong>R$ <?= number_format($orcamento['desconto'], 2, ',', '.') ?></strong>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between fs-5">
                            <span>Total</span>
                            <strong>R$ <?= number_format($orcamento['total'], 2, ',', '.') ?></strong>
                        </div>

                        <hr>

                        <p class="mb-0">
                            <strong>Forma de pagamento:</strong><br>
                            <?= esc($orcamento['forma_pagamento'] ?: '-') ?>
                        </p>
                    </div>
                </div>
            </div>

        </div>

    </main>

</div>

<?= view('templates/footer') ?>
