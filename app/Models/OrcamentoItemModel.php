<?php

namespace App\Models;

use CodeIgniter\Model;

class OrcamentoItemModel extends Model
{
    protected $table = 'orcamento_itens';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'orcamento_id',
        'produto_servico_id',
        'ambiente',
        'descricao',
        'largura',
        'altura',
        'quantidade',
        'unidade_calculo',
        'area_m2',
        'valor_unitario',
        'valor_total',
        'observacoes'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'orcamento_id' => 'required|integer',
        'descricao' => 'required|min_length[2]',
        'quantidade' => 'required|decimal',
        'valor_unitario' => 'required|decimal',
        'valor_total' => 'required|decimal',
    ];

    protected $validationMessages = [
        'descricao' => [
            'required' => 'A descrição do item é obrigatória.',
        ],
    ];

    protected $skipValidation = false;
}