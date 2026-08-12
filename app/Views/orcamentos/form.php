<?= view('templates/header', ['title' => $title ?? 'Novo Orçamento | Lunna Gestor']) ?>

<div class="layout">

    <?= view('templates/sidebar') ?>

    <main class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Novo orçamento</h2>
                <p class="text-muted mb-0">Monte uma proposta com cliente, itens, medidas e valores</p>
            </div>

            <a href="<?= base_url('/orcamentos') ?>" class="btn btn-outline-dark">
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

        <form action="<?= base_url('/orcamentos/salvar') ?>" method="post" id="form-orcamento">
            <?= csrf_field() ?>

            <div class="card card-dashboard mb-4">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">Dados do orçamento</h5>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Cliente *</label>
                            <select name="cliente_id" class="form-select" required>
                                <option value="">Selecione o cliente</option>

                                <?php foreach ($clientes as $cliente): ?>
                                    <option value="<?= $cliente['id'] ?>" <?= old('cliente_id') == $cliente['id'] ? 'selected' : '' ?>>
                                        <?= esc($cliente['nome']) ?> 
                                        <?= !empty($cliente['whatsapp']) ? ' - ' . esc(formatarTelefone($cliente['whatsapp'])) : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <small class="text-muted">
                                O cliente precisa estar cadastrado antes de criar o orçamento.
                            </small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Data *</label>
                            <input 
                                type="date" 
                                name="data_orcamento" 
                                class="form-control" 
                                value="<?= old('data_orcamento') ?? date('Y-m-d') ?>" 
                                required
                            >
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Validade</label>
                            <input 
                                type="date" 
                                name="validade" 
                                class="form-control" 
                                value="<?= old('validade') ?>"
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="novo">Novo</option>
                                <option value="aguardando_medicao">Aguardando medição</option>
                                <option value="em_elaboracao">Em elaboração</option>
                                <option value="enviado">Enviado</option>
                                <option value="em_negociacao">Em negociação</option>
                                <option value="aprovado">Aprovado</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Prazo estimado</label>
                            <input 
                                type="text" 
                                name="prazo_entrega" 
                                class="form-control" 
                                placeholder="Exemplo: 10 a 15 dias úteis"
                                value="<?= old('prazo_entrega') ?>"
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Forma de pagamento</label>
                            <input 
                                type="text" 
                                name="forma_pagamento" 
                                class="form-control" 
                                placeholder="Exemplo: entrada + restante na instalação"
                                value="<?= old('forma_pagamento') ?>"
                            >
                        </div>

                    </div>

                </div>
            </div>

            <div class="card card-dashboard mb-4">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Itens do orçamento</h5>
                            <p class="text-muted mb-0">Adicione produtos, medidas, ambientes e valores</p>
                        </div>

                        <button type="button" class="btn btn-outline-dark" id="btn-adicionar-item">
                            Adicionar item
                        </button>
                    </div>

                    <div id="itens-container"></div>

                    <template id="template-item">
                        <div class="orcamento-item border rounded-3 p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <strong class="item-titulo">Item</strong>

                                <button type="button" class="btn btn-sm btn-outline-danger btn-remover-item">
                                    Remover
                                </button>
                            </div>

                            <div class="row g-3">

                                <div class="col-md-3">
                                    <label class="form-label">Ambiente</label>
                                    <input 
                                        type="text" 
                                        class="form-control"
                                        data-name="ambiente"
                                        placeholder="Ex: Banheiro suíte"
                                    >
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label">Produto/Serviço</label>
                                    <select class="form-select produto-select" data-name="produto_servico_id">
                                        <option value="">Selecione</option>

                                        <?php foreach ($produtos as $produto): ?>
                                            <option 
                                                value="<?= $produto['id'] ?>"
                                                data-nome="<?= esc($produto['nome']) ?>"
                                                data-unidade="<?= esc($produto['unidade_calculo']) ?>"
                                                data-valor="<?= esc($produto['valor_base']) ?>"
                                            >
                                                <?= esc($produto['nome']) ?> 
                                                — R$ <?= number_format($produto['valor_base'], 2, ',', '.') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Descrição *</label>
                                    <input 
                                        type="text" 
                                        class="form-control descricao-item"
                                        data-name="descricao"
                                        placeholder="Ex: Box vidro temperado 8mm"
                                        required
                                    >
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Largura</label>
                                    <input 
                                        type="text" 
                                        class="form-control calc-input largura"
                                        data-name="largura"
                                        placeholder="0,00"
                                    >
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Altura</label>
                                    <input 
                                        type="text" 
                                        class="form-control calc-input altura"
                                        data-name="altura"
                                        placeholder="0,00"
                                    >
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Qtd.</label>
                                    <input 
                                        type="text" 
                                        class="form-control calc-input quantidade"
                                        data-name="quantidade"
                                        value="1"
                                    >
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Unidade</label>
                                    <select class="form-select unidade-calculo calc-input" data-name="unidade_calculo">
                                        <option value="m2">m²</option>
                                        <option value="metro_linear">Metro linear</option>
                                        <option value="unidade">Unidade</option>
                                        <option value="servico_fechado">Serviço fechado</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Área m²</label>
                                    <input 
                                        type="text" 
                                        class="form-control area-m2"
                                        data-name="area_m2"
                                        value="0,000"
                                        readonly
                                    >
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Valor unitário</label>
                                    <input 
                                        type="text" 
                                        class="form-control calc-input money valor-unitario"
                                        data-name="valor_unitario"
                                        value="0,00"
                                    >
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Total item</label>
                                    <input 
                                        type="text" 
                                        class="form-control valor-total-item"
                                        data-name="valor_total"
                                        value="0,00"
                                        readonly
                                    >
                                </div>

                                <div class="col-md-9">
                                    <label class="form-label">Observações do item</label>
                                    <input 
                                        type="text" 
                                        class="form-control"
                                        data-name="observacoes"
                                        placeholder="Ex: parede fora de esquadro, cliente pediu acabamento específico..."
                                    >
                                </div>

                            </div>
                        </div>
                    </template>

                </div>
            </div>

            <div class="card card-dashboard mb-4">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">Totais</h5>

                    <div class="row g-3 justify-content-end">

                        <div class="col-md-3">
                            <label class="form-label">Subtotal</label>
                            <input 
                                type="text" 
                                id="subtotal" 
                                class="form-control" 
                                value="0,00" 
                                readonly
                            >
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Desconto</label>
                            <input 
                                type="text" 
                                name="desconto" 
                                id="desconto" 
                                class="form-control money" 
                                value="0,00"
                            >
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Total</label>
                            <input 
                                type="text" 
                                id="total_geral" 
                                class="form-control fw-bold" 
                                value="0,00" 
                                readonly
                            >
                        </div>

                    </div>

                </div>
            </div>

            <div class="card card-dashboard mb-4">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">Observações</h5>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Observações para o cliente</label>
                            <textarea 
                                name="observacoes_cliente" 
                                class="form-control" 
                                rows="4"
                                placeholder="Ex: valores sujeitos à conferência após medição técnica."
                            ><?= old('observacoes_cliente') ?></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Observações internas</label>
                            <textarea 
                                name="observacoes_internas" 
                                class="form-control" 
                                rows="4"
                                placeholder="Anotações que não aparecem para o cliente."
                            ><?= old('observacoes_internas') ?></textarea>
                        </div>

                    </div>

                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url('/orcamentos') ?>" class="btn btn-outline-secondary">
                    Cancelar
                </a>

                <button type="submit" class="btn btn-dark">
                    Salvar orçamento
                </button>
            </div>

        </form>

    </main>

</div>

<?= view('templates/footer') ?>
