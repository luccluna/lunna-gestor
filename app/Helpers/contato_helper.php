<?php

if (!function_exists('apenasDigitos')) {
    function apenasDigitos(?string $valor): string
    {
        return preg_replace('/\D+/', '', (string) $valor) ?? '';
    }
}

if (!function_exists('whatsappUrl')) {
    function whatsappUrl(?string $telefone, string $mensagem = ''): ?string
    {
        $numero = apenasDigitos($telefone);

        if ($numero === '') {
            return null;
        }

        if (strlen($numero) === 10 || strlen($numero) === 11) {
            $numero = '55' . $numero;
        }

        $url = 'https://wa.me/' . $numero;

        if ($mensagem !== '') {
            $url .= '?text=' . rawurlencode($mensagem);
        }

        return $url;
    }
}
