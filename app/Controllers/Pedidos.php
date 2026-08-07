<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PedidoModel;
use App\Models\PedidoStatusHistoricoModel;
use App\Models\OrcamentoItemModel;
use App\Models\PagamentoModel;

class Pedidos extends BaseController
{
    protected $pedidoModel;
    protected $pedidoStatusHistoricoModel;
    protected $orcamentoItemModel;
    protected $pagamentoModel;

    public function __construct()
    {
        $this->pedidoModel = new PedidoModel();
        $this->pedidoStatusHistoricoModel = new PedidoStatusHistoricoModel();
        $this->orcamentoItemModel = new OrcamentoItemModel();
        $this->pagamentoModel = new PagamentoModel();
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

        if ($redirect = bloquearSemPermissao('pedidos', 'visualizar')) {
            return $redirect;
        }

        $busca = $this->request->getGet('busca');
        $status = $this->request->getGet('status');

        $builder = $this->pedidoModel
            ->select('pedidos.*, clientes.nome AS cliente_nome, clientes.whatsapp AS cliente_whatsapp, orcamentos.numero AS orcamento_numero')
            ->join('clientes', 'clientes.id = pedidos.cliente_id')
            ->join('orcamentos', 'orcamentos.id = pedidos.orcamento_id')
            ->where('pedidos.ativo', 1)
            ->orderBy('pedidos.id', 'DESC');

        if (!empty($busca)) {
            $builder->groupStart()
                ->like('pedidos.numero', $busca)
                ->orLike('clientes.nome', $busca)
                ->orLike('clientes.whatsapp', $busca)
                ->orLike('orcamentos.numero', $busca)
                ->groupEnd();
        }

        if (!empty($status)) {
            $builder->where('pedidos.status', $status);
        }

        return view('pedidos/index', [
            'title' => 'Pedidos | Lunna Gestor',
            'pedidos' => $builder->paginate(10),
            'pager' => $this->pedidoModel->pager,
            'busca' => $busca,
            'status' => $status
        ]);
    }

    public function ver($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('pedidos', 'visualizar')) {
            return $redirect;
        }

        $pedido = $this->pedidoModel
            ->select('
                pedidos.*, 
                clientes.nome AS cliente_nome,
                clientes.cpf_cnpj,
                clientes.telefone,
                clientes.whatsapp,
                clientes.email,
                clientes.endereco,
                clientes.numero AS cliente_numero,
                clientes.bairro,
                clientes.cidade,
                clientes.estado,
                orcamentos.numero AS orcamento_numero,
                orcamentos.forma_pagamento,
                orcamentos.prazo_entrega,
                orcamentos.observacoes_cliente
            ')
            ->join('clientes', 'clientes.id = pedidos.cliente_id')
            ->join('orcamentos', 'orcamentos.id = pedidos.orcamento_id')
            ->where('pedidos.id', $id)
            ->first();

        if (!$pedido) {
            return redirect()
                ->to('/pedidos')
                ->with('erro', 'Pedido não encontrado.');
        }

        $itens = $this->orcamentoItemModel
            ->where('orcamento_id', $pedido['orcamento_id'])
            ->orderBy('id', 'ASC')
            ->findAll();

        $historico = $this->pedidoStatusHistoricoModel
            ->where('pedido_id', $id)
            ->orderBy('id', 'DESC')
            ->findAll();

        $pagamentos = $this->pagamentoModel
            ->where('pedido_id', $id)
            ->where('ativo', 1)
            ->orderBy('data_vencimento', 'ASC')
            ->findAll();

        $totalPago = 0;
        $totalPendente = 0;
        $totalAtrasado = 0;
        $totalCancelado = 0;

        foreach ($pagamentos as $pagamento) {
            $valor = (float) $pagamento['valor'];

            if ($pagamento['status'] === 'pago') {
                $totalPago += $valor;
            }

            if ($pagamento['status'] === 'pendente') {
                $totalPendente += $valor;
            }

            if ($pagamento['status'] === 'atrasado') {
                $totalAtrasado += $valor;
            }

            if ($pagamento['status'] === 'cancelado') {
                $totalCancelado += $valor;
            }
        }

        $saldoRestante = max(((float) $pedido['total']) - $totalPago, 0);
        $percentualPago = 0;

        if ((float) $pedido['total'] > 0) {
            $percentualPago = ($totalPago / (float) $pedido['total']) * 100;
            $percentualPago = min($percentualPago, 100);
        }

        $resumoFinanceiro = [
            'total_pedido' => (float) $pedido['total'],
            'total_pago' => $totalPago,
            'total_pendente' => $totalPendente,
            'total_atrasado' => $totalAtrasado,
            'total_cancelado' => $totalCancelado,
            'saldo_restante' => $saldoRestante,
            'percentual_pago' => $percentualPago,
        ];

        return view('pedidos/ver', [
            'title' => 'Pedido ' . $pedido['numero'] . ' | Lunna Gestor',
            'pedido' => $pedido,
            'itens' => $itens,
            'historico' => $historico,
            'pagamentos' => $pagamentos,
            'resumoFinanceiro' => $resumoFinanceiro
        ]);
    }

    public function atualizarStatus($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('pedidos', 'alterar_status')) {
            return $redirect;
        }

        $pedido = $this->pedidoModel->find($id);

        if (!$pedido) {
            return redirect()
                ->to('/pedidos')
                ->with('erro', 'Pedido não encontrado.');
        }

        $novoStatus = $this->request->getPost('status');
        $observacao = $this->request->getPost('observacao');

        if (empty($novoStatus)) {
            return redirect()
                ->to('/pedidos/ver/' . $id)
                ->with('erro', 'Selecione um status válido.');
        }

        $statusAnterior = $pedido['status'];

        if ($statusAnterior === $novoStatus) {
            return redirect()
                ->to('/pedidos/ver/' . $id)
                ->with('erro', 'O pedido já está com este status.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $this->pedidoModel->update($id, [
            'status' => $novoStatus
        ]);

        $this->pedidoStatusHistoricoModel->insert([
            'pedido_id' => $id,
            'status_anterior' => $statusAnterior,
            'status_novo' => $novoStatus,
            'observacao' => $observacao,
            'usuario_id' => session()->get('usuario_id'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()
                ->to('/pedidos/ver/' . $id)
                ->with('erro', 'Erro ao atualizar status do pedido.');
        }

        return redirect()
            ->to('/pedidos/ver/' . $id)
            ->with('sucesso', 'Status do pedido atualizado com sucesso.');
    }

    public function excluir($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('pedidos', 'excluir')) {
            return $redirect;
        }

        $pedido = $this->pedidoModel->find($id);

        if (!$pedido) {
            return redirect()
                ->to('/pedidos')
                ->with('erro', 'Pedido não encontrado.');
        }

        $this->pedidoModel->update($id, ['ativo' => 0]);
        $this->pedidoModel->delete($id);

        return redirect()
            ->to('/pedidos')
            ->with('sucesso', 'Pedido removido com sucesso.');
    }
}
