<?php
    $rotaAtual = trim(service('uri')->getPath(), '/');
    $ativo = static fn (string $prefixo): string => ($rotaAtual === $prefixo || str_starts_with($rotaAtual, $prefixo . '/')) ? 'active' : '';
?>

<aside class="sidebar">
    <div class="sidebar-header">
        <a class="sidebar-brand" href="<?= base_url('/dashboard') ?>">
            <span class="sidebar-logo-wrap">
                <img src="<?= base_url('assets/img/lunna/logo-lunna-icon-192.png') ?>" alt="Lunna Vidraçaria">
            </span>
            <div>
                <h5>Lunna Gestor</h5>
                <small>Vidraçarias</small>
            </div>
        </a>

        <?php if (session()->get('logado')): ?>
            <div class="sidebar-user">
                <strong><?= esc(session()->get('usuario_nome')) ?></strong>
                <span><?= esc(ucfirst(session()->get('usuario_perfil'))) ?></span>
            </div>
        <?php endif; ?>
    </div>

    <nav class="sidebar-nav">
        <span class="sidebar-section">Operação</span>

        <?php if (temPermissao('dashboard')): ?>
            <a class="<?= $ativo('dashboard') ?>" href="<?= base_url('/dashboard') ?>">Dashboard</a>
        <?php endif; ?>

        <?php if (temPermissao('clientes')): ?>
            <a class="<?= $ativo('clientes') ?>" href="<?= base_url('/clientes') ?>">Clientes</a>
        <?php endif; ?>

        <?php if (temPermissao('produtos')): ?>
            <a class="<?= $ativo('produtos') ?>" href="<?= base_url('/produtos') ?>">Produtos e Serviços</a>
        <?php endif; ?>

        <?php if (temPermissao('estoque')): ?>
            <a class="<?= $ativo('estoque') ?>" href="<?= base_url('/estoque') ?>">Estoque</a>
        <?php endif; ?>

        <?php if (temPermissao('orcamentos')): ?>
            <a class="<?= $ativo('orcamentos') ?>" href="<?= base_url('/orcamentos') ?>">Orçamentos</a>
        <?php endif; ?>

        <?php if (temPermissao('pedidos')): ?>
            <a class="<?= $ativo('pedidos') ?>" href="<?= base_url('/pedidos') ?>">Pedidos</a>
        <?php endif; ?>

        <?php if (temPermissao('agenda')): ?>
            <a class="<?= $ativo('agenda') ?>" href="<?= base_url('/agenda') ?>">Agenda</a>
        <?php endif; ?>

        <?php if (temPermissao('pagamentos')): ?>
            <a class="<?= $ativo('pagamentos') ?>" href="<?= base_url('/pagamentos') ?>">Pagamentos</a>
        <?php endif; ?>

        <?php if (temPermissao('usuarios')): ?>
            <span class="sidebar-section">Administração</span>
            <a class="<?= $ativo('primeiros-passos') ?>" href="<?= base_url('/primeiros-passos') ?>">Primeiros passos</a>
            <a class="<?= $ativo('usuarios') ?>" href="<?= base_url('/usuarios') ?>">Usuários</a>
        <?php endif; ?>

        <a class="sidebar-site-link" href="<?= base_url('/') ?>">Página pública</a>
        <a class="sidebar-logout" href="<?= base_url('/logout') ?>">Sair</a>
    </nav>
</aside>
