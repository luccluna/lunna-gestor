<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdutoServicoModel extends Model
{
    protected $table = 'produtos_servicos';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'categoria_id',
        'nome',
        'tipo',
        'unidade_calculo',
        'valor_base',
        'custo_base',
        'margem_lucro',
        'descricao',
        'observacoes',
        'ativo'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'nome' => 'required|min_length[3]|max_length[150]',
        'tipo' => 'required|in_list[produto,servico]',
        'unidade_calculo' => 'required|in_list[m2,metro_linear,unidade,servico_fechado]',
        'valor_base' => 'permit_empty|decimal',
        'custo_base' => 'permit_empty|decimal',
        'margem_lucro' => 'permit_empty|decimal',
    ];

    protected $validationMessages = [
        'nome' => [
            'required' => 'O nome do produto/serviço é obrigatório.',
            'min_length' => 'O nome precisa ter pelo menos 3 caracteres.',
        ],
        'tipo' => [
            'required' => 'Informe se é produto ou serviço.',
            'in_list' => 'Tipo inválido.',
        ],
        'unidade_calculo' => [
            'required' => 'Informe a unidade de cálculo.',
            'in_list' => 'Unidade de cálculo inválida.',
        ],
    ];

    protected $skipValidation = false;
}