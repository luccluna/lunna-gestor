<?php
    $logo = base_url('assets/img/lunna/logo-lunna-horizontal-720.png');
    $logoIcon = base_url('assets/img/lunna/logo-lunna-icon-192.png');
    $hero = base_url('assets/img/lunna/hero-portas-de-vidro.jpg');
?>

<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Login | Lunna Gestor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#006634">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/lunna/favicon.png') ?>">
    <link href="<?= base_url('assets/css/style.css?v=20260811-7') ?>" rel="stylesheet">
</head>

<body class="login-page" style="--login-hero-image: url('<?= $hero ?>');">

    <main class="login-shell">
        <section class="login-panel" aria-label="Resumo do sistema">
            <div class="login-panel-content">
                <img class="login-panel-logo" src="<?= $logo ?>" alt="Lunna Vidraçaria">
                <span class="login-badge">Gestão para vidraçarias</span>
                <h1>Lunna Gestor</h1>
                <p>
                    A área interna da Lunna conecta clientes, orçamentos, pedidos, agenda de instalação e recebimentos em um fluxo único.
                </p>
            </div>

            <div class="login-metrics">
                <div>
                    <strong>Orçamento</strong>
                    <span>PDF e aprovação</span>
                </div>
                <div>
                    <strong>Instalação</strong>
                    <span>Agenda por pedido</span>
                </div>
                <div>
                    <strong>Financeiro</strong>
                    <span>Pendentes e pagos</span>
                </div>
            </div>
        </section>

        <section class="login-card" aria-label="Acesso ao sistema">
            <div class="login-card-header">
                <img class="login-card-logo" src="<?= $logoIcon ?>" alt="Lunna Vidraçaria">
                <div>
                    <h2>Acessar sistema</h2>
                    <p>Entre com seu usuário da vidraçaria.</p>
                </div>
            </div>

            <?php if (session()->getFlashdata('erro')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('erro') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('sucesso')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('sucesso') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('/login') ?>" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" placeholder="usuario@vidracaria.com.br" autocomplete="email" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Senha</label>
                    <input type="password" name="senha" class="form-control" placeholder="Digite sua senha" autocomplete="current-password" required>
                </div>

                <button type="submit" class="btn btn-dark w-100">
                    Entrar
                </button>
            </form>

            <div class="login-footer">
                <a href="<?= base_url('/') ?>">Ver página de apresentação</a>
                <span>Lunna Vidraçaria © <?= date('Y') ?></span>
            </div>
        </section>
    </main>

</body>
</html>
