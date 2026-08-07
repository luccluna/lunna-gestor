<?= view('templates/header', ['title' => $title ?? 'Primeiros passos | Lunna Gestor']) ?>

<div class="layout">

    <?= view('templates/sidebar') ?>

    <main class="main-content">

        <div class="page-title d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <span class="page-kicker">Implantação</span>
                <h2 class="fw-bold mb-1">Primeiros passos</h2>
                <p class="text-muted mb-0">Checklist operacional para colocar a vidraçaria em uso com segurança.</p>
            </div>

            <a href="<?= base_url('/dashboard') ?>" class="btn btn-outline-dark">Voltar ao dashboard</a>
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

        <div class="system-note mb-4">
            <strong>Pronto para operação:</strong>
            revise usuários, produtos, orçamentos, agenda, pagamentos e rotina de backup antes de liberar o acesso da equipe.
        </div>

        <div class="setup-grid">
            <section class="setup-card">
                <div class="setup-card-header">
                    <span class="status-dot status-dot-green"></span>
                    <h5>Base da empresa</h5>
                </div>
                <ul class="setup-list">
                    <li>Confirmar URL final, ambiente de produção e banco de dados.</li>
                    <li>Cadastrar os usuários reais e remover contas de teste.</li>
                    <li>Conferir perfis de acesso: administrador, gerente, vendedor, financeiro, medidor e instalador.</li>
                    <li>Executar um backup completo antes da primeira carga real.</li>
                </ul>
            </section>

            <section class="setup-card">
                <div class="setup-card-header">
                    <span class="status-dot status-dot-blue"></span>
                    <h5>Catálogo comercial</h5>
                </div>
                <ul class="setup-list">
                    <li>Revisar categorias, produtos, serviços e unidades de cálculo.</li>
                    <li>Conferir preços por m², metro linear, unidade e serviço fechado.</li>
                    <li>Testar um orçamento com itens variados e gerar o PDF.</li>
                    <li>Ajustar descrições usadas pela equipe comercial.</li>
                </ul>
            </section>

            <section class="setup-card">
                <div class="setup-card-header">
                    <span class="status-dot status-dot-amber"></span>
                    <h5>Fluxo da operação</h5>
                </div>
                <ol class="setup-steps">
                    <li>Cliente cadastrado.</li>
                    <li>Orçamento emitido e aprovado.</li>
                    <li>Pedido criado automaticamente.</li>
                    <li>Instalação agendada.</li>
                    <li>Pagamento registrado e baixado.</li>
                    <li>Pedido finalizado.</li>
                </ol>
            </section>

            <section class="setup-card">
                <div class="setup-card-header">
                    <span class="status-dot status-dot-red"></span>
                    <h5>Financeiro e rotinas</h5>
                </div>
                <ul class="setup-list">
                    <li>Conferir pagamentos pendentes, atrasados, pagos e cancelados.</li>
                    <li>Agendar a rotina <code>php spark pagamentos:atualizar-atrasados</code>.</li>
                    <li>Validar relatórios do dashboard com pedidos reais.</li>
                    <li>Definir frequência de backup e restauração de teste.</li>
                </ul>
            </section>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-lg-6">
                <div class="card card-dashboard h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Validação antes de liberar</h5>
                        <div class="release-checklist">
                            <label><input type="checkbox"> Login administrativo confirmado</label>
                            <label><input type="checkbox"> Orçamento de teste aprovado e convertido</label>
                            <label><input type="checkbox"> Agenda vinculada a um pedido</label>
                            <label><input type="checkbox"> Pagamento marcado como pago via POST</label>
                            <label><input type="checkbox"> Backup exportado e guardado fora da hospedagem</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card card-dashboard h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Acessos úteis</h5>
                        <div class="doc-links">
                            <a href="<?= base_url('/dashboard') ?>">Dashboard operacional</a>
                            <a href="<?= base_url('/usuarios') ?>">Usuários e perfis</a>
                            <a href="<?= base_url('/produtos') ?>">Produtos e serviços</a>
                            <a href="<?= base_url('/orcamentos') ?>">Orçamentos</a>
                            <a href="<?= base_url('/pagamentos') ?>">Pagamentos</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

</div>

<?= view('templates/footer') ?>
