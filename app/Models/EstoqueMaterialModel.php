<?php

namespace App\Models;

use CodeIgniter\Model;

class EstoqueMaterialModel extends Model
{
    protected $table = 'estoque_materiais';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'produto_servico_id',
        'nome',
        'unidade_medida',
        'tipo_controle',
        'espessura',
        'largura',
        'altura',
        'comprimento',
        'fornecedor',
        'localizacao',
        'lote',
        'origem',
        'nf_numero',
        'nf_chave_acesso',
        'data_compra',
        'saldo_atual',
        'estoque_minimo',
        'custo_unitario',
        'descricao',
        'observacoes',
        'ativo',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'nome' => 'required|min_length[3]|max_length[150]',
        'unidade_medida' => 'required|max_length[30]',
        'tipo_controle' => 'required|in_list[unidade,metro_linear,metro_quadrado,chapa,retalho,servico_sem_estoque]',
        'origem' => 'permit_empty|in_list[manual,nota_compra,ajuste,retalho]',
        'saldo_atual' => 'permit_empty|decimal',
        'estoque_minimo' => 'permit_empty|decimal',
        'custo_unitario' => 'permit_empty|decimal',
    ];

    protected $validationMessages = [
        'nome' => [
            'required' => 'O nome do material e obrigatorio.',
            'min_length' => 'O nome precisa ter pelo menos 3 caracteres.',
        ],
        'unidade_medida' => [
            'required' => 'Informe a unidade de medida.',
        ],
    ];

    protected $skipValidation = false;
}
