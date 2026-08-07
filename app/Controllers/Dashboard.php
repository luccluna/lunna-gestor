<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ClienteModel;
use App\Models\OrcamentoModel;
use App\Models\PedidoModel;
use App\Models\AgendaModel;
use App\Models\PagamentoModel;
use App\Models\EstoqueMaterialModel;

class Dashboard extends BaseController
{
    protected $clienteModel;
    protected $orcamentoModel;
    protected $pedidoModel;
    protected $agendaModel;
    protected $pagamentoModel;
    protected $estoqueMaterialModel;

    public function __construct()
    {
        $this->clienteModel = new ClienteModel();
        $this->orcamentoModel = new OrcamentoModel();
        $this->pedidoModel = new PedidoModel();
        $this->agendaModel = new AgendaModel();
        $this->pagamentoModel = new PagamentoModel();
        $this->estoqueMaterialModel = new EstoqueMaterialModel();
    }

    public function index()
    {
        if (!session()->get('logado')) {
            return redirect()->to('/login');
        }

        if ($redirect = bloquearSemPermissao('dashboard')) {
            return $redirect;
        }

        $hoje = date('Y-m-d');
        $inicioMes = date('Y-m-01');
        $fimMes = date('Y-m-t');

        $inicioSemana = date('Y-m-d', strtotime('monday this week'));
        $fimSemana = date('Y-m-d', strtotime('sunday this week'));

        $clientesTotal = $this->clienteModel
            ->where('ativo', 1)
            ->countAllResults();

        $orcamentosPendentes = $this->orcamentoModel
            ->where('ativo', 1)
            ->whereIn('status', [
                'novo',
                'aguardando_medicao',
                'em_elaboracao',
                'enviado',
                'em_negociacao'
            ])
            ->countAllResults();

        $orcamentosAprovadosMes = $this->orcamentoModel
            ->where('ativo', 1)
            ->where('status', 'aprovado')
            ->where('data_orcamento >=', $inicioMes)
            ->where('data_orcamento <=', $fimMes)
            ->countAllResults();

        $pedidosEmAndamento = $this->pedidoModel
            ->where('ativo', 1)
            ->whereIn('status', [
                'aprovado',
                'aguardando_entrada',
                'aguardando_material',
                'em_producao',
                'pronto_para_instalacao',
                'instalacao_agendada',
                'em_instalacao',
                'instalado'
            ])
            ->countAllResults();

        $instalacoesSemana = $this->agendaModel
            ->where('ativo', 1)
            ->where('tipo', 'instalacao')
            ->where('data_agenda >=', $inicioSemana)
            ->where('data_agenda <=', $fimSemana)
            ->whereNotIn('status', ['cancelado'])
            ->countAllResults();

        $pagamentosPendentes = $this->pagamentoModel
            ->where('ativo', 1)
            ->where('status', 'pendente')
            ->countAllResults();

        $pagamentosAtrasados = $this->pagamentoModel
            ->where('ativo', 1)
            ->where('status', 'atrasado')
            ->countAllResults();

        $recebidoMes = $this->pagamentoModel
            ->selectSum('valor')
            ->where('ativo', 1)
            ->where('status', 'pago')
            ->where('data_pagamento >=', $inicioMes)
            ->where('data_pagamento <=', $fimMes)
            ->first();

        $aReceber = $this->pagamentoModel
            ->selectSum('valor')
            ->where('ativo', 1)
            ->whereIn('status', ['pendente', 'atrasado'])
            ->first();

        $faturamentoPrevistoMes = $this->pedidoModel
            ->selectSum('total')
            ->where('ativo', 1)
            ->where('data_pedido >=', $inicioMes)
            ->where('data_pedido <=', $fimMes)
            ->whereNotIn('status', ['cancelado'])
            ->first();

        $materiaisBaixoEstoque = null;
        $db = \Config\Database::connect();

        if (temPermissao('estoque') && $db->tableExists('estoque_materiais')) {
            $materiaisBaixoEstoque = $this->estoqueMaterialModel
                ->where('ativo', 1)
                ->where('saldo_atual <= estoque_minimo', null, false)
                ->countAllResults();
        }

        $orcamentosRecentes = $this->orcamentoModel
            ->select('orcamentos.*, clientes.nome AS cliente_nome')
            ->join('clientes', 'clientes.id = orcamentos.cliente_id')
            ->where('orcamentos.ativo', 1)
            ->orderBy('orcamentos.id', 'DESC')
            ->limit(5)
            ->findAll();

        $pedidosRecentes = $this->pedidoModel
            ->select('pedidos.*, clientes.nome AS cliente_nome')
            ->join('clientes', 'clientes.id = pedidos.cliente_id')
            ->where('pedidos.ativo', 1)
            ->orderBy('pedidos.id', 'DESC')
            ->limit(5)
            ->findAll();

        $agendaProximos = $this->agendaModel
            ->select('agenda.*, clientes.nome AS cliente_nome, pedidos.numero AS pedido_numero')
            ->join('clientes', 'clientes.id = agenda.cliente_id')
            ->join('pedidos', 'pedidos.id = agenda.pedido_id', 'left')
            ->where('agenda.ativo', 1)
            ->where('agenda.data_agenda >=', $hoje)
            ->whereNotIn('agenda.status', ['cancelado', 'concluido'])
            ->orderBy('agenda.data_agenda', 'ASC')
            ->orderBy('agenda.hora_inicio', 'ASC')
            ->limit(5)
            ->findAll();

        return view('dashboard/index', [
            'title' => 'Dashboard | Lunna Gestor',

            'clientesTotal' => $clientesTotal,
            'orcamentosPendentes' => $orcamentosPendentes,
            'orcamentosAprovadosMes' => $orcamentosAprovadosMes,
            'pedidosEmAndamento' => $pedidosEmAndamento,
            'instalacoesSemana' => $instalacoesSemana,
            'pagamentosPendentes' => $pagamentosPendentes,
            'pagamentosAtrasados' => $pagamentosAtrasados,

            'recebidoMes' => (float) ($recebidoMes['valor'] ?? 0),
            'aReceber' => (float) ($aReceber['valor'] ?? 0),
            'faturamentoPrevistoMes' => (float) ($faturamentoPrevistoMes['total'] ?? 0),
            'materiaisBaixoEstoque' => $materiaisBaixoEstoque,

            'orcamentosRecentes' => $orcamentosRecentes,
            'pedidosRecentes' => $pedidosRecentes,
            'agendaProximos' => $agendaProximos,
        ]);
    }

}
