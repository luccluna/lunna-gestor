<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AgendaModel;
use App\Models\ClienteModel;
use App\Models\OrcamentoItemModel;
use App\Models\PedidoModel;

class Agenda extends BaseController
{
    protected $agendaModel;
    protected $clienteModel;
    protected $orcamentoItemModel;
    protected $pedidoModel;

    public function __construct()
    {
        $this->agendaModel = new AgendaModel();
        $this->clienteModel = new ClienteModel();
        $this->orcamentoItemModel = new OrcamentoItemModel();
        $this->pedidoModel = new PedidoModel();
    }

    private function verificarLogin()
    {
        if (!session()->get('logado')) {
            return redirect()->to('/login');
        }

        return null;
    }

    public function index()
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('agenda', 'visualizar')) {
            return $redirect;
        }

        $busca = $this->request->getGet('busca');
        $tipo = $this->request->getGet('tipo');
        $status = $this->request->getGet('status');
        $data = $this->request->getGet('data');

        $builder = $this->agendaModel
            ->select('
                agenda.*,
                clientes.nome AS cliente_nome,
                clientes.whatsapp AS cliente_whatsapp,
                pedidos.numero AS pedido_numero,
                pedidos.orcamento_id AS pedido_orcamento_id
            ')
            ->join('clientes', 'clientes.id = agenda.cliente_id')
            ->join('pedidos', 'pedidos.id = agenda.pedido_id', 'left')
            ->where('agenda.ativo', 1)
            ->orderBy('agenda.data_agenda', 'ASC')
            ->orderBy('agenda.hora_inicio', 'ASC');

        if (!empty($busca)) {
            $builder->groupStart()
                ->like('agenda.titulo', $busca)
                ->orLike('clientes.nome', $busca)
                ->orLike('clientes.whatsapp', $busca)
                ->orLike('pedidos.numero', $busca)
                ->orLike('agenda.responsavel', $busca)
                ->groupEnd();
        }

        if (!empty($tipo)) {
            $builder->where('agenda.tipo', $tipo);
        }

        if (!empty($status)) {
            $builder->where('agenda.status', $status);
        }

        if (!empty($data)) {
            $builder->where('agenda.data_agenda', $data);
        }

        $agenda = $builder->paginate(10);
        $this->adicionarResumoServicosAgenda($agenda);

        return view('agenda/index', [
            'title' => 'Agenda | Lunna Gestor',
            'agenda' => $agenda,
            'pager' => $this->agendaModel->pager,
            'busca' => $busca,
            'tipo' => $tipo,
            'status' => $status,
            'dataFiltro' => $data
        ]);
    }

    public function novo()
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('agenda', 'criar')) {
            return $redirect;
        }

        $pedidoId = $this->request->getGet('pedido_id');

        $clientes = $this->clienteModel
            ->where('ativo', 1)
            ->orderBy('nome', 'ASC')
            ->findAll();

        $pedido = null;

        if (!empty($pedidoId)) {
            $pedido = $this->buscarPedidoComCliente($pedidoId);
        }

        return view('agenda/form', [
            'title' => 'Novo Agendamento | Lunna Gestor',
            'agendamento' => null,
            'clientes' => $clientes,
            'pedido' => $pedido
        ]);
    }

    public function salvar()
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('agenda', 'criar')) {
            return $redirect;
        }

        $dados = $this->tratarDados($this->request->getPost());

        if (!$this->agendaModel->insert($dados)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('erros', $this->agendaModel->errors());
        }

        if (!empty($dados['pedido_id']) && $dados['tipo'] === 'instalacao') {
            $this->pedidoModel->update($dados['pedido_id'], [
                'status' => 'instalacao_agendada'
            ]);
        }

        return redirect()
            ->to('/agenda')
            ->with('sucesso', 'Agendamento criado com sucesso.');
    }

    public function editar($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('agenda', 'editar')) {
            return $redirect;
        }

        $agendamento = $this->agendaModel->find($id);

        if (!$agendamento) {
            return redirect()
                ->to('/agenda')
                ->with('erro', 'Agendamento não encontrado.');
        }

        $clientes = $this->clienteModel
            ->where('ativo', 1)
            ->orderBy('nome', 'ASC')
            ->findAll();

        $pedido = null;

        if (!empty($agendamento['pedido_id'])) {
            $pedido = $this->buscarPedidoComCliente($agendamento['pedido_id']);
        }

        return view('agenda/form', [
            'title' => 'Editar Agendamento | Lunna Gestor',
            'agendamento' => $agendamento,
            'clientes' => $clientes,
            'pedido' => $pedido
        ]);
    }

    public function atualizar($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('agenda', 'editar')) {
            return $redirect;
        }

        $agendamento = $this->agendaModel->find($id);

        if (!$agendamento) {
            return redirect()
                ->to('/agenda')
                ->with('erro', 'Agendamento não encontrado.');
        }

        $dados = $this->tratarDados($this->request->getPost());

        if (!$this->agendaModel->update($id, $dados)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('erros', $this->agendaModel->errors());
        }

        if (!empty($dados['pedido_id']) && $dados['tipo'] === 'instalacao') {
            if ($dados['status'] === 'concluido') {
                $this->pedidoModel->update($dados['pedido_id'], [
                    'status' => 'instalado'
                ]);
            } elseif (in_array($dados['status'], ['agendado', 'confirmado'])) {
                $this->pedidoModel->update($dados['pedido_id'], [
                    'status' => 'instalacao_agendada'
                ]);
            }
        }

        return redirect()
            ->to('/agenda')
            ->with('sucesso', 'Agendamento atualizado com sucesso.');
    }

    public function concluir($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('agenda', 'concluir')) {
            return $redirect;
        }

        $agendamento = $this->agendaModel->find($id);

        if (!$agendamento) {
            return redirect()
                ->to('/agenda')
                ->with('erro', 'Agendamento não encontrado.');
        }

        $this->agendaModel->update($id, [
            'status' => 'concluido'
        ]);

        if (!empty($agendamento['pedido_id']) && $agendamento['tipo'] === 'instalacao') {
            $this->pedidoModel->update($agendamento['pedido_id'], [
                'status' => 'instalado'
            ]);
        }

        return redirect()
            ->to('/agenda')
            ->with('sucesso', 'Agendamento concluído com sucesso.');
    }

    public function excluir($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

         if ($redirect = bloquearSemPermissao('agenda', 'excluir')) {
            return $redirect;
        }

        $agendamento = $this->agendaModel->find($id);

        if (!$agendamento) {
            return redirect()
                ->to('/agenda')
                ->with('erro', 'Agendamento não encontrado.');
        }

        $this->agendaModel->update($id, ['ativo' => 0]);
        $this->agendaModel->delete($id);

        return redirect()
            ->to('/agenda')
            ->with('sucesso', 'Agendamento removido com sucesso.');
    }

    private function tratarDados(array $dados): array
    {
        if (isset($dados['pedido_id']) && $dados['pedido_id'] === '') {
            $dados['pedido_id'] = null;
        }

        if (isset($dados['hora_inicio']) && $dados['hora_inicio'] === '') {
            $dados['hora_inicio'] = null;
        }

        if (isset($dados['hora_fim']) && $dados['hora_fim'] === '') {
            $dados['hora_fim'] = null;
        }

        $dados['usuario_id'] = session()->get('usuario_id');
        $dados['ativo'] = 1;

        return $dados;
    }

    private function buscarPedidoComCliente($pedidoId)
    {
        $pedido = $this->pedidoModel
            ->select('
                pedidos.*,
                clientes.nome AS cliente_nome,
                clientes.id AS cliente_id,
                clientes.whatsapp,
                clientes.endereco,
                clientes.numero AS cliente_numero,
                clientes.complemento,
                clientes.bairro,
                clientes.cidade,
                clientes.estado
            ')
            ->join('clientes', 'clientes.id = pedidos.cliente_id')
            ->where('pedidos.id', $pedidoId)
            ->first();

        if ($pedido) {
            $resumos = $this->resumosServicosPorOrcamentos([(int) $pedido['orcamento_id']]);
            $pedido['servico_resumo'] = $resumos[(int) $pedido['orcamento_id']] ?? '';
            $pedido['servico_titulo'] = $pedido['servico_resumo'] !== ''
                ? $this->tituloServicoAgenda('instalacao', $pedido['servico_resumo'])
                : '';
        }

        return $pedido;
    }

    private function adicionarResumoServicosAgenda(array &$agenda): void
    {
        $orcamentoIds = [];

        foreach ($agenda as $item) {
            if (!empty($item['pedido_orcamento_id'])) {
                $orcamentoIds[] = (int) $item['pedido_orcamento_id'];
            }
        }

        $resumos = $this->resumosServicosPorOrcamentos($orcamentoIds);

        foreach ($agenda as &$item) {
            $resumo = $resumos[(int) ($item['pedido_orcamento_id'] ?? 0)] ?? null;
            $item['servico_resumo'] = $resumo;

            if ($this->tituloGenericoPedido($item['titulo'] ?? '', $item['pedido_numero'] ?? null) && $resumo) {
                $item['titulo_exibicao'] = $this->tituloServicoAgenda($item['tipo'] ?? '', $resumo);
                continue;
            }

            $item['titulo_exibicao'] = $item['titulo'] ?? '';
        }
    }

    private function resumosServicosPorOrcamentos(array $orcamentoIds): array
    {
        $orcamentoIds = array_values(array_unique(array_filter($orcamentoIds)));

        if (empty($orcamentoIds)) {
            return [];
        }

        $itens = $this->orcamentoItemModel
            ->select('orcamento_id, descricao')
            ->whereIn('orcamento_id', $orcamentoIds)
            ->orderBy('id', 'ASC')
            ->findAll();

        $descricoes = [];

        foreach ($itens as $item) {
            $orcamentoId = (int) $item['orcamento_id'];
            $descricao = trim((string) ($item['descricao'] ?? ''));

            if ($descricao === '') {
                continue;
            }

            $descricoes[$orcamentoId][] = $descricao;
        }

        $resumos = [];

        foreach ($descricoes as $orcamentoId => $listaDescricoes) {
            $resumos[$orcamentoId] = $this->resumirDescricoesItens($listaDescricoes);
        }

        return $resumos;
    }

    private function resumirDescricoesItens(array $descricoes): string
    {
        $descricoes = array_values(array_unique(array_filter($descricoes)));

        if (empty($descricoes)) {
            return '';
        }

        if (count($descricoes) === 1) {
            return $descricoes[0];
        }

        $primeiras = array_slice($descricoes, 0, 2);
        $resumo = implode(' + ', $primeiras);
        $restantes = count($descricoes) - count($primeiras);

        if ($restantes > 0) {
            $resumo .= ' +' . $restantes . ' item(ns)';
        }

        return $resumo;
    }

    private function tituloGenericoPedido(string $titulo, ?string $pedidoNumero): bool
    {
        $titulo = trim($titulo);

        if ($titulo === '') {
            return true;
        }

        if ($pedidoNumero && stripos($titulo, $pedidoNumero) !== false) {
            return true;
        }

        return (bool) preg_match('/pedido\s+ped-/i', $titulo);
    }

    private function tituloServicoAgenda(string $tipo, string $resumo): string
    {
        $prefixos = [
            'medicao' => 'Medição',
            'instalacao' => 'Instalação',
            'manutencao' => 'Manutenção',
            'retorno' => 'Retorno',
            'entrega' => 'Entrega',
            'visita_comercial' => 'Visita',
        ];

        $prefixo = $prefixos[$tipo] ?? 'Serviço';

        return $prefixo . ' de ' . $resumo;
    }
}
