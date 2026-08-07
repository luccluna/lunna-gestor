<?php

namespace App\Services;

class PagamentoAtrasadoService
{
    public function atualizar(?string $dataReferencia = null): int
    {
        $dataReferencia = $dataReferencia ?: date('Y-m-d');
        $db = \Config\Database::connect();

        $db->table('pagamentos')
            ->where('ativo', 1)
            ->where('status', 'pendente')
            ->where('data_vencimento <', $dataReferencia)
            ->update([
                'status' => 'atrasado',
            ]);

        return $db->affectedRows();
    }
}
