<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;

class Auth extends BaseController
{
    protected $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    public function login()
    {
        if (session()->get('logado')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function autenticar()
    {
        $email = trim((string) $this->request->getPost('email'));
        $senha = (string) $this->request->getPost('senha');

        if (empty($email) || empty($senha)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('erro', 'Informe e-mail e senha.');
        }

        $usuario = $this->usuarioModel
            ->where('email', $email)
            ->where('ativo', 1)
            ->first();

        if (!$usuario || !password_verify($senha, $usuario['senha'])) {
            return redirect()
                ->back()
                ->withInput()
                ->with('erro', 'E-mail ou senha inválidos.');
        }

        $this->usuarioModel->update($usuario['id'], [
            'ultimo_acesso' => date('Y-m-d H:i:s')
        ]);

        session()->set([
            'usuario_id' => $usuario['id'],
            'usuario_nome' => $usuario['nome'],
            'usuario_email' => $usuario['email'],
            'usuario_perfil' => $usuario['perfil'],
            'logado' => true
        ]);

        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()
            ->to('/login')
            ->with('sucesso', 'Você saiu do sistema.');
    }
}