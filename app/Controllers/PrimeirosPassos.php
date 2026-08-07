<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class PrimeirosPassos extends BaseController
{
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

        if ($redirect = bloquearSemPermissao('usuarios')) {
            return $redirect;
        }

        return view('primeiros_passos/index', [
            'title' => 'Primeiros passos | Lunna Gestor',
        ]);
    }
}
