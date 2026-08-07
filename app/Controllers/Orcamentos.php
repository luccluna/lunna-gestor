<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OrcamentoModel;
use App\Models\OrcamentoItemModel;
use App\Models\ClienteModel;
use App\Models\ProdutoServicoModel;
use App\Models\PedidoModel;
use App\Models\PedidoStatusHistoricoModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Orcamentos extends BaseController
{
    protected $orcamentoModel;
    protected $orcamentoItemModel;
    protected $clienteModel;
    protected $produtoModel;
    protected $pedidoModel;
    protected $pedidoStatusHistoricoModel;

    public function __construct()
    {
        $this->orcamentoModel = new OrcamentoModel();
        $this->orcamentoItemModel = new OrcamentoItemModel();
        $this->clienteModel = new ClienteModel();
        $this->produtoModel = new ProdutoServicoModel();
        $this->pedidoModel = new PedidoModel();
        $this->pedidoStatusHistoricoModel = new PedidoStatusHistoricoModel();
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

        if ($redirect = bloquearSemPermissao('orcamentos', 'visualizar')) {
            return $redirect;
        }

        $busca = $this->request->getGet('busca');
        $status = $this->request->getGet('status');

        $builder = $this->orcamentoModel
            ->select('orcamentos.*, clientes.nome AS cliente_nome, clientes.whatsapp AS cliente_whatsapp')
            ->join('clientes', 'clientes.id = orcamentos.cliente_id')
            ->where('orcamentos.ativo', 1)
            ->orderBy('orcamentos.id', 'DESC');

        if (!empty($busca)) {
            $builder->groupStart()
                ->like('orcamentos.numero', $busca)
                ->orLike('clientes.nome', $busca)
                ->orLike('clientes.whatsapp', $busca)
                ->groupEnd();
        }

        if (!empty($status)) {
            $builder->where('orcamentos.status', $status);
        }

        return view('orcamentos/index', [
            'title' => 'Orçamentos | Lunna Gestor',
            'orcamentos' => $builder->paginate(10),
            'pager' => $this->orcamentoModel->pager,
            'busca' => $busca,
            'status' => $status
        ]);
    }

    public function novo()
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('orcamentos', 'criar')) {
            return $redirect;
        }

        $clientes = $this->clienteModel
            ->where('ativo', 1)
            ->orderBy('nome', 'ASC')
            ->findAll();

        $produtos = $this->produtoModel
            ->where('ativo', 1)
            ->orderBy('nome', 'ASC')
            ->findAll();

        return view('orcamentos/form', [
            'title' => 'Novo Orçamento | Lunna Gestor',
            'clientes' => $clientes,
            'produtos' => $produtos
        ]);
    }

    public function salvar()
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('orcamentos', 'criar')) {
            return $redirect;
        }

        $post = $this->request->getPost();
        $itens = $post['itens'] ?? [];

        if (empty($itens)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('erro', 'Adicione pelo menos um item ao orçamento.');
        }

        $subtotal = 0;

        foreach ($itens as $item) {
            $subtotal += $this->moedaParaDecimal($item['valor_total'] ?? 0);
        }

        $desconto = $this->moedaParaDecimal($post['desconto'] ?? 0);
        $total = max($subtotal - $desconto, 0);

        $dadosOrcamento = [
            'cliente_id' => $post['cliente_id'] ?? null,
            'usuario_id' => session()->get('usuario_id'),
            'data_orcamento' => $post['data_orcamento'] ?? date('Y-m-d'),
            'validade' => !empty($post['validade']) ? $post['validade'] : null,
            'prazo_entrega' => $post['prazo_entrega'] ?? null,
            'status' => $post['status'] ?? 'novo',
            'subtotal' => $subtotal,
            'desconto' => $desconto,
            'total' => $total,
            'forma_pagamento' => $post['forma_pagamento'] ?? null,
            'observacoes_cliente' => $post['observacoes_cliente'] ?? null,
            'observacoes_internas' => $post['observacoes_internas'] ?? null,
            'ativo' => 1
        ];

        $db = \Config\Database::connect();

        try {
            $db->transBegin();

            $orcamentoId = $this->inserirOrcamentoComNumero($dadosOrcamento);

            if (!$orcamentoId) {
                $db->transRollback();

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('erros', $this->orcamentoModel->errors())
                    ->with('erro', 'Erro ao salvar os dados principais do orçamento.');
            }

            foreach ($itens as $item) {
                if (empty($item['descricao'])) {
                    continue;
                }

                $largura = $this->numeroParaDecimal($item['largura'] ?? 0);
                $altura = $this->numeroParaDecimal($item['altura'] ?? 0);
                $quantidade = $this->numeroParaDecimal($item['quantidade'] ?? 1);
                $valorUnitario = $this->moedaParaDecimal($item['valor_unitario'] ?? 0);
                $valorTotal = $this->moedaParaDecimal($item['valor_total'] ?? 0);
                $areaM2 = $this->numeroParaDecimal($item['area_m2'] ?? 0);

                $dadosItem = [
                    'orcamento_id' => $orcamentoId,
                    'produto_servico_id' => !empty($item['produto_servico_id']) ? $item['produto_servico_id'] : null,
                    'ambiente' => $item['ambiente'] ?? null,
                    'descricao' => $item['descricao'],
                    'largura' => $largura,
                    'altura' => $altura,
                    'quantidade' => $quantidade,
                    'unidade_calculo' => $item['unidade_calculo'] ?? 'm2',
                    'area_m2' => $areaM2,
                    'valor_unitario' => $valorUnitario,
                    'valor_total' => $valorTotal,
                    'observacoes' => $item['observacoes'] ?? null
                ];

                $itemId = $this->orcamentoItemModel->insert($dadosItem);

                if (!$itemId) {
                    $db->transRollback();

                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('erros', $this->orcamentoItemModel->errors())
                        ->with('erro', 'Erro ao salvar um dos itens do orçamento.');
                }
            }

            $db->transCommit();

            return redirect()
                ->to('/orcamentos/ver/' . $orcamentoId)
                ->with('sucesso', 'Orçamento criado com sucesso.');

        } catch (\Throwable $e) {
            $db->transRollback();

            return redirect()
                ->back()
                ->withInput()
                ->with('erro', 'Erro técnico ao salvar orçamento: ' . $e->getMessage());
        }
    }

    public function ver($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('orcamentos', 'visualizar')) {
            return $redirect;
        }

        $orcamento = $this->orcamentoModel
            ->select('
                orcamentos.*, 
                clientes.nome AS cliente_nome, 
                clientes.cpf_cnpj, 
                clientes.telefone, 
                clientes.whatsapp, 
                clientes.email, 
                clientes.endereco, 
                clientes.numero AS cliente_numero, 
                clientes.bairro, 
                clientes.cidade, 
                clientes.estado
            ')
            ->join('clientes', 'clientes.id = orcamentos.cliente_id')
            ->where('orcamentos.id', $id)
            ->first();

        if (!$orcamento) {
            return redirect()
                ->to('/orcamentos')
                ->with('erro', 'Orçamento não encontrado.');
        }

        $itens = $this->orcamentoItemModel
            ->where('orcamento_id', $id)
            ->orderBy('id', 'ASC')
            ->findAll();

        return view('orcamentos/ver', [
            'title' => 'Orçamento ' . $orcamento['numero'] . ' | Lunna Gestor',
            'orcamento' => $orcamento,
            'itens' => $itens
        ]);
    }

    public function pdf($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('orcamentos', 'pdf')) {
            return $redirect;
        }

        $orcamento = $this->orcamentoModel
            ->select('
                orcamentos.*, 
                clientes.nome AS cliente_nome, 
                clientes.cpf_cnpj, 
                clientes.telefone, 
                clientes.whatsapp, 
                clientes.email, 
                clientes.endereco, 
                clientes.numero AS cliente_numero, 
                clientes.complemento,
                clientes.bairro, 
                clientes.cidade, 
                clientes.estado
            ')
            ->join('clientes', 'clientes.id = orcamentos.cliente_id')
            ->where('orcamentos.id', $id)
            ->first();

        if (!$orcamento) {
            return redirect()
                ->to('/orcamentos')
                ->with('erro', 'Orçamento não encontrado.');
        }

        $itens = $this->orcamentoItemModel
            ->where('orcamento_id', $id)
            ->orderBy('id', 'ASC')
            ->findAll();

        $html = view('orcamentos/pdf', [
            'orcamento' => $orcamento,
            'itens' => $itens
        ]);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $nomeArquivo = 'orcamento-' . $orcamento['numero'] . '.pdf';

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $nomeArquivo . '"')
            ->setBody($dompdf->output());
    }

    public function aprovar($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('orcamentos', 'aprovar')) {
            return $redirect;
        }

        $orcamento = $this->orcamentoModel->find($id);

        if (!$orcamento) {
            return redirect()
                ->to('/orcamentos')
                ->with('erro', 'Orçamento não encontrado.');
        }

        if (in_array($orcamento['status'], ['cancelado', 'recusado'])) {
            return redirect()
                ->to('/orcamentos/ver/' . $id)
                ->with('erro', 'Não é possível aprovar um orçamento cancelado ou recusado.');
        }

        $pedidoExistente = $this->pedidoModel
            ->where('orcamento_id', $id)
            ->where('ativo', 1)
            ->first();

        if ($pedidoExistente) {
            return redirect()
                ->to('/pedidos/ver/' . $pedidoExistente['id'])
                ->with('erro', 'Este orçamento já foi convertido em pedido.');
        }

        $db = \Config\Database::connect();

        try {
            $db->transBegin();

            $dadosPedido = [
                'orcamento_id' => $orcamento['id'],
                'cliente_id' => $orcamento['cliente_id'],
                'usuario_id' => session()->get('usuario_id'),
                'data_pedido' => date('Y-m-d'),
                'status' => 'aprovado',
                'subtotal' => $orcamento['subtotal'],
                'desconto' => $orcamento['desconto'],
                'total' => $orcamento['total'],
                'observacoes' => 'Pedido gerado a partir do orçamento ' . $orcamento['numero'] . '.',
                'ativo' => 1
            ];

            $pedidoId = $this->inserirPedidoComNumero($dadosPedido);

            if (!$pedidoId) {
                $db->transRollback();

                return redirect()
                    ->to('/orcamentos/ver/' . $id)
                    ->with('erros', $this->pedidoModel->errors())
                    ->with('erro', 'Erro ao gerar pedido a partir do orçamento.');
            }

            $orcamentoAtualizado = $this->orcamentoModel->update($id, [
                'status' => 'aprovado'
            ]);

            if (!$orcamentoAtualizado) {
                $db->transRollback();

                return redirect()
                    ->to('/orcamentos/ver/' . $id)
                    ->with('erros', $this->orcamentoModel->errors())
                    ->with('erro', 'Erro ao atualizar o status do orçamento.');
            }

            $historicoId = $this->pedidoStatusHistoricoModel->insert([
                'pedido_id' => $pedidoId,
                'status_anterior' => null,
                'status_novo' => 'aprovado',
                'observacao' => 'Pedido criado a partir da aprovação do orçamento.',
                'usuario_id' => session()->get('usuario_id'),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            if (!$historicoId) {
                $db->transRollback();

                return redirect()
                    ->to('/orcamentos/ver/' . $id)
                    ->with('erro', 'Erro ao registrar o histórico inicial do pedido.');
            }

            $db->transCommit();

            return redirect()
                ->to('/pedidos/ver/' . $pedidoId)
                ->with('sucesso', 'Orçamento aprovado e pedido criado com sucesso.');

        } catch (\Throwable $e) {
            $db->transRollback();

            return redirect()
                ->to('/orcamentos/ver/' . $id)
                ->with('erro', 'Erro técnico ao converter orçamento em pedido: ' . $e->getMessage());
        }
    }

    public function excluir($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('orcamentos', 'excluir')) {
            return $redirect;
        }

        $orcamento = $this->orcamentoModel->find($id);

        if (!$orcamento) {
            return redirect()
                ->to('/orcamentos')
                ->with('erro', 'Orçamento não encontrado.');
        }

        $this->orcamentoModel->update($id, ['ativo' => 0]);
        $this->orcamentoModel->delete($id);

        return redirect()
            ->to('/orcamentos')
            ->with('sucesso', 'Orçamento removido com sucesso.');
    }

    private function inserirOrcamentoComNumero(array $dadosOrcamento): ?int
    {
        for ($tentativa = 1; $tentativa <= 3; $tentativa++) {
            $dadosOrcamento['numero'] = $this->gerarNumeroOrcamento();
            $orcamentoId = $this->orcamentoModel->insert($dadosOrcamento);

            if ($orcamentoId) {
                return (int) $orcamentoId;
            }

            if (!$this->erroBancoDuplicidade()) {
                return null;
            }
        }

        return null;
    }

    private function inserirPedidoComNumero(array $dadosPedido): ?int
    {
        for ($tentativa = 1; $tentativa <= 3; $tentativa++) {
            $dadosPedido['numero'] = $this->gerarNumeroPedido();
            $pedidoId = $this->pedidoModel->insert($dadosPedido);

            if ($pedidoId) {
                return (int) $pedidoId;
            }

            if (!$this->erroBancoDuplicidade()) {
                return null;
            }
        }

        return null;
    }

    private function erroBancoDuplicidade(): bool
    {
        $erro = \Config\Database::connect()->error();

        return (int) ($erro['code'] ?? 0) === 1062;
    }

    private function gerarNumeroOrcamento(): string
    {
        $ano = date('Y');

        $ultimo = $this->orcamentoModel
            ->like('numero', "ORC-$ano-", 'after')
            ->orderBy('id', 'DESC')
            ->first();

        if (!$ultimo) {
            return "ORC-$ano-0001";
        }

        $partes = explode('-', $ultimo['numero']);
        $sequencial = (int) end($partes);
        $novoSequencial = $sequencial + 1;

        return "ORC-$ano-" . str_pad($novoSequencial, 4, '0', STR_PAD_LEFT);
    }

    private function gerarNumeroPedido(): string
    {
        $ano = date('Y');

        $ultimo = $this->pedidoModel
            ->like('numero', "PED-$ano-", 'after')
            ->orderBy('id', 'DESC')
            ->first();

        if (!$ultimo) {
            return "PED-$ano-0001";
        }

        $partes = explode('-', $ultimo['numero']);
        $sequencial = (int) end($partes);
        $novoSequencial = $sequencial + 1;

        return "PED-$ano-" . str_pad($novoSequencial, 4, '0', STR_PAD_LEFT);
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

    private function numeroParaDecimal($valor): float
    {
        if ($valor === null || $valor === '') {
            return 0;
        }

        $valor = str_replace(',', '.', $valor);

        return (float) $valor;
    }
}
