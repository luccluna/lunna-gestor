<?= view('templates/header', ['title' => $title ?? 'Dashboard | Lunna Gestor']) ?>

<div class="layout">

    <?= view('templates/sidebar') ?>

    <main class="main-content">

        <div class="page-title d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1">Dashboard</h2>
                <p class="text-muted mb-0">Visão geral da Lunna Vidraçaria</p>
            </div>

            <div class="text-end">
                <small class="text-muted">Hoje</small>
                <div class="fw-bold"><?= date('d/m/Y') ?></div>
            </div>

        </div>
            <?php if (session()->getFlashdata('sucesso')): ?>
                <div class="alert alert-success mt-3">
                    <?= session()->getFlashdata('sucesso') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('erro')): ?>
                <div class="alert alert-danger mt-3">
                    <?= session()->getFlashdata('erro') ?>
                </div>
            <?php endif; ?>

        <div class="row g-3 mb-4">

            <div class="col-md-3">
                <div class="card card-dashboard h-100">
                    <div class="card-body">
                        <small class="text-muted">Clientes cadastrados</small>
                        <h3 class="fw-bold mt-2 mb-0"><?= esc($clientesTotal) ?></h3>
                        <small class="text-muted">Clientes ativos no sistema</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-dashboard h-100">
                    <div class="card-body">
                        <small class="text-muted">Orçamentos pendentes</small>
                        <h3 class="fw-bold mt-2 mb-0"><?= esc($orcamentosPendentes) ?></h3>
                        <small class="text-muted">Em aberto ou negociação</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-dashboard h-100">
                    <div class="card-body">
                        <small class="text-muted">Aprovados no mês</small>
                        <h3 class="fw-bold mt-2 mb-0"><?= esc($orcamentosAprovadosMes) ?></h3>
                        <small class="text-muted">Orçamentos aprovados</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-dashboard h-100">
                    <div class="card-body">
                        <small class="text-muted">Pedidos em andamento</small>
                        <h3 class="fw-bold mt-2 mb-0"><?= esc($pedidosEmAndamento) ?></h3>
                        <small class="text-muted">Operação ativa</small>
                    </div>
                </div>
            </div>

        </div>

        <div class="row g-3 mb-4">

            <div class="col-md-3">
                <div class="card card-dashboard h-100">
                    <div class="card-body">
                        <small class="text-muted">Instalações da semana</small>
                        <h3 class="fw-bold mt-2 mb-0"><?= esc($instalacoesSemana) ?></h3>
                        <small class="text-muted">Agenda semanal</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-dashboard h-100">
                    <div class="card-body">
                        <small class="text-muted">Pagamentos pendentes</small>
                        <h3 class="fw-bold mt-2 mb-0"><?= esc($pagamentosPendentes) ?></h3>
                        <small class="text-muted">Ainda não recebidos</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-dashboard h-100 border-danger">
                    <div class="card-body">
                        <small class="text-muted">Pagamentos atrasados</small>
                        <h3 class="fw-bold mt-2 mb-0 text-danger"><?= esc($pagamentosAtrasados) ?></h3>
                        <small class="text-muted">Exigem atenção</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-dashboard h-100">
                    <div class="card-body">
                        <small class="text-muted">Recebido no mês</small>
                        <h3 class="fw-bold mt-2 mb-0">
                            R$ <?= number_format($recebidoMes, 2, ',', '.') ?>
                        </h3>
                        <small class="text-muted">Pagamentos confirmados</small>
                    </div>
                </div>
            </div>

        </div>

        <div class="row g-3 mb-4">

            <div class="col-md-6">
                <div class="card card-dashboard h-100">
                    <div class="card-body">
                        <small class="text-muted">Valor a receber</small>
                        <h3 class="fw-bold mt-2 mb-0">
                            R$ <?= number_format($aReceber, 2, ',', '.') ?>
                        </h3>
                        <small class="text-muted">Pendentes + atrasados</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-dashboard h-100">
                    <div class="card-body">
                        <small class="text-muted">Faturamento previsto do mês</small>
                        <h3 class="fw-bold mt-2 mb-0">
                            R$ <?= number_format($faturamentoPrevistoMes, 2, ',', '.') ?>
                        </h3>
                        <small class="text-muted">Pedidos criados no mês, exceto cancelados</small>
                    </div>
                </div>
            </div>

        </div>

        <?php if ($materiaisBaixoEstoque !== null): ?>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card card-dashboard h-100 <?= $materiaisBaixoEstoque > 0 ? 'border-danger' : '' ?>">
                        <div class="card-body">
                            <small class="text-muted">Materiais em baixo estoque</small>
                            <h3 class="fw-bold mt-2 mb-0 <?= $materiaisBaixoEstoque > 0 ? 'text-danger' : '' ?>">
                                <?= esc($materiaisBaixoEstoque) ?>
                            </h3>
                            <a href="<?= base_url('/estoque?alerta=baixo') ?>" class="btn btn-sm btn-outline-dark mt-3">
                                Ver estoque
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row g-4">

            <div class="col-md-6">
                <div class="card card-dashboard h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Próximos agendamentos</h5>
                            <a href="<?= base_url('/agenda') ?>" class="btn btn-sm btn-outline-dark">Ver agenda</a>
                        </div>

                        <?php
                            $tiposAgenda = [
                                'medicao' => 'Medição',
                                'instalacao' => 'Instalação',
                                'manutencao' => 'Manutenção',
                                'retorno' => 'Retorno',
                                'entrega' => 'Entrega',
                                'visita_comercial' => 'Visita comercial'
                            ];

                            $statusAgenda = [
                                'agendado' => 'Agendado',
                                'confirmado' => 'Confirmado',
                                'em_rota' => 'Em rota',
                                'em_andamento' => 'Em andamento',
                                'concluido' => 'Concluído',
                                'reagendado' => 'Reagendado',
                                'cancelado' => 'Cancelado'
                            ];
                        ?>

                        <?php if (!empty($agendaProximos)): ?>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Compromisso</th>
                                            <th>Cliente</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($agendaProximos as $item): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= date('d/m/Y', strtotime($item['data_agenda'])) ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?= !empty($item['hora_inicio']) ? substr($item['hora_inicio'], 0, 5) : '--:--' ?>
                                                    </small>
                                                </td>

                                                <td>
                                                    <strong><?= esc($item['titulo']) ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?= esc($tiposAgenda[$item['tipo']] ?? $item['tipo']) ?>
                                                        <?= !empty($item['pedido_numero']) ? ' • ' . esc($item['pedido_numero']) : '' ?>
                                                    </small>
                                                </td>

                                                <td><?= esc($item['cliente_nome']) ?></td>

                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <?= esc($statusAgenda[$item['status']] ?? $item['status']) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">Nenhum agendamento futuro encontrado.</p>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-dashboard h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Pedidos recentes</h5>
                            <a href="<?= base_url('/pedidos') ?>" class="btn btn-sm btn-outline-dark">Ver pedidos</a>
                        </div>

                        <?php
                            $statusPedidos = [
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

                        <?php if (!empty($pedidosRecentes)): ?>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Pedido</th>
                                            <th>Cliente</th>
                                            <th>Status</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($pedidosRecentes as $pedido): ?>
                                            <tr>
                                                <td>
                                                    <a href="<?= base_url('/pedidos/ver/' . $pedido['id']) ?>" class="fw-bold">
                                                        <?= esc($pedido['numero']) ?>
                                                    </a>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?= date('d/m/Y', strtotime($pedido['data_pedido'])) ?>
                                                    </small>
                                                </td>

                                                <td><?= esc($pedido['cliente_nome']) ?></td>

                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <?= esc($statusPedidos[$pedido['status']] ?? $pedido['status']) ?>
                                                    </span>
                                                </td>

                                                <td>
                                                    <strong>
                                                        R$ <?= number_format($pedido['total'], 2, ',', '.') ?>
                                                    </strong>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">Nenhum pedido recente encontrado.</p>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

        </div>

        <div class="card card-dashboard mt-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Orçamentos recentes</h5>
                    <a href="<?= base_url('/orcamentos') ?>" class="btn btn-sm btn-outline-dark">Ver orçamentos</a>
                </div>

                <?php
                    $statusOrcamentos = [
                        'novo' => 'Novo',
                        'aguardando_medicao' => 'Aguardando medição',
                        'em_elaboracao' => 'Em elaboração',
                        'enviado' => 'Enviado',
                        'em_negociacao' => 'Em negociação',
                        'aprovado' => 'Aprovado',
                        'recusado' => 'Recusado',
                        'cancelado' => 'Cancelado'
                    ];
                ?>

                <?php if (!empty($orcamentosRecentes)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Orçamento</th>
                                    <th>Cliente</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th class="text-end">Ação</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($orcamentosRecentes as $orcamento): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($orcamento['numero']) ?></strong>
                                        </td>

                                        <td><?= esc($orcamento['cliente_nome']) ?></td>

                                        <td><?= date('d/m/Y', strtotime($orcamento['data_orcamento'])) ?></td>

                                        <td>
                                            <span class="badge bg-secondary">
                                                <?= esc($statusOrcamentos[$orcamento['status']] ?? $orcamento['status']) ?>
                                            </span>
                                        </td>

                                        <td>
                                            <strong>
                                                R$ <?= number_format($orcamento['total'], 2, ',', '.') ?>
                                            </strong>
                                        </td>

                                        <td class="text-end">
                                            <a href="<?= base_url('/orcamentos/ver/' . $orcamento['id']) ?>" class="btn btn-sm btn-outline-dark">
                                                Ver
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">Nenhum orçamento recente encontrado.</p>
                <?php endif; ?>

            </div>
        </div>

    </main>

</div>

<?= view('templates/footer') ?>
