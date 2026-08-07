<?php

if (!function_exists('perfilUsuario')) {
    function perfilUsuario(): ?string
    {
        return session()->get('usuario_perfil');
    }
}

if (!function_exists('temPermissao')) {
    function temPermissao(string $modulo): bool
    {
        return temAcao($modulo, 'visualizar');
    }
}

if (!function_exists('temAcao')) {
    function temAcao(string $modulo, string $acao = 'visualizar'): bool
    {
        $perfil = perfilUsuario();

        if (!$perfil) {
            return false;
        }

        if ($perfil === 'administrador') {
            return true;
        }

        $permissoes = [
            'gerente' => [
                'dashboard' => ['visualizar'],

                'clientes' => ['visualizar', 'criar', 'editar'],
                'produtos' => ['visualizar', 'criar', 'editar'],
                'estoque' => ['visualizar', 'criar', 'editar', 'movimentar'],
                'orcamentos' => ['visualizar', 'criar', 'editar', 'aprovar', 'pdf'],
                'pedidos' => ['visualizar', 'editar', 'alterar_status'],
                'agenda' => ['visualizar', 'criar', 'editar', 'concluir'],
                'pagamentos' => ['visualizar', 'criar', 'editar', 'marcar_pago'],
            ],

            'vendedor' => [
                'dashboard' => ['visualizar'],

                'clientes' => ['visualizar', 'criar', 'editar'],
                'orcamentos' => ['visualizar', 'criar', 'editar', 'pdf'],
                'pedidos' => ['visualizar'],
            ],

            'financeiro' => [
                'dashboard' => ['visualizar'],

                'pedidos' => ['visualizar'],
                'pagamentos' => ['visualizar', 'criar', 'editar', 'marcar_pago'],
            ],

            'medidor' => [
                'dashboard' => ['visualizar'],

                'clientes' => ['visualizar'],
                'pedidos' => ['visualizar'],
                'agenda' => ['visualizar', 'criar', 'editar', 'concluir'],
            ],

            'instalador' => [
                'dashboard' => ['visualizar'],

                'pedidos' => ['visualizar'],
                'agenda' => ['visualizar', 'concluir'],
            ],
        ];

        return in_array($acao, $permissoes[$perfil][$modulo] ?? [], true);
    }
}

if (!function_exists('bloquearSemPermissao')) {
    function bloquearSemPermissao(string $modulo, string $acao = 'visualizar')
    {
        if (!temAcao($modulo, $acao)) {
            return redirect()
                ->to('/dashboard')
                ->with('erro', 'Você não tem permissão para executar esta ação.');
        }

        return null;
    }
}
