<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProdutoServicoModel;
use App\Models\CategoriaServicoModel;

class Produtos extends BaseController
{
    protected $produtoModel;
    protected $categoriaModel;

    public function __construct()
    {
        $this->produtoModel = new ProdutoServicoModel();
        $this->categoriaModel = new CategoriaServicoModel();
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
        if ($redirect = bloquearSemPermissao('produtos', 'visualizar')) {
            return $redirect;
        }

        $busca = $this->request->getGet('busca');

        $builder = $this->produtoModel
            ->select('produtos_servicos.*, categorias_servicos.nome AS categoria_nome')
            ->join('categorias_servicos', 'categorias_servicos.id = produtos_servicos.categoria_id', 'left')
            ->where('produtos_servicos.ativo', 1)
            ->orderBy('produtos_servicos.nome', 'ASC');

        if (!empty($busca)) {
            $builder->groupStart()
                ->like('produtos_servicos.nome', $busca)
                ->orLike('categorias_servicos.nome', $busca)
                ->orLike('produtos_servicos.tipo', $busca)
                ->groupEnd();
        }

        $data = [
            'title' => 'Produtos e Serviços | Lunna Gestor',
            'produtos' => $builder->paginate(10),
            'pager' => $this->produtoModel->pager,
            'busca' => $busca
        ];

        return view('produtos/index', $data);
    }

    public function categorias()
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }
        if ($redirect = bloquearSemPermissao('produtos', 'visualizar')) {
            return $redirect;
        }

        $busca = $this->request->getGet('busca');

        $builder = $this->categoriaModel
            ->where('ativo', 1)
            ->orderBy('nome', 'ASC');

        if (!empty($busca)) {
            $builder->groupStart()
                ->like('nome', $busca)
                ->orLike('descricao', $busca)
                ->groupEnd();
        }

        $categorias = $builder->paginate(10);

        foreach ($categorias as &$categoria) {
            $categoria['produtos_vinculados'] = $this->produtoModel
                ->where('categoria_id', $categoria['id'])
                ->where('ativo', 1)
                ->countAllResults();
        }
        unset($categoria);

        return view('produtos/categorias_index', [
            'title' => 'Categorias | Lunna Gestor',
            'categorias' => $categorias,
            'pager' => $this->categoriaModel->pager,
            'busca' => $busca
        ]);
    }

    public function categoriaNova()
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }
        if ($redirect = bloquearSemPermissao('produtos', 'criar')) {
            return $redirect;
        }

        return view('produtos/categorias_form', [
            'title' => 'Nova Categoria | Lunna Gestor',
            'categoria' => null
        ]);
    }

    public function categoriaSalvar()
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }
        if ($redirect = bloquearSemPermissao('produtos', 'criar')) {
            return $redirect;
        }

        $dados = $this->tratarDadosCategoria($this->request->getPost());

        if ($this->categoriaDuplicada($dados['nome'] ?? null)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('erros', ['Já existe uma categoria ativa com este nome.']);
        }

        if (!$this->categoriaModel->insert($dados)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('erros', $this->categoriaModel->errors());
        }

        return redirect()
            ->to('/produtos/categorias')
            ->with('sucesso', 'Categoria cadastrada com sucesso.');
    }

    public function categoriaEditar($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }
        if ($redirect = bloquearSemPermissao('produtos', 'editar')) {
            return $redirect;
        }

        $categoria = $this->categoriaModel->find($id);

        if (!$categoria) {
            return redirect()
                ->to('/produtos/categorias')
                ->with('erro', 'Categoria não encontrada.');
        }

        return view('produtos/categorias_form', [
            'title' => 'Editar Categoria | Lunna Gestor',
            'categoria' => $categoria
        ]);
    }

    public function categoriaAtualizar($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }
        if ($redirect = bloquearSemPermissao('produtos', 'editar')) {
            return $redirect;
        }

        $categoria = $this->categoriaModel->find($id);

        if (!$categoria) {
            return redirect()
                ->to('/produtos/categorias')
                ->with('erro', 'Categoria não encontrada.');
        }

        $dados = $this->tratarDadosCategoria($this->request->getPost());

        if ($this->categoriaDuplicada($dados['nome'] ?? null, (int) $id)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('erros', ['Já existe uma categoria ativa com este nome.']);
        }

        if (!$this->categoriaModel->update($id, $dados)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('erros', $this->categoriaModel->errors());
        }

        return redirect()
            ->to('/produtos/categorias')
            ->with('sucesso', 'Categoria atualizada com sucesso.');
    }

    public function categoriaExcluir($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }
        if ($redirect = bloquearSemPermissao('produtos', 'excluir')) {
            return $redirect;
        }

        $categoria = $this->categoriaModel->find($id);

        if (!$categoria) {
            return redirect()
                ->to('/produtos/categorias')
                ->with('erro', 'Categoria não encontrada.');
        }

        $produtosVinculados = $this->produtoModel
            ->where('categoria_id', $id)
            ->where('ativo', 1)
            ->countAllResults();

        if ($produtosVinculados > 0) {
            return redirect()
                ->to('/produtos/categorias')
                ->with('erro', 'Esta categoria possui produtos ou serviços vinculados. Altere esses itens antes de excluir.');
        }

        $this->categoriaModel->update($id, ['ativo' => 0]);
        $this->categoriaModel->delete($id);

        return redirect()
            ->to('/produtos/categorias')
            ->with('sucesso', 'Categoria removida com sucesso.');
    }

    public function novo()
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }
        if ($redirect = bloquearSemPermissao('produtos', 'criar')) {
            return $redirect;
        }

        $categorias = $this->categoriaModel
            ->where('ativo', 1)
            ->orderBy('nome', 'ASC')
            ->findAll();

        return view('produtos/form', [
            'title' => 'Novo Produto/Serviço | Lunna Gestor',
            'produto' => null,
            'categorias' => $categorias
        ]);
    }

    public function salvar()
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }
    
        if ($redirect = bloquearSemPermissao('produtos', 'criar')) {
            return $redirect;
        }

        $dados = $this->tratarDadosNumericos($this->request->getPost());

        if (!$this->produtoModel->insert($dados)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('erros', $this->produtoModel->errors());
        }

        return redirect()
            ->to('/produtos')
            ->with('sucesso', 'Produto/serviço cadastrado com sucesso.');
    }

    public function editar($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('produtos', 'editar')) {
            return $redirect;
        }

        $produto = $this->produtoModel->find($id);

        if (!$produto) {
            return redirect()
                ->to('/produtos')
                ->with('erro', 'Produto/serviço não encontrado.');
        }

        $categorias = $this->categoriaModel
            ->where('ativo', 1)
            ->orderBy('nome', 'ASC')
            ->findAll();

        return view('produtos/form', [
            'title' => 'Editar Produto/Serviço | Lunna Gestor',
            'produto' => $produto,
            'categorias' => $categorias
        ]);
    }

    public function atualizar($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('produtos', 'editar')) {
            return $redirect;
        }

        $produto = $this->produtoModel->find($id);

        if (!$produto) {
            return redirect()
                ->to('/produtos')
                ->with('erro', 'Produto/serviço não encontrado.');
        }

        $dados = $this->tratarDadosNumericos($this->request->getPost());

        if (!$this->produtoModel->update($id, $dados)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('erros', $this->produtoModel->errors());
        }

        return redirect()
            ->to('/produtos')
            ->with('sucesso', 'Produto/serviço atualizado com sucesso.');
    }

    public function excluir($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('produtos', 'excluir')) {
            return $redirect;
        }

        $produto = $this->produtoModel->find($id);

        if (!$produto) {
            return redirect()
                ->to('/produtos')
                ->with('erro', 'Produto/serviço não encontrado.');
        }

        $this->produtoModel->update($id, ['ativo' => 0]);
        $this->produtoModel->delete($id);

        return redirect()
            ->to('/produtos')
            ->with('sucesso', 'Produto/serviço removido com sucesso.');
    }

    private function tratarDadosNumericos(array $dados): array
    {
        foreach (['valor_base', 'custo_base', 'margem_lucro'] as $campo) {
            if (isset($dados[$campo])) {
                $dados[$campo] = str_replace('.', '', $dados[$campo]);
                $dados[$campo] = str_replace(',', '.', $dados[$campo]);

                if ($dados[$campo] === '') {
                    $dados[$campo] = 0;
                }
            }
        }

        if (isset($dados['categoria_id']) && $dados['categoria_id'] === '') {
            $dados['categoria_id'] = null;
        }

        return $dados;
    }

    private function tratarDadosCategoria(array $dados): array
    {
        return [
            'nome' => trim((string) ($dados['nome'] ?? '')),
            'descricao' => trim((string) ($dados['descricao'] ?? '')) ?: null,
            'ativo' => 1,
        ];
    }

    private function categoriaDuplicada(?string $nome, ?int $ignorarId = null): bool
    {
        $nome = trim((string) $nome);

        if ($nome === '') {
            return false;
        }

        $builder = $this->categoriaModel
            ->where('ativo', 1)
            ->where('nome', $nome);

        if ($ignorarId) {
            $builder->where('id !=', $ignorarId);
        }

        return $builder->countAllResults() > 0;
    }
}
