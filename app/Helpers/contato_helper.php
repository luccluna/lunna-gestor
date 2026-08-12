<?php

if (!function_exists('apenasDigitos')) {
    function apenasDigitos(?string $valor): string
    {
        return preg_replace('/\D+/', '', (string) $valor) ?? '';
    }
}

if (!function_exists('formatarCpfCnpj')) {
    function formatarCpfCnpj(?string $valor): string
    {
        $digitos = apenasDigitos($valor);

        if (strlen($digitos) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $digitos);
        }

        if (strlen($digitos) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $digitos);
        }

        return trim((string) $valor);
    }
}

if (!function_exists('formatarTelefone')) {
    function formatarTelefone(?string $valor): string
    {
        $digitos = apenasDigitos($valor);

        if (strlen($digitos) === 13 && str_starts_with($digitos, '55')) {
            $digitos = substr($digitos, 2);
        }

        if (strlen($digitos) === 12 && str_starts_with($digitos, '55')) {
            $digitos = substr($digitos, 2);
        }

        if (strlen($digitos) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $digitos);
        }

        if (strlen($digitos) === 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $digitos);
        }

        return trim((string) $valor);
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
