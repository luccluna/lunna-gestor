<?= view('templates/header', ['title' => $title ?? 'Usuário | Lunna Gestor']) ?>

<div class="layout">

    <?= view('templates/sidebar') ?>

    <main class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">
                    <?= $usuario ? 'Editar usuário' : 'Novo usuário' ?>
                </h2>
                <p class="text-muted mb-0">
                    Configure dados de acesso ao sistema
                </p>
            </div>

            <a href="<?= base_url('/usuarios') ?>" class="btn btn-outline-dark">
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

        <?php
            $action = $usuario 
                ? base_url('/usuarios/atualizar/' . $usuario['id']) 
                : base_url('/usuarios/salvar');

            $valorCampo = function ($campo) use ($usuario) {
                return old($campo) ?? ($usuario[$campo] ?? '');
            };

            $perfis = [
                'administrador' => 'Administrador',
                'gerente' => 'Gerente',
                'vendedor' => 'Vendedor',
                'financeiro' => 'Financeiro',
                'medidor' => 'Medidor',
                'instalador' => 'Instalador'
            ];

            $perfilAtual = $valorCampo('perfil') ?: 'vendedor';
        ?>

        <form action="<?= $action ?>" method="post">
            <?= csrf_field() ?>

            <div class="card card-dashboard mb-4">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">Dados do usuário</h5>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Nome *</label>
                            <input 
                                type="text" 
                                name="nome" 
                                class="form-control" 
                                required
                                value="<?= esc($valorCampo('nome')) ?>"
                            >
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">E-mail *</label>
                            <input 
                                type="email" 
                                name="email" 
                                class="form-control" 
                                required
                                value="<?= esc($valorCampo('email')) ?>"
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Perfil *</label>
                            <select name="perfil" class="form-select" required>
                                <?php foreach ($perfis as $valor => $label): ?>
                                    <option value="<?= $valor ?>" <?= $perfilAtual === $valor ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php if ($usuario): ?>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check mb-2">
                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        name="ativo" 
                                        id="ativo"
                                        <?= (int) ($usuario['ativo'] ?? 1) === 1 ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="ativo">
                                        Usuário ativo
                                    </label>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>

                </div>
            </div>

            <div class="card card-dashboard mb-4">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">Senha de acesso</h5>

                    <?php if ($usuario): ?>
                        <p class="text-muted">
                            Preencha os campos abaixo somente se quiser alterar a senha.
                        </p>
                    <?php endif; ?>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">
                                Senha <?= $usuario ? '' : '*' ?>
                            </label>
                            <input 
                                type="password" 
                                name="senha" 
                                class="form-control" 
                                <?= $usuario ? '' : 'required' ?>
                            >
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                Confirmar senha <?= $usuario ? '' : '*' ?>
                            </label>
                            <input 
                                type="password" 
                                name="confirmar_senha" 
                                class="form-control" 
                                <?= $usuario ? '' : 'required' ?>
                            >
                        </div>

                    </div>

                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url('/usuarios') ?>" class="btn btn-outline-secondary">
                    Cancelar
                </a>

                <button type="submit" class="btn btn-dark">
                    <?= $usuario ? 'Salvar alterações' : 'Cadastrar usuário' ?>
                </button>
            </div>

        </form>

    </main>

</div>

<?= view('templates/footer') ?>