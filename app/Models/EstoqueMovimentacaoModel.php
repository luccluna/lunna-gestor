<?php

namespace App\Models;

use CodeIgniter\Model;

class EstoqueMovimentacaoModel extends Model
{
    protected $table = 'estoque_movimentacoes';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'material_id',
        'pedido_id',
        'usuario_id',
        'tipo',
        'origem',
        'documento',
        'nf_numero',
        'nf_chave_acesso',
        'fornecedor',
        'lote',
        'quantidade',
        'custo_unitario',
        'saldo_anterior',
        'saldo_posterior',
        'data_movimentacao',
        'observacoes',
        'created_at',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'material_id' => 'required|integer',
        'tipo' => 'required|in_list[entrada,saida]',
        'origem' => 'permit_empty|in_list[manual,nota_compra,pedido,ajuste,retalho]',
        'quantidade' => 'required|decimal|greater_than[0]',
        'custo_unitario' => 'permit_empty|decimal',
        'data_movimentacao' => 'required|valid_date',
    ];

    protected $validationMessages = [
        'tipo' => [
            'required' => 'Informe se a movimentacao e entrada ou saida.',
            'in_list' => 'Tipo de movimentacao invalido.',
        ],
        'quantidade' => [
            'required' => 'Informe a quantidade movimentada.',
            'greater_than' => 'A quantidade precisa ser maior que zero.',
        ],
        'data_movimentacao' => [
            'required' => 'Informe a data da movimentacao.',
        ],
    ];

    protected $skipValidation = false;
}
