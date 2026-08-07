<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;

class Usuarios extends BaseController
{
    protected $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    private function verificarLogin()
    {
        if (!session()->get('logado')) {
            return redirect()->to('/login');
        }

        return null;
    }

    private function verificarAdministrador()
    {
        if ($redirect = bloquearSemPermissao('usuarios')) {
            return $redirect;
        }

        return null;
    }

    public function index()
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = $this->verificarAdministrador()) {
            return $redirect;
        }

        $busca = $this->request->getGet('busca');
        $perfil = $this->request->getGet('perfil');

        $builder = $this->usuarioModel
            ->where('ativo', 1)
            ->orderBy('nome', 'ASC');

        if (!empty($busca)) {
            $builder->groupStart()
                ->like('nome', $busca)
                ->orLike('email', $busca)
                ->groupEnd();
        }

        if (!empty($perfil)) {
            $builder->where('perfil', $perfil);
        }

        return view('usuarios/index', [
            'title' => 'Usuários | Lunna Gestor',
            'usuarios' => $builder->paginate(10),
            'pager' => $this->usuarioModel->pager,
            'busca' => $busca,
            'perfil' => $perfil
        ]);
    }

    public function novo()
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = $this->verificarAdministrador()) {
            return $redirect;
        }

        return view('usuarios/form', [
            'title' => 'Novo Usuário | Lunna Gestor',
            'usuario' => null
        ]);
    }

    public function salvar()
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = $this->verificarAdministrador()) {
            return $redirect;
        }

        $dados = $this->request->getPost();

        if (empty($dados['senha'])) {
            return redirect()
                ->back()
                ->withInput()
                ->with('erro', 'Informe uma senha para o usuário.');
        }

        if ($dados['senha'] !== ($dados['confirmar_senha'] ?? '')) {
            return redirect()
                ->back()
                ->withInput()
                ->with('erro', 'A confirmação de senha não confere.');
        }

        $emailExiste = $this->usuarioModel
            ->withDeleted()
            ->where('email', $dados['email'])
            ->first();

        if ($emailExiste) {
            if (!empty($emailExiste['deleted_at'])) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('erro', 'Este e-mail já pertence a um usuário removido. Reative o usuário antigo ou use outro e-mail.');
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('erro', 'Já existe um usuário ativo com este e-mail.');
        }

        $dadosUsuario = [
            'nome' => $dados['nome'],
            'email' => $dados['email'],
            'senha' => password_hash($dados['senha'], PASSWORD_DEFAULT),
            'perfil' => $dados['perfil'],
            'ativo' => 1
        ];

        if (!$this->usuarioModel->insert($dadosUsuario)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('erros', $this->usuarioModel->errors());
        }

        return redirect()
            ->to('/usuarios')
            ->with('sucesso', 'Usuário cadastrado com sucesso.');
    }

    public function editar($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = $this->verificarAdministrador()) {
            return $redirect;
        }

        $usuario = $this->usuarioModel->find($id);

        if (!$usuario) {
            return redirect()
                ->to('/usuarios')
                ->with('erro', 'Usuário não encontrado.');
        }

        return view('usuarios/form', [
            'title' => 'Editar Usuário | Lunna Gestor',
            'usuario' => $usuario
        ]);
    }

    public function atualizar($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = $this->verificarAdministrador()) {
            return $redirect;
        }

        $usuario = $this->usuarioModel->find($id);

        if (!$usuario) {
            return redirect()
                ->to('/usuarios')
                ->with('erro', 'Usuário não encontrado.');
        }

        $dados = $this->request->getPost();

        $emailExiste = $this->usuarioModel
        ->withDeleted()
        ->where('email', $dados['email'])
        ->where('id !=', $id)
        ->first();

        if ($emailExiste) {
            return redirect()
                ->back()
                ->withInput()
                ->with('erro', 'Já existe outro usuário com este e-mail.');
        }

        $dadosUsuario = [
            'nome' => $dados['nome'],
            'email' => $dados['email'],
            'perfil' => $dados['perfil'],
            'ativo' => isset($dados['ativo']) ? 1 : 0
        ];

        if (!empty($dados['senha'])) {
            if ($dados['senha'] !== ($dados['confirmar_senha'] ?? '')) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('erro', 'A confirmação de senha não confere.');
            }

            $dadosUsuario['senha'] = password_hash($dados['senha'], PASSWORD_DEFAULT);
        }

        if (!$this->usuarioModel->update($id, $dadosUsuario)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('erros', $this->usuarioModel->errors());
        }

        return redirect()
            ->to('/usuarios')
            ->with('sucesso', 'Usuário atualizado com sucesso.');
    }

    public function excluir($id)
    {
        if ($redirect = $this->verificarLogin()) {
            return $redirect;
        }

        if ($redirect = $this->verificarAdministrador()) {
            return $redirect;
        }

        if ((int) session()->get('usuario_id') === (int) $id) {
            return redirect()
                ->to('/usuarios')
                ->with('erro', 'Você não pode excluir o próprio usuário logado.');
        }

        $usuario = $this->usuarioModel->find($id);

        if (!$usuario) {
            return redirect()
                ->to('/usuarios')
                ->with('erro', 'Usuário não encontrado.');
        }

        $this->usuarioModel->update($id, ['ativo' => 0]);
        $this->usuarioModel->delete($id);

        return redirect()
            ->to('/usuarios')
            ->with('sucesso', 'Usuário removido com sucesso.');
    }
}