<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ClienteModel;

class Clientes extends BaseController
{
    protected $clienteModel;

    public function __construct()
    {
        $this->clienteModel = new ClienteModel();
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

        if ($redirect = bloquearSemPermissao('clientes', 'visualizar')) {
            return $redirect;
        }

        $busca = $this->request->getGet('busca');

        $query = $this->clienteModel
            ->where('ativo', 1)
            ->orderBy('nome', 'ASC');

        if (!empty($busca)) {
            $query->groupStart()
                ->like('nome', $busca)
                ->orLike('telefone', $busca)
                ->orLike('whatsapp', $busca)
                ->orLike('cidade', $busca)
                ->orLike('cpf_cnpj', $busca)
                ->groupEnd();
        }

        $data = [
            'title' => 'Clientes | Lunna Gestor',
            'clientes' => $query->paginate(10),
            'pager' => $this->clienteModel->pager,
            'busca' => $busca
        ];

        return view('clientes/index', $data);
    }

    public function novo()
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = bloquearSemPermissao('clientes', 'criar')) {
            return $redirect;
        }

        return view('clientes/form', [
            'title' => 'Novo Cliente | Lunna Gestor',
            'cliente' => null
        ]);
    }

    public function salvar()
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }
        if ($redirect = bloquearSemPermissao('clientes', 'criar')) {
            return $redirect;
        }

        $dados = $this->request->getPost();

        if (!$this->clienteModel->insert($dados)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('erros', $this->clienteModel->errors());
        }

        return redirect()
            ->to('/clientes')
            ->with('sucesso', 'Cliente cadastrado com sucesso.');
    }

    public function editar($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }
        if ($redirect = bloquearSemPermissao('clientes', 'editar')) {
            return $redirect;
        }

        $cliente = $this->clienteModel->find($id);

        if (!$cliente) {
            return redirect()
                ->to('/clientes')
                ->with('erro', 'Cliente não encontrado.');
        }

        return view('clientes/form', [
            'title' => 'Editar Cliente | Lunna Gestor',
            'cliente' => $cliente
        ]);
    }

    public function atualizar($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }
        if ($redirect = bloquearSemPermissao('clientes', 'editar')) {
            return $redirect;
        }

        $cliente = $this->clienteModel->find($id);

        if (!$cliente) {
            return redirect()
                ->to('/clientes')
                ->with('erro', 'Cliente não encontrado.');
        }

        $dados = $this->request->getPost();

        if (!$this->clienteModel->update($id, $dados)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('erros', $this->clienteModel->errors());
        }

        return redirect()
            ->to('/clientes')
            ->with('sucesso', 'Cliente atualizado com sucesso.');
    }

    public function excluir($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }
        
        if ($redirect = bloquearSemPermissao('clientes', 'excluir')) {
            return $redirect;
        }

        $cliente = $this->clienteModel->find($id);

        if (!$cliente) {
            return redirect()
                ->to('/clientes')
                ->with('erro', 'Cliente não encontrado.');
        }

        $this->clienteModel->update($id, ['ativo' => 0]);
        $this->clienteModel->delete($id);

        return redirect()
            ->to('/clientes')
            ->with('sucesso', 'Cliente removido com sucesso.');
    }
}
