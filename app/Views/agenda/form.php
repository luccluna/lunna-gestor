<?= view('templates/header', ['title' => $title ?? 'Agendamento | Lunna Gestor']) ?>

<div class="layout">

    <?= view('templates/sidebar') ?>

    <main class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">
                    <?= $agendamento ? 'Editar agendamento' : 'Novo agendamento' ?>
                </h2>
                <p class="text-muted mb-0">
                    Organize medições, instalações e visitas da equipe
                </p>
            </div>

            <a href="<?= base_url('/agenda') ?>" class="btn btn-outline-dark">
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
            $action = $agendamento 
                ? base_url('/agenda/atualizar/' . $agendamento['id']) 
                : base_url('/agenda/salvar');

            $valorCampo = function ($campo) use ($agendamento, $pedido) {
                if (old($campo) !== null) {
                    return old($campo);
                }

                if ($agendamento && array_key_exists($campo, $agendamento)) {
                    return $agendamento[$campo];
                }

                if ($pedido) {
                    $mapa = [
                        'cliente_id' => 'cliente_id',
                        'pedido_id' => 'id',
                        'titulo' => 'servico_titulo',
                        'endereco' => 'endereco',
                        'numero' => 'cliente_numero',
                        'complemento' => 'complemento',
                        'bairro' => 'bairro',
                        'cidade' => 'cidade',
                        'estado' => 'estado'
                    ];

                    if (isset($mapa[$campo]) && isset($pedido[$mapa[$campo]])) {
                        return $pedido[$mapa[$campo]];
                    }
                }

                return '';
            };

            $tipos = [
                'medicao' => 'Medição',
                'instalacao' => 'Instalação',
                'manutencao' => 'Manutenção',
                'retorno' => 'Retorno',
                'entrega' => 'Entrega',
                'visita_comercial' => 'Visita comercial'
            ];

            $statusOptions = [
                'agendado' => 'Agendado',
                'confirmado' => 'Confirmado',
                'em_rota' => 'Em rota',
                'em_andamento' => 'Em andamento',
                'concluido' => 'Concluído',
                'reagendado' => 'Reagendado',
                'cancelado' => 'Cancelado'
            ];

            $tipoAtual = $valorCampo('tipo') ?: 'medicao';
            $statusAtual = $valorCampo('status') ?: 'agendado';
            $clientesEnderecos = [];

            foreach ($clientes as $cliente) {
                $clientesEnderecos[$cliente['id']] = [
                    'endereco' => $cliente['endereco'] ?? '',
                    'numero' => $cliente['numero'] ?? '',
                    'complemento' => $cliente['complemento'] ?? '',
                    'bairro' => $cliente['bairro'] ?? '',
                    'cidade' => $cliente['cidade'] ?? '',
                    'estado' => $cliente['estado'] ?? '',
                ];
            }
        ?>

        <?php if ($pedido): ?>
            <div class="alert alert-info">
                Agendamento vinculado ao pedido 
                <strong><?= esc($pedido['numero']) ?></strong>
                do cliente 
                <strong><?= esc($pedido['cliente_nome']) ?></strong>.
            </div>
        <?php endif; ?>

        <form action="<?= $action ?>" method="post">
            <?= csrf_field() ?>

            <div class="card card-dashboard mb-4">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">Dados do compromisso</h5>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Cliente *</label>
                            <select name="cliente_id" id="agenda-cliente" class="form-select" required>
                                <option value="">Selecione o cliente</option>

                                <?php foreach ($clientes as $cliente): ?>
                                    <option 
                                        value="<?= $cliente['id'] ?>"
                                        <?= (string) $valorCampo('cliente_id') === (string) $cliente['id'] ? 'selected' : '' ?>
                                    >
                                        <?= esc($cliente['nome']) ?>
                                        <?= !empty($cliente['whatsapp']) ? ' - ' . esc(formatarTelefone($cliente['whatsapp'])) : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Pedido vinculado</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                value="<?= $pedido ? esc($pedido['numero']) : ($valorCampo('pedido_id') ? 'Pedido ID ' . esc($valorCampo('pedido_id')) : 'Sem pedido') ?>"
                                readonly
                            >

                            <input 
                                type="hidden" 
                                name="pedido_id" 
                                value="<?= esc($valorCampo('pedido_id')) ?>"
                            >
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tipo *</label>
                            <select name="tipo" class="form-select" required>
                                <?php foreach ($tipos as $valor => $label): ?>
                                    <option value="<?= $valor ?>" <?= $tipoAtual === $valor ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Título *</label>
                            <input 
                                type="text" 
                                name="titulo" 
                                class="form-control" 
                                required
                                value="<?= esc($valorCampo('titulo') ?: ($pedido ? 'Instalação do pedido ' . $pedido['numero'] : '')) ?>"
                                placeholder="Exemplo: Medição box banheiro suíte"
                            >
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Data *</label>
                            <input 
                                type="date" 
                                name="data_agenda" 
                                class="form-control"
                                required
                                value="<?= esc($valorCampo('data_agenda')) ?>"
                            >
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Status *</label>
                            <select name="status" class="form-select" required>
                                <?php foreach ($statusOptions as $valor => $label): ?>
                                    <option value="<?= $valor ?>" <?= $statusAtual === $valor ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Hora início</label>
                            <input 
                                type="time" 
                                name="hora_inicio" 
                                class="form-control"
                                value="<?= esc($valorCampo('hora_inicio')) ?>"
                            >
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Hora fim</label>
                            <input 
                                type="time" 
                                name="hora_fim" 
                                class="form-control"
                                value="<?= esc($valorCampo('hora_fim')) ?>"
                            >
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Responsável/equipe</label>
                            <input 
                                type="text" 
                                name="responsavel" 
                                class="form-control"
                                value="<?= esc($valorCampo('responsavel')) ?>"
                                placeholder="Exemplo: João e equipe de instalação"
                            >
                        </div>

                    </div>

                </div>
            </div>

            <div class="card card-dashboard mb-4">
                <div class="card-body">

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Endereço do compromisso</h5>
                            <small class="text-muted">Use o endereço do cliente como base ou altere para outro local de obra.</small>
                        </div>

                        <button type="button" id="agenda-usar-endereco-cliente" class="btn btn-outline-dark btn-sm">
                            Usar endereço do cliente
                        </button>
                    </div>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Endereço</label>
                            <input 
                                type="text" 
                                name="endereco" 
                                id="agenda-endereco"
                                class="form-control"
                                value="<?= esc($valorCampo('endereco')) ?>"
                            >
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Número</label>
                            <input 
                                type="text" 
                                name="numero" 
                                id="agenda-numero"
                                class="form-control"
                                value="<?= esc($valorCampo('numero')) ?>"
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Complemento</label>
                            <input 
                                type="text" 
                                name="complemento" 
                                id="agenda-complemento"
                                class="form-control"
                                value="<?= esc($valorCampo('complemento')) ?>"
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Bairro</label>
                            <input 
                                type="text" 
                                name="bairro" 
                                id="agenda-bairro"
                                class="form-control"
                                value="<?= esc($valorCampo('bairro')) ?>"
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Cidade</label>
                            <input 
                                type="text" 
                                name="cidade" 
                                id="agenda-cidade"
                                class="form-control"
                                value="<?= esc($valorCampo('cidade') ?: 'São Francisco') ?>"
                            >
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">UF</label>
                            <input 
                                type="text" 
                                name="estado" 
                                id="agenda-estado"
                                class="form-control"
                                maxlength="2"
                                value="<?= esc($valorCampo('estado') ?: 'MG') ?>"
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
                        placeholder="Exemplo: confirmar com cliente antes de sair, levar ferragens, local com acesso difícil..."
                    ><?= esc($valorCampo('observacoes')) ?></textarea>

                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url('/agenda') ?>" class="btn btn-outline-secondary">
                    Cancelar
                </a>

                <button type="submit" class="btn btn-dark">
                    <?= $agendamento ? 'Salvar alterações' : 'Cadastrar agendamento' ?>
                </button>
            </div>

        </form>

    </main>

</div>

<script>
    window.lunnaAgendaClientes = <?= json_encode($clientesEnderecos, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
</script>

<?= view('templates/footer') ?>
