<?= view('templates/header', ['title' => $title ?? 'Clientes | Lunna Gestor']) ?>

<div class="layout">

    <?= view('templates/sidebar') ?>

    <main class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Clientes</h2>
                <p class="text-muted mb-0">Gerencie os clientes da Lunna Vidraçaria</p>
            </div>

            <?php if (temAcao('clientes', 'criar')): ?>
                <a href="<?= base_url('/clientes/novo') ?>" class="btn btn-dark">
                    Novo cliente
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
                <form method="get" action="<?= base_url('/clientes') ?>" class="row g-2">
                    <div class="col-md-10">
                        <input 
                            type="text" 
                            name="busca" 
                            class="form-control" 
                            placeholder="Buscar por nome, telefone, WhatsApp, cidade ou CPF/CNPJ"
                            value="<?= esc($busca ?? '') ?>"
                        >
                    </div>

                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-outline-dark">
                            Buscar
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
                                <th>Cliente</th>
                                <th>Tipo</th>
                                <th>WhatsApp</th>
                                <th>Cidade</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($clientes)): ?>
                                <?php foreach ($clientes as $cliente): ?>
                                    <?php
                                        $telefoneContato = $cliente['whatsapp'] ?: $cliente['telefone'] ?: null;
                                        $whatsappClienteUrl = whatsappUrl(
                                            $telefoneContato,
                                            'Olá, ' . $cliente['nome'] . '! Aqui é da Lunna Vidraçaria.'
                                        );
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($cliente['nome']) ?></strong>
                                            <?php if (!empty($cliente['cpf_cnpj'])): ?>
                                                <br>
                                                <small class="text-muted"><?= esc(formatarCpfCnpj($cliente['cpf_cnpj'])) ?></small>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?= esc(str_replace('_', ' ', ucfirst($cliente['tipo_cliente']))) ?>
                                        </td>

                                        <td>
                                            <?= esc(formatarTelefone($cliente['whatsapp'] ?: $cliente['telefone'] ?: '') ?: '-') ?>
                                        </td>

                                        <td>
                                            <?= esc($cliente['cidade']) ?>/<?= esc($cliente['estado']) ?>
                                        </td>

                                        <td class="text-end">
                                            <?php if ($whatsappClienteUrl): ?>
                                                <a
                                                    href="<?= esc($whatsappClienteUrl) ?>"
                                                    class="btn btn-sm btn-success"
                                                    target="_blank"
                                                    rel="noopener"
                                                >
                                                    WhatsApp
                                                </a>
                                            <?php endif; ?>

                                            <?php if (temAcao('clientes', 'editar')): ?>
                                                <a 
                                                    href="<?= base_url('/clientes/editar/' . $cliente['id']) ?>" 
                                                    class="btn btn-sm btn-outline-dark"
                                                >
                                                    Editar
                                                </a>
                                            <?php endif; ?>

                                            <?php if (temAcao('clientes', 'excluir')): ?>
                                                <form 
                                                    action="<?= base_url('/clientes/excluir/' . $cliente['id']) ?>" 
                                                    method="post" 
                                                    class="d-inline"
                                                    onsubmit="return confirm('Tem certeza que deseja remover este cliente?')"
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
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Nenhum cliente encontrado.
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
