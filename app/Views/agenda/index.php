<?= view('templates/header', ['title' => $title ?? 'Agenda | Lunna Gestor']) ?>

<div class="layout">

    <?= view('templates/sidebar') ?>

    <main class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Agenda</h2>
                <p class="text-muted mb-0">Controle medições, instalações, manutenções e visitas técnicas</p>
            </div>

        <?php if (temAcao('agenda', 'criar')): ?>
            <a href="<?= base_url('/agenda/novo') ?>" class="btn btn-dark">
                Novo agendamento
            </a>
        <?php endif; ?>
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
                <form method="get" action="<?= base_url('/agenda') ?>" class="row g-2">

                    <div class="col-md-4">
                        <input 
                            type="text" 
                            name="busca" 
                            class="form-control" 
                            placeholder="Buscar por cliente, pedido, responsável ou título"
                            value="<?= esc($busca ?? '') ?>"
                        >
                    </div>

                    <div class="col-md-2">
                        <input 
                            type="date" 
                            name="data" 
                            class="form-control"
                            value="<?= esc($dataFiltro ?? '') ?>"
                        >
                    </div>

                    <div class="col-md-2">
                        <select name="tipo" class="form-select">
                            <option value="">Todos os tipos</option>

                            <?php
                                $tipos = [
                                    'medicao' => 'Medição',
                                    'instalacao' => 'Instalação',
                                    'manutencao' => 'Manutenção',
                                    'retorno' => 'Retorno',
                                    'entrega' => 'Entrega',
                                    'visita_comercial' => 'Visita comercial'
                                ];
                            ?>

                            <?php foreach ($tipos as $valor => $label): ?>
                                <option value="<?= $valor ?>" <?= ($tipo ?? '') === $valor ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">Todos os status</option>

                            <?php
                                $statusOptions = [
                                    'agendado' => 'Agendado',
                                    'confirmado' => 'Confirmado',
                                    'em_rota' => 'Em rota',
                                    'em_andamento' => 'Em andamento',
                                    'concluido' => 'Concluído',
                                    'reagendado' => 'Reagendado',
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
                                <th>Data/Hora</th>
                                <th>Compromisso</th>
                                <th>Cliente</th>
                                <th>Pedido</th>
                                <th>Responsável</th>
                                <th>Status</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($agenda)): ?>
                                <?php foreach ($agenda as $item): ?>
                                    <?php $tituloAgenda = $item['titulo_exibicao'] ?? $item['titulo']; ?>
                                    <tr>
                                        <td>
                                            <strong><?= date('d/m/Y', strtotime($item['data_agenda'])) ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <?= !empty($item['hora_inicio']) ? substr($item['hora_inicio'], 0, 5) : '--:--' ?>
                                                <?= !empty($item['hora_fim']) ? ' às ' . substr($item['hora_fim'], 0, 5) : '' ?>
                                            </small>
                                        </td>

                                        <td>
                                            <strong><?= esc($tituloAgenda) ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <?= esc($tipos[$item['tipo']] ?? $item['tipo']) ?>
                                                <?php if (
                                                    !empty($item['servico_resumo']) &&
                                                    stripos((string) $tituloAgenda, (string) $item['servico_resumo']) === false
                                                ): ?>
                                                    · <?= esc($item['servico_resumo']) ?>
                                                <?php endif; ?>
                                            </small>
                                        </td>

                                        <td>
                                            <?= esc($item['cliente_nome']) ?>
                                            <?php if (!empty($item['cliente_whatsapp'])): ?>
                                                <br>
                                                <small class="text-muted"><?= esc($item['cliente_whatsapp']) ?></small>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?= esc($item['pedido_numero'] ?? '-') ?>
                                        </td>

                                        <td>
                                            <?= esc($item['responsavel'] ?: '-') ?>
                                        </td>

                                        <td>
                                            <?php
                                                $statusClasses = [
                                                    'agendado' => 'bg-secondary',
                                                    'confirmado' => 'bg-primary',
                                                    'em_rota' => 'bg-warning text-dark',
                                                    'em_andamento' => 'bg-warning text-dark',
                                                    'concluido' => 'bg-success',
                                                    'reagendado' => 'bg-info text-dark',
                                                    'cancelado' => 'bg-danger'
                                                ];
                                            ?>

                                            <span class="badge <?= esc($statusClasses[$item['status']] ?? 'bg-secondary') ?>">
                                                <?= esc($statusOptions[$item['status']] ?? $item['status']) ?>
                                            </span>
                                        </td>

                                        <td class="text-end">
                                            <?php if (
                                                temAcao('agenda', 'concluir') && 
                                                !in_array($item['status'], ['concluido', 'cancelado'])
                                            ): ?>
                                            <form 
                                                action="<?= base_url('/agenda/concluir/' . $item['id']) ?>" 
                                                method="post" 
                                                class="d-inline"
                                                onsubmit="return confirm('Marcar este agendamento como concluído?')"
                                            >
                                                <?= csrf_field() ?>

                                                <button type="submit" class="btn btn-sm btn-outline-success">
                                                    Concluir
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if (temAcao('agenda', 'editar')): ?>
                                            <a 
                                                href="<?= base_url('/agenda/editar/' . $item['id']) ?>" 
                                                class="btn btn-sm btn-outline-dark"
                                            >
                                                Editar
                                            </a>
                                        <?php endif; ?>

                                        <?php if (temAcao('agenda', 'excluir')): ?>
                                            <form 
                                                action="<?= base_url('/agenda/excluir/' . $item['id']) ?>" 
                                                method="post" 
                                                class="d-inline"
                                                onsubmit="return confirm('Tem certeza que deseja remover este agendamento?')"
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
                                        Nenhum agendamento encontrado.
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
