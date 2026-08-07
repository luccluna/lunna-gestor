<?= view('templates/header', ['title' => $title ?? 'Usuários | Lunna Gestor']) ?>

<div class="layout">

    <?= view('templates/sidebar') ?>

    <main class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Usuários</h2>
                <p class="text-muted mb-0">Gerencie acessos ao sistema</p>
            </div>

            <a href="<?= base_url('/usuarios/novo') ?>" class="btn btn-dark">
                Novo usuário
            </a>
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
                <form method="get" action="<?= base_url('/usuarios') ?>" class="row g-2">

                    <div class="col-md-8">
                        <input 
                            type="text" 
                            name="busca" 
                            class="form-control" 
                            placeholder="Buscar por nome ou e-mail"
                            value="<?= esc($busca ?? '') ?>"
                        >
                    </div>

                    <div class="col-md-2">
                        <select name="perfil" class="form-select">
                            <option value="">Todos os perfis</option>

                            <?php
                                $perfis = [
                                    'administrador' => 'Administrador',
                                    'gerente' => 'Gerente',
                                    'vendedor' => 'Vendedor',
                                    'financeiro' => 'Financeiro',
                                    'medidor' => 'Medidor',
                                    'instalador' => 'Instalador'
                                ];
                            ?>

                            <?php foreach ($perfis as $valor => $label): ?>
                                <option value="<?= $valor ?>" <?= ($perfil ?? '') === $valor ? 'selected' : '' ?>>
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
                                <th>Usuário</th>
                                <th>Perfil</th>
                                <th>Último acesso</th>
                                <th>Status</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($usuarios)): ?>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($usuario['nome']) ?></strong>
                                            <br>
                                            <small class="text-muted"><?= esc($usuario['email']) ?></small>
                                        </td>

                                        <td>
                                            <?= esc($perfis[$usuario['perfil']] ?? $usuario['perfil']) ?>
                                        </td>

                                        <td>
                                            <?= !empty($usuario['ultimo_acesso']) 
                                                ? date('d/m/Y H:i', strtotime($usuario['ultimo_acesso'])) 
                                                : '-' 
                                            ?>
                                        </td>

                                        <td>
                                            <?php if ((int) $usuario['ativo'] === 1): ?>
                                                <span class="badge bg-success">Ativo</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inativo</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-end">
                                            <a 
                                                href="<?= base_url('/usuarios/editar/' . $usuario['id']) ?>" 
                                                class="btn btn-sm btn-outline-dark"
                                            >
                                                Editar
                                            </a>

                                            <?php if ((int) session()->get('usuario_id') !== (int) $usuario['id']): ?>
                                                <form 
                                                    action="<?= base_url('/usuarios/excluir/' . $usuario['id']) ?>" 
                                                    method="post" 
                                                    class="d-inline"
                                                    onsubmit="return confirm('Tem certeza que deseja remover este usuário?')"
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
                                        Nenhum usuário encontrado.
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