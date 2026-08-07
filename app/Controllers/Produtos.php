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
}
