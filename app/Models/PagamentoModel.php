<?php

namespace App\Models;

use CodeIgniter\Model;

class PagamentoModel extends Model
{
    protected $table = 'pagamentos';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'pedido_id',
        'cliente_id',
        'usuario_id',
        'descricao',
        'tipo',
        'forma_pagamento',
        'valor',
        'data_vencimento',
        'data_pagamento',
        'status',
        'observacoes',
        'ativo'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'pedido_id' => 'required|integer',
        'cliente_id' => 'required|integer',
        'descricao' => 'required|min_length[3]|max_length[150]',
        'tipo' => 'required|in_list[entrada,parcela,saldo_final,pagamento_unico,outro]',
        'forma_pagamento' => 'required|in_list[pix,dinheiro,cartao_debito,cartao_credito,boleto,transferencia,cheque,outro]',
        'valor' => 'required|decimal',
        'data_vencimento' => 'required',
        'status' => 'required|in_list[pendente,pago,atrasado,cancelado]',
    ];

    protected $validationMessages = [
        'descricao' => [
            'required' => 'Informe a descrição do pagamento.',
            'min_length' => 'A descrição precisa ter pelo menos 3 caracteres.',
        ],
        'valor' => [
            'required' => 'Informe o valor do pagamento.',
            'decimal' => 'Informe um valor válido.',
        ],
        'data_vencimento' => [
            'required' => 'Informe a data de vencimento.',
        ],
    ];

    protected $skipValidation = false;
}