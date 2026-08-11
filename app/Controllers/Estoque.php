<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EstoqueMaterialModel;
use App\Models\EstoqueMovimentacaoModel;
use App\Models\PedidoModel;
use App\Models\ProdutoServicoModel;

class Estoque extends BaseController
{
    protected $materialModel;
    protected $movimentacaoModel;
    protected $pedidoModel;
    protected $produtoModel;

    public function __construct()
    {
        $this->materialModel = new EstoqueMaterialModel();
        $this->movimentacaoModel = new EstoqueMovimentacaoModel();
        $this->pedidoModel = new PedidoModel();
        $this->produtoModel = new ProdutoServicoModel();
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

        if ($redirect = bloquearSemPermissao('estoque', 'visualizar')) {
            return $redirect;
        }

        $busca = $this->request->getGet('busca');
        $alerta = $this->request->getGet('alerta');

        $listaModel = new EstoqueMaterialModel();
        $builder = $listaModel
            ->select('estoque_materiais.*, produtos_servicos.nome AS produto_nome')
            ->join('produtos_servicos', 'produtos_servicos.id = estoque_materiais.produto_servico_id', 'left')
            ->where('estoque_materiais.ativo', 1)
            ->orderBy('estoque_materiais.nome', 'ASC');

        if (!empty($busca)) {
            $builder->groupStart()
                ->like('estoque_materiais.nome', $busca)
                ->orLike('estoque_materiais.fornecedor', $busca)
                ->orLike('estoque_materiais.localizacao', $busca)
                ->orLike('produtos_servicos.nome', $busca)
                ->groupEnd();
        }

        if ($alerta === 'baixo') {
            $builder->where('estoque_materiais.saldo_atual <= estoque_materiais.estoque_minimo', null, false);
        }

        $resumoModel = new EstoqueMaterialModel();
        $materiaisResumo = $resumoModel
            ->where('estoque_materiais.ativo', 1)
            ->findAll();

        $valorEstoque = 0;
        $materiaisBaixos = 0;

        foreach ($materiaisResumo as $materialResumo) {
            $valorEstoque += (float) $materialResumo['saldo_atual'] * (float) $materialResumo['custo_unitario'];

            if ((float) $materialResumo['saldo_atual'] <= (float) $materialResumo['estoque_minimo']) {
                $materiaisBaixos++;
            }
        }

        $historico = $this->movimentacaoModel
            ->select('estoque_movimentacoes.*, estoque_materiais.nome AS material_nome, pedidos.numero AS pedido_numero')
            ->join('estoque_materiais', 'estoque_materiais.id = estoque_movimentacoes.material_id')
            ->join('pedidos', 'pedidos.id = estoque_movimentacoes.pedido_id', 'left')
            ->orderBy('estoque_movimentacoes.id', 'DESC')
            ->limit(8)
            ->findAll();

        return view('estoque/index', [
            'title' => 'Estoque | Lunna Gestor',
            'materiais' => $builder->paginate(10),
            'pager' => $listaModel->pager,
            'busca' => $busca,
            'alerta' => $alerta,
            'totalMateriais' => count($materiaisResumo),
            'materiaisBaixos' => $materiaisBaixos,
            'valorEstoque' => $valorEstoque,
            'historico' => $historico,
        ]);
    }

    public function novo()
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('estoque', 'criar')) {
            return $redirect;
        }

        return view('estoque/form', [
            'title' => 'Novo material | Lunna Gestor',
            'material' => null,
            'produtos' => $this->produtosAtivos(),
        ]);
    }

    public function importarNota()
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('estoque', 'criar')) {
            return $redirect;
        }

        $resultado = $this->extrairDadosArquivoNota();

        if (!empty($resultado['erro'])) {
            return redirect()
                ->to('/estoque/novo')
                ->with('erro', $resultado['erro']);
        }

        return redirect()
            ->to('/estoque/novo')
            ->with('notaImportada', $resultado['dados'])
            ->with('sucesso', 'Nota importada. Confira os campos antes de cadastrar o material.');
    }

    public function salvar()
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('estoque', 'criar')) {
            return $redirect;
        }

        $dados = $this->tratarDadosMaterial($this->request->getPost());

        $db = \Config\Database::connect();
        $db->transBegin();

        $materialId = $this->materialModel->insert($dados);

        if (!$materialId) {
            $db->transRollback();

            return redirect()
                ->back()
                ->withInput()
                ->with('erros', $this->materialModel->errors());
        }

        $saldoInicial = (float) ($dados['saldo_atual'] ?? 0);

        if ($saldoInicial > 0) {
            $movimentacaoInicial = [
                'material_id' => $materialId,
                'pedido_id' => null,
                'usuario_id' => session()->get('usuario_id'),
                'tipo' => 'entrada',
                'origem' => $dados['origem'] ?? 'manual',
                'documento' => 'Saldo inicial',
                'nf_numero' => $dados['nf_numero'] ?? null,
                'nf_chave_acesso' => $dados['nf_chave_acesso'] ?? null,
                'fornecedor' => $dados['fornecedor'] ?? null,
                'lote' => $dados['lote'] ?? null,
                'quantidade' => $saldoInicial,
                'custo_unitario' => (float) ($dados['custo_unitario'] ?? 0),
                'saldo_anterior' => 0,
                'saldo_posterior' => $saldoInicial,
                'data_movimentacao' => date('Y-m-d'),
                'observacoes' => 'Saldo informado no cadastro do material.',
                'created_at' => date('Y-m-d H:i:s'),
            ];

            if (!$this->movimentacaoModel->insert($movimentacaoInicial)) {
                $db->transRollback();

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('erros', $this->movimentacaoModel->errors());
            }
        }

        $db->transCommit();

        return redirect()
            ->to('/estoque')
            ->with('sucesso', 'Material cadastrado no estoque.');
    }

    public function editar($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('estoque', 'editar')) {
            return $redirect;
        }

        $material = $this->materialModel->find($id);

        if (!$material) {
            return redirect()
                ->to('/estoque')
                ->with('erro', 'Material nao encontrado.');
        }

        return view('estoque/form', [
            'title' => 'Editar material | Lunna Gestor',
            'material' => $material,
            'produtos' => $this->produtosAtivos(),
        ]);
    }

    public function atualizar($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('estoque', 'editar')) {
            return $redirect;
        }

        $material = $this->materialModel->find($id);

        if (!$material) {
            return redirect()
                ->to('/estoque')
                ->with('erro', 'Material nao encontrado.');
        }

        $dados = $this->tratarDadosMaterial($this->request->getPost());
        unset($dados['saldo_atual']);

        if (!$this->materialModel->update($id, $dados)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('erros', $this->materialModel->errors());
        }

        return redirect()
            ->to('/estoque')
            ->with('sucesso', 'Material atualizado.');
    }

    public function movimentar($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('estoque', 'movimentar')) {
            return $redirect;
        }

        $material = $this->materialModel->find($id);

        if (!$material) {
            return redirect()
                ->to('/estoque')
                ->with('erro', 'Material nao encontrado.');
        }

        return view('estoque/movimentar', [
            'title' => 'Movimentar estoque | Lunna Gestor',
            'material' => $material,
            'pedidos' => $this->pedidosAtivos(),
            'pedidoSelecionado' => $this->request->getGet('pedido_id'),
        ]);
    }

    public function registrarMovimentacao($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('estoque', 'movimentar')) {
            return $redirect;
        }

        $material = $this->materialModel->find($id);

        if (!$material) {
            return redirect()
                ->to('/estoque')
                ->with('erro', 'Material nao encontrado.');
        }

        $tipo = $this->request->getPost('tipo');
        $origem = $this->request->getPost('origem') ?: ($tipo === 'saida' ? 'pedido' : 'manual');
        $quantidade = $this->normalizarDecimal($this->request->getPost('quantidade'));
        $custoUnitario = $this->normalizarDecimal($this->request->getPost('custo_unitario'));
        $pedidoId = $this->request->getPost('pedido_id') ?: null;
        $saldoAnterior = (float) $material['saldo_atual'];

        if ($tipo === 'saida' && $quantidade > $saldoAnterior) {
            return redirect()
                ->back()
                ->withInput()
                ->with('erro', 'A saida informada e maior que o saldo atual do material.');
        }

        $saldoPosterior = $tipo === 'entrada'
            ? $saldoAnterior + $quantidade
            : $saldoAnterior - $quantidade;

        $db = \Config\Database::connect();
        $db->transBegin();

        $dadosMovimentacao = [
            'material_id' => $id,
            'pedido_id' => $tipo === 'saida' ? $pedidoId : null,
            'usuario_id' => session()->get('usuario_id'),
            'tipo' => $tipo,
            'origem' => $origem,
            'documento' => $this->request->getPost('documento'),
            'nf_numero' => $this->request->getPost('nf_numero'),
            'nf_chave_acesso' => $this->limparChaveAcesso($this->request->getPost('nf_chave_acesso')),
            'fornecedor' => $this->request->getPost('fornecedor'),
            'lote' => $this->request->getPost('lote'),
            'quantidade' => $quantidade,
            'custo_unitario' => $custoUnitario,
            'saldo_anterior' => $saldoAnterior,
            'saldo_posterior' => $saldoPosterior,
            'data_movimentacao' => $this->request->getPost('data_movimentacao') ?: date('Y-m-d'),
            'observacoes' => $this->request->getPost('observacoes'),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if (!$this->movimentacaoModel->insert($dadosMovimentacao)) {
            $db->transRollback();

            return redirect()
                ->back()
                ->withInput()
                ->with('erros', $this->movimentacaoModel->errors());
        }

        $dadosMaterial = ['saldo_atual' => $saldoPosterior];

        if ($tipo === 'entrada' && $custoUnitario > 0) {
            $dadosMaterial['custo_unitario'] = $custoUnitario;
        }

        if ($tipo === 'entrada') {
            foreach (['fornecedor', 'lote', 'nf_numero', 'nf_chave_acesso'] as $campo) {
                $valor = $campo === 'nf_chave_acesso'
                    ? $this->limparChaveAcesso($this->request->getPost($campo))
                    : $this->request->getPost($campo);

                if (!empty($valor)) {
                    $dadosMaterial[$campo] = $valor;
                }
            }

            if ($origem === 'nota_compra') {
                $dadosMaterial['origem'] = 'nota_compra';
                $dadosMaterial['data_compra'] = $this->request->getPost('data_movimentacao') ?: date('Y-m-d');
            }
        }

        if (!$this->materialModel->update($id, $dadosMaterial)) {
            $db->transRollback();

            return redirect()
                ->back()
                ->withInput()
                ->with('erros', $this->materialModel->errors() ?: ['Nao foi possivel atualizar o saldo do material.']);
        }

        $db->transCommit();

        if ($db->transStatus() === false) {
            return redirect()
                ->back()
                ->withInput()
                ->with('erro', 'Nao foi possivel registrar a movimentacao.');
        }

        return redirect()
            ->to('/estoque')
            ->with('sucesso', 'Movimentacao de estoque registrada.');
    }

    public function excluir($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('estoque', 'excluir')) {
            return $redirect;
        }

        $material = $this->materialModel->find($id);

        if (!$material) {
            return redirect()
                ->to('/estoque')
                ->with('erro', 'Material nao encontrado.');
        }

        $this->materialModel->update($id, ['ativo' => 0]);
        $this->materialModel->delete($id);

        return redirect()
            ->to('/estoque')
            ->with('sucesso', 'Material removido do estoque.');
    }

    private function produtosAtivos(): array
    {
        return $this->produtoModel
            ->where('produtos_servicos.ativo', 1)
            ->where('produtos_servicos.tipo', 'produto')
            ->orderBy('produtos_servicos.nome', 'ASC')
            ->findAll();
    }

    private function pedidosAtivos(): array
    {
        return $this->pedidoModel
            ->select('pedidos.id, pedidos.numero, clientes.nome AS cliente_nome')
            ->join('clientes', 'clientes.id = pedidos.cliente_id')
            ->where('pedidos.ativo', 1)
            ->orderBy('pedidos.id', 'DESC')
            ->findAll(100);
    }

    private function tratarDadosMaterial(array $dados): array
    {
        foreach (['saldo_atual', 'estoque_minimo', 'custo_unitario', 'largura', 'altura', 'comprimento'] as $campo) {
            if (isset($dados[$campo])) {
                $dados[$campo] = $this->normalizarDecimal($dados[$campo]);
            }
        }

        $dados['tipo_controle'] = ($dados['tipo_controle'] ?? '') ?: 'unidade';
        $dados['origem'] = ($dados['origem'] ?? '') ?: 'manual';
        $dados['nf_chave_acesso'] = $this->limparChaveAcesso($dados['nf_chave_acesso'] ?? null);

        if (($dados['produto_servico_id'] ?? '') === '') {
            $dados['produto_servico_id'] = null;
        }

        return $dados;
    }

    private function normalizarDecimal($valor): float
    {
        $valor = (string) $valor;
        $valor = str_replace('.', '', $valor);
        $valor = str_replace(',', '.', $valor);

        if ($valor === '') {
            return 0;
        }

        return (float) $valor;
    }

    private function limparChaveAcesso($valor): ?string
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        $valor = preg_replace('/\D+/', '', (string) $valor);

        return $valor !== '' ? $valor : null;
    }

    private function temDadosNotaExtraidos(array $dados): bool
    {
        foreach (['nf_chave_acesso', 'nf_numero', 'fornecedor', 'data_compra', 'nome'] as $campo) {
            if (!empty($dados[$campo])) {
                return true;
            }
        }

        return false;
    }

    private function extrairDadosArquivoNota(): array
    {
        $arquivo = $this->request->getFile('arquivo_nota');

        if (!$arquivo || $arquivo->getError() === UPLOAD_ERR_NO_FILE) {
            return ['erro' => 'Selecione um arquivo .xml, .html ou .htm da nota fiscal.'];
        }

        if (!$arquivo->isValid()) {
            return ['erro' => 'Nao foi possivel ler o arquivo enviado.'];
        }

        if ($arquivo->getSize() > 2 * 1024 * 1024) {
            return ['erro' => 'O arquivo da nota deve ter no maximo 2 MB.'];
        }

        $extensao = strtolower($arquivo->getClientExtension());

        if (!in_array($extensao, ['xml', 'html', 'htm'], true)) {
            return ['erro' => 'Formato invalido. Envie uma nota em .xml, .html ou .htm.'];
        }

        $conteudo = file_get_contents($arquivo->getTempName());

        if ($conteudo === false || trim($conteudo) === '') {
            return ['erro' => 'O arquivo enviado esta vazio ou nao pode ser lido.'];
        }

        $dados = $extensao === 'xml'
            ? $this->extrairDadosNotaXml($conteudo)
            : $this->extrairDadosNotaHtml($conteudo);

        if (!$this->temDadosNotaExtraidos($dados)) {
            return ['erro' => 'Nao encontrei dados de nota fiscal nesse arquivo. Confira se ele foi baixado corretamente.'];
        }

        $dados['origem'] = 'nota_compra';

        return ['dados' => $dados];
    }

    private function extrairDadosNotaXml(string $conteudo): array
    {
        $dados = [];
        $anterior = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($conteudo);
        libxml_clear_errors();
        libxml_use_internal_errors($anterior);

        if (!$xml) {
            return $dados;
        }

        $dados['nf_numero'] = $this->valorXml($xml, '//*[local-name()="nNF"]');
        $dados['fornecedor'] = $this->valorXml($xml, '//*[local-name()="emit"]/*[local-name()="xNome"]');

        $dataEmissao = $this->valorXml($xml, '//*[local-name()="dhEmi"]')
            ?: $this->valorXml($xml, '//*[local-name()="dEmi"]');

        if (!empty($dataEmissao)) {
            $dados['data_compra'] = substr($dataEmissao, 0, 10);
        }

        $infNfe = $xml->xpath('//*[local-name()="infNFe"]');

        if (!empty($infNfe[0]['Id'])) {
            $dados['nf_chave_acesso'] = $this->limparChaveAcesso((string) $infNfe[0]['Id']);
        }

        $itens = $this->itensNotaXml($xml);

        if (!empty($itens)) {
            $primeiroItem = $itens[0];
            $dados['nome'] = $primeiroItem['nome'] ?? null;
            $dados['unidade_medida'] = strtolower($primeiroItem['unidade'] ?? 'unidade');
            $dados['saldo_atual'] = $this->formatarDecimalTela((float) ($primeiroItem['quantidade'] ?? 0), 3);
            $dados['custo_unitario'] = $this->formatarDecimalTela((float) ($primeiroItem['valor_unitario'] ?? 0), 2);
            $dados['descricao'] = $this->resumoItensNota($itens);
        }

        $dados['observacoes'] = 'Dados importados de arquivo XML de NF. Confira produto, unidade, quantidade e custo antes de salvar.';

        return $dados;
    }

    private function extrairDadosNotaHtml(string $conteudo): array
    {
        $texto = html_entity_decode(strip_tags($conteudo), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $texto = preg_replace('/\s+/', ' ', $texto) ?? '';
        $dados = [];

        if (preg_match('/((?:\d[\s.\-]*){44})/', $texto, $chave)) {
            $dados['nf_chave_acesso'] = $this->limparChaveAcesso($chave[1]);
        }

        if (preg_match('/(?:numero|n[úu]mero|nro\.?|n[ºo]|nota fiscal)\D{0,50}(\d{1,12})/iu', $texto, $numero)) {
            $dados['nf_numero'] = $numero[1];
        }

        if (preg_match('/(?:emitente|fornecedor|razao social|razão social|nome\/razao social)\D{0,80}([A-Z0-9][^:]{3,120})/iu', $texto, $fornecedor)) {
            $dados['fornecedor'] = trim($fornecedor[1]);
        }

        if (preg_match('/(?:emissao|emissão|data de emissao|data de emissão)\D{0,40}(\d{2}\/\d{2}\/\d{4})/iu', $texto, $data)) {
            $partes = explode('/', $data[1]);
            $dados['data_compra'] = "{$partes[2]}-{$partes[1]}-{$partes[0]}";
        }

        $dados['observacoes'] = 'Dados importados de arquivo HTML de NF. Confira os dados e cadastre manualmente os itens que nao forem identificados.';

        return $dados;
    }

    private function valorXml(\SimpleXMLElement $xml, string $xpath): ?string
    {
        $resultado = $xml->xpath($xpath);

        if (empty($resultado)) {
            return null;
        }

        $valor = trim((string) $resultado[0]);

        return $valor !== '' ? $valor : null;
    }

    private function itensNotaXml(\SimpleXMLElement $xml): array
    {
        $detalhes = $xml->xpath('//*[local-name()="det"]') ?: [];
        $itens = [];

        foreach ($detalhes as $detalhe) {
            $produto = $detalhe->xpath('.//*[local-name()="prod"]');

            if (empty($produto[0])) {
                continue;
            }

            $produto = $produto[0];

            $itens[] = [
                'nome' => $this->valorXml($produto, './/*[local-name()="xProd"]'),
                'unidade' => $this->valorXml($produto, './/*[local-name()="uCom"]'),
                'quantidade' => (float) ($this->valorXml($produto, './/*[local-name()="qCom"]') ?? 0),
                'valor_unitario' => (float) ($this->valorXml($produto, './/*[local-name()="vUnCom"]') ?? 0),
                'valor_total' => (float) ($this->valorXml($produto, './/*[local-name()="vProd"]') ?? 0),
            ];
        }

        return $itens;
    }

    private function resumoItensNota(array $itens): string
    {
        $linhas = ['Itens encontrados na NF:'];

        foreach (array_slice($itens, 0, 20) as $item) {
            $quantidade = $this->formatarDecimalTela((float) ($item['quantidade'] ?? 0), 3);
            $valorUnitario = $this->formatarDecimalTela((float) ($item['valor_unitario'] ?? 0), 2);

            $linhas[] = sprintf(
                '- %s | Qtde: %s %s | Unitario: R$ %s',
                $item['nome'] ?: 'Produto sem nome',
                $quantidade,
                $item['unidade'] ?: '',
                $valorUnitario
            );
        }

        if (count($itens) > 20) {
            $linhas[] = '- Existem mais itens na NF. Confira o arquivo original.';
        }

        return implode(PHP_EOL, $linhas);
    }

    private function formatarDecimalTela(float $valor, int $casas): string
    {
        return number_format($valor, $casas, ',', '.');
    }
}
