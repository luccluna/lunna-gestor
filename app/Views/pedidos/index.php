<?= view('templates/header', ['title' => $title ?? 'Pedidos | Lunna Gestor']) ?>

<div class="layout">

    <?= view('templates/sidebar') ?>

    <main class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Pedidos</h2>
                <p class="text-muted mb-0">Acompanhe produção, instalação e finalização dos serviços</p>
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
                <form method="get" action="<?= base_url('/pedidos') ?>" class="row g-2">

                    <div class="col-md-7">
                        <input 
                            type="text" 
                            name="busca" 
                            class="form-control" 
                            placeholder="Buscar por pedido, orçamento, cliente ou WhatsApp"
                            value="<?= esc($busca ?? '') ?>"
                        >
                    </div>

                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">Todos os status</option>

                            <?php
                                $statusOptions = [
                                    'aprovado' => 'Aprovado',
                                    'aguardando_entrada' => 'Aguardando entrada',
                                    'aguardando_material' => 'Aguardando material',
                                    'em_producao' => 'Em produção',
                                    'pronto_para_instalacao' => 'Pronto para instalação',
                                    'instalacao_agendada' => 'Instalação agendada',
                                    'em_instalacao' => 'Em instalação',
                                    'instalado' => 'Instalado',
                                    'finalizado' => 'Finalizado',
                                    'cancelado' => 'Cancelado'
                                ];
                            ?>

                            <?php foreach ($statusOptions as $valor => $label): ?>
                                <option value="<?= $valor ?>" <?= ($status ?? '') === $valor ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-outline-dark">
                            Filtrar
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
                                <th>Pedido</th>
                                <th>Orçamento</th>
                                <th>Cliente</th>
                                <th>Data</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($pedidos)): ?>
                                <?php foreach ($pedidos as $pedido): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($pedido['numero']) ?></strong>
                                        </td>

                                        <td>
                                            <?= esc($pedido['orcamento_numero']) ?>
                                        </td>

                                        <td>
                                            <?= esc($pedido['cliente_nome']) ?>
                                            <?php if (!empty($pedido['cliente_whatsapp'])): ?>
                                                <br>
                                                <small class="text-muted"><?= esc(formatarTelefone($pedido['cliente_whatsapp'])) ?></small>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?= date('d/m/Y', strtotime($pedido['data_pedido'])) ?>
                                        </td>

                                        <td>
                                            <?php
                                                $statusLabels = [
                                                    'aprovado' => 'Aprovado',
                                                    'aguardando_entrada' => 'Aguardando entrada',
                                                    'aguardando_material' => 'Aguardando material',
                                                    'em_producao' => 'Em produção',
                                                    'pronto_para_instalacao' => 'Pronto para instalação',
                                                    'instalacao_agendada' => 'Instalação agendada',
                                                    'em_instalacao' => 'Em instalação',
                                                    'instalado' => 'Instalado',
                                                    'finalizado' => 'Finalizado',
                                                    'cancelado' => 'Cancelado'
                                                ];
                                            ?>

                                            <span class="badge bg-secondary">
                                                <?= esc($statusLabels[$pedido['status']] ?? $pedido['status']) ?>
                                            </span>
                                        </td>

                                        <td>
                                            <strong>R$ <?= number_format($pedido['total'], 2, ',', '.') ?></strong>
                                        </td>

                                        <td class="text-end">
                                            <a 
                                                href="<?= base_url('/pedidos/ver/' . $pedido['id']) ?>" 
                                                class="btn btn-sm btn-outline-dark"
                                            >
                                                Ver
                                            </a>

                                            <?php if (temAcao('pedidos', 'excluir')): ?>
                                                <form 
                                                    action="<?= base_url('/pedidos/excluir/' . $pedido['id']) ?>" 
                                                    method="post" 
                                                    class="d-inline"
                                                    onsubmit="return confirm('Tem certeza que deseja remover este pedido?')"
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
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Nenhum pedido encontrado.
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
