<?php

namespace App\Models;

use CodeIgniter\Model;

class OrcamentoModel extends Model
{
    protected $table = 'orcamentos';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'numero',
        'cliente_id',
        'usuario_id',
        'data_orcamento',
        'validade',
        'prazo_entrega',
        'status',
        'subtotal',
        'desconto',
        'total',
        'forma_pagamento',
        'observacoes_cliente',
        'observacoes_internas',
        'ativo'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'cliente_id' => 'required|integer',
        'data_orcamento' => 'required|valid_date',
        'status' => 'required',
    ];

    protected $validationMessages = [
        'cliente_id' => [
            'required' => 'Selecione um cliente para o orçamento.',
        ],
        'data_orcamento' => [
            'required' => 'Informe a data do orçamento.',
        ],
    ];

    protected $skipValidation = false;
}