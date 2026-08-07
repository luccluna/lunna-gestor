<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriaServicoModel extends Model
{
    protected $table = 'categorias_servicos';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'nome',
        'descricao',
        'ativo'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'nome' => 'required|min_length[2]|max_length[100]',
    ];

    protected $validationMessages = [
        'nome' => [
            'required' => 'O nome da categoria é obrigatório.',
            'min_length' => 'O nome da categoria precisa ter pelo menos 2 caracteres.',
        ],
    ];

    protected $skipValidation = false;
}