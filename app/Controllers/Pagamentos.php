<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PagamentoModel;
use App\Models\PedidoModel;

class Pagamentos extends BaseController
{
    protected $pagamentoModel;
    protected $pedidoModel;

    public function __construct()
    {
        $this->pagamentoModel = new PagamentoModel();
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

        if ($redirect = bloquearSemPermissao('pagamentos', 'visualizar')) {
            return $redirect;
        }

        $busca = $this->request->getGet('busca');
        $status = $this->request->getGet('status');
        $forma = $this->request->getGet('forma_pagamento');

        $builder = $this->pagamentoModel
            ->select('
                pagamentos.*, 
                clientes.nome AS cliente_nome,
                clientes.whatsapp AS cliente_whatsapp,
                pedidos.numero AS pedido_numero
            ')
            ->join('clientes', 'clientes.id = pagamentos.cliente_id')
            ->join('pedidos', 'pedidos.id = pagamentos.pedido_id')
            ->where('pagamentos.ativo', 1)
            ->orderBy('pagamentos.data_vencimento', 'ASC');

        if (!empty($busca)) {
            $builder->groupStart()
                ->like('pagamentos.descricao', $busca)
                ->orLike('clientes.nome', $busca)
                ->orLike('clientes.whatsapp', $busca)
                ->orLike('pedidos.numero', $busca)
                ->groupEnd();
        }

        if (!empty($status)) {
            $builder->where('pagamentos.status', $status);
        }

        if (!empty($forma)) {
            $builder->where('pagamentos.forma_pagamento', $forma);
        }

        return view('pagamentos/index', [
            'title' => 'Pagamentos | Lunna Gestor',
            'pagamentos' => $builder->paginate(10),
            'pager' => $this->pagamentoModel->pager,
            'busca' => $busca,
            'status' => $status,
            'formaPagamento' => $forma
        ]);
    }

    public function novo()
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('pagamentos', 'criar')) {
            return $redirect;
        }

        $pedidoId = $this->request->getGet('pedido_id');

        $pedido = null;

        if (!empty($pedidoId)) {
            $pedido = $this->buscarPedidoComCliente($pedidoId);
        }

        $pedidos = $this->pedidoModel
            ->select('pedidos.*, clientes.nome AS cliente_nome')
            ->join('clientes', 'clientes.id = pedidos.cliente_id')
            ->where('pedidos.ativo', 1)
            ->orderBy('pedidos.id', 'DESC')
            ->findAll();

        return view('pagamentos/form', [
            'title' => 'Novo Pagamento | Lunna Gestor',
            'pagamento' => null,
            'pedido' => $pedido,
            'pedidos' => $pedidos,
            'resumoPedido' => $pedido ? $this->resumoFinanceiroPedido((int) $pedido['id']) : null
        ]);
    }

    public function salvar()
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('pagamentos', 'criar')) {
            return $redirect;
        }

        $dados = $this->tratarDados($this->request->getPost());

        if (!$this->pagamentoModel->insert($dados)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('erros', $this->pagamentoModel->errors());
        }

        return redirect()
            ->to('/pagamentos')
            ->with('sucesso', 'Pagamento registrado com sucesso.');
    }

    public function editar($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('pagamentos', 'editar')) {
            return $redirect;
        }

        $pagamento = $this->pagamentoModel->find($id);

        if (!$pagamento) {
            return redirect()
                ->to('/pagamentos')
                ->with('erro', 'Pagamento não encontrado.');
        }

        $pedido = $this->buscarPedidoComCliente($pagamento['pedido_id']);

        $pedidos = $this->pedidoModel
            ->select('pedidos.*, clientes.nome AS cliente_nome')
            ->join('clientes', 'clientes.id = pedidos.cliente_id')
            ->where('pedidos.ativo', 1)
            ->orderBy('pedidos.id', 'DESC')
            ->findAll();

        return view('pagamentos/form', [
            'title' => 'Editar Pagamento | Lunna Gestor',
            'pagamento' => $pagamento,
            'pedido' => $pedido,
            'pedidos' => $pedidos,
            'resumoPedido' => $pedido ? $this->resumoFinanceiroPedido((int) $pedido['id']) : null
        ]);
    }

    public function atualizar($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

         if ($redirect = bloquearSemPermissao('pagamentos', 'editar')) {
            return $redirect;
        }

        $pagamento = $this->pagamentoModel->find($id);

        if (!$pagamento) {
            return redirect()
                ->to('/pagamentos')
                ->with('erro', 'Pagamento não encontrado.');
        }

        $dados = $this->tratarDados($this->request->getPost());

        if (!$this->pagamentoModel->update($id, $dados)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('erros', $this->pagamentoModel->errors());
        }

        return redirect()
            ->to('/pagamentos')
            ->with('sucesso', 'Pagamento atualizado com sucesso.');
    }

    public function marcarPago($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('pagamentos', 'marcar_pago')) {
            return $redirect;
        }

        $pagamento = $this->pagamentoModel->find($id);

        if (!$pagamento) {
            return redirect()
                ->to('/pagamentos')
                ->with('erro', 'Pagamento não encontrado.');
        }

        $this->pagamentoModel->update($id, [
            'status' => 'pago',
            'data_pagamento' => date('Y-m-d')
        ]);

        $redirectPedido = $this->request->getGet('pedido');

        if (!empty($redirectPedido)) {
            return redirect()
                ->to('/pedidos/ver/' . $pagamento['pedido_id'])
                ->with('sucesso', 'Pagamento marcado como pago.');
        }

        return redirect()
            ->back()
            ->with('sucesso', 'Pagamento marcado como pago.');
    }

    public function excluir($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('pagamentos', 'excluir')) {
            return $redirect;
        }

        $pagamento = $this->pagamentoModel->find($id);

        if (!$pagamento) {
            return redirect()
                ->to('/pagamentos')
                ->with('erro', 'Pagamento não encontrado.');
        }

        $this->pagamentoModel->update($id, ['ativo' => 0]);
        $this->pagamentoModel->delete($id);

        return redirect()
            ->to('/pagamentos')
            ->with('sucesso', 'Pagamento removido com sucesso.');
    }

    private function tratarDados(array $dados): array
    {
        $pedido = $this->pedidoModel->find($dados['pedido_id'] ?? null);

        if ($pedido) {
            $dados['cliente_id'] = $pedido['cliente_id'];
        }

        $dados['usuario_id'] = session()->get('usuario_id');
        $dados['valor'] = $this->moedaParaDecimal($dados['valor'] ?? 0);

        if (($dados['status'] ?? '') === 'pago' && empty($dados['data_pagamento'])) {
            $dados['data_pagamento'] = date('Y-m-d');
        }

        if (($dados['status'] ?? '') !== 'pago') {
            $dados['data_pagamento'] = !empty($dados['data_pagamento']) ? $dados['data_pagamento'] : null;
        }

        $dados['ativo'] = 1;

        return $dados;
    }

    private function buscarPedidoComCliente($pedidoId)
    {
        return $this->pedidoModel
            ->select('
                pedidos.*, 
                clientes.nome AS cliente_nome,
                clientes.whatsapp,
                clientes.email
            ')
            ->join('clientes', 'clientes.id = pedidos.cliente_id')
            ->where('pedidos.id', $pedidoId)
            ->first();
    }

    private function resumoFinanceiroPedido(int $pedidoId): array
    {
        $pedido = $this->pedidoModel->find($pedidoId);

        if (!$pedido) {
            return [
                'total_pedido' => 0,
                'total_pago' => 0,
                'saldo_restante' => 0,
            ];
        }

        $pago = $this->pagamentoModel
            ->selectSum('valor')
            ->where('pedido_id', $pedidoId)
            ->where('ativo', 1)
            ->where('status', 'pago')
            ->first();

        $totalPedido = (float) $pedido['total'];
        $totalPago = (float) ($pago['valor'] ?? 0);

        return [
            'total_pedido' => $totalPedido,
            'total_pago' => $totalPago,
            'saldo_restante' => max($totalPedido - $totalPago, 0),
        ];
    }

    private function moedaParaDecimal($valor): float
    {
        if ($valor === null || $valor === '') {
            return 0;
        }

        $valor = str_replace('R$', '', $valor);
        $valor = str_replace(' ', '', $valor);
        $valor = str_replace('.', '', $valor);
        $valor = str_replace(',', '.', $valor);

        return (float) $valor;
    }
}
