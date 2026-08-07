<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AgendaModel;
use App\Models\ClienteModel;
use App\Models\PedidoModel;

class Agenda extends BaseController
{
    protected $agendaModel;
    protected $clienteModel;
    protected $pedidoModel;

    public function __construct()
    {
        $this->agendaModel = new AgendaModel();
        $this->clienteModel = new ClienteModel();
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
            ->select('agenda.*, clientes.nome AS cliente_nome, clientes.whatsapp AS cliente_whatsapp, pedidos.numero AS pedido_numero')
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

        return view('agenda/index', [
            'title' => 'Agenda | Lunna Gestor',
            'agenda' => $builder->paginate(10),
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
        return $this->pedidoModel
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
    }
}