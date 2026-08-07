<?= view('templates/header', ['title' => $title ?? 'Cliente | Lunna Gestor']) ?>

<div class="layout">

    <?= view('templates/sidebar') ?>

    <main class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">
                    <?= $cliente ? 'Editar cliente' : 'Novo cliente' ?>
                </h2>
                <p class="text-muted mb-0">
                    Preencha os dados cadastrais do cliente
                </p>
            </div>

            <a href="<?= base_url('/clientes') ?>" class="btn btn-outline-dark">
                Voltar
            </a>
        </div>

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
            $action = $cliente 
                ? base_url('/clientes/atualizar/' . $cliente['id']) 
                : base_url('/clientes/salvar');

            function valorCampo($campo, $cliente) {
                return old($campo) ?? ($cliente[$campo] ?? '');
            }
        ?>

        <form action="<?= $action ?>" method="post">
            <?= csrf_field() ?>

            <div class="card card-dashboard mb-4">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">Dados principais</h5>

                    <div class="row g-3">

                        <div class="col-md-8">
                            <label class="form-label">Nome/Razão social *</label>
                            <input 
                                type="text" 
                                name="nome" 
                                class="form-control" 
                                value="<?= esc(valorCampo('nome', $cliente)) ?>" 
                                required
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tipo de cliente</label>
                            <select name="tipo_cliente" class="form-select">
                                <?php
                                    $tipoAtual = valorCampo('tipo_cliente', $cliente) ?: 'pessoa_fisica';
                                    $tipos = [
                                        'pessoa_fisica' => 'Pessoa física',
                                        'pessoa_juridica' => 'Pessoa jurídica',
                                        'construtora' => 'Construtora',
                                        'condominio' => 'Condomínio',
                                        'arquiteto' => 'Arquiteto',
                                        'outro' => 'Outro'
                                    ];
                                ?>

                                <?php foreach ($tipos as $valor => $label): ?>
                                    <option value="<?= $valor ?>" <?= $tipoAtual === $valor ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">CPF/CNPJ</label>
                            <input 
                                type="text" 
                                name="cpf_cnpj" 
                                class="form-control" 
                                value="<?= esc(valorCampo('cpf_cnpj', $cliente)) ?>"
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Telefone</label>
                            <input 
                                type="text" 
                                name="telefone" 
                                class="form-control" 
                                value="<?= esc(valorCampo('telefone', $cliente)) ?>"
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">WhatsApp</label>
                            <input 
                                type="text" 
                                name="whatsapp" 
                                class="form-control" 
                                value="<?= esc(valorCampo('whatsapp', $cliente)) ?>"
                            >
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">E-mail</label>
                            <input 
                                type="email" 
                                name="email" 
                                class="form-control" 
                                value="<?= esc(valorCampo('email', $cliente)) ?>"
                            >
                        </div>

                    </div>

                </div>
            </div>

            <div class="card card-dashboard mb-4">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">Endereço</h5>

                    <div class="row g-3">

                        <div class="col-md-3">
                            <label class="form-label">CEP</label>
                            <input 
                                type="text" 
                                name="cep" 
                                class="form-control" 
                                value="<?= esc(valorCampo('cep', $cliente)) ?>"
                            >
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Endereço</label>
                            <input 
                                type="text" 
                                name="endereco" 
                                class="form-control" 
                                value="<?= esc(valorCampo('endereco', $cliente)) ?>"
                            >
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Número</label>
                            <input 
                                type="text" 
                                name="numero" 
                                class="form-control" 
                                value="<?= esc(valorCampo('numero', $cliente)) ?>"
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Complemento</label>
                            <input 
                                type="text" 
                                name="complemento" 
                                class="form-control" 
                                value="<?= esc(valorCampo('complemento', $cliente)) ?>"
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Bairro</label>
                            <input 
                                type="text" 
                                name="bairro" 
                                class="form-control" 
                                value="<?= esc(valorCampo('bairro', $cliente)) ?>"
                            >
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Cidade</label>
                            <input 
                                type="text" 
                                name="cidade" 
                                class="form-control" 
                                value="<?= esc(valorCampo('cidade', $cliente) ?: 'São Francisco') ?>"
                            >
                        </div>

                        <div class="col-md-1">
                            <label class="form-label">UF</label>
                            <input 
                                type="text" 
                                name="estado" 
                                class="form-control" 
                                value="<?= esc(valorCampo('estado', $cliente) ?: 'MG') ?>"
                                maxlength="2"
                            >
                        </div>

                    </div>

                </div>
            </div>

            <div class="card card-dashboard mb-4">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">Observações</h5>

                    <textarea 
                        name="observacoes" 
                        class="form-control" 
                        rows="4"
                        placeholder="Exemplo: cliente veio por indicação, prefere atendimento pelo WhatsApp, obra em andamento..."
                    ><?= esc(valorCampo('observacoes', $cliente)) ?></textarea>

                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url('/clientes') ?>" class="btn btn-outline-secondary">
                    Cancelar
                </a>

                <button type="submit" class="btn btn-dark">
                    <?= $cliente ? 'Salvar alterações' : 'Cadastrar cliente' ?>
                </button>
            </div>

        </form>

    </main>

</div>

<?= view('templates/footer') ?>