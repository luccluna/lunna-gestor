<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'nome',
        'email',
        'senha',
        'perfil',
        'ativo',
        'ultimo_acesso'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'nome' => 'required|min_length[3]|max_length[120]',
        'email' => 'required|valid_email|max_length[120]',
        'perfil' => 'required|in_list[administrador,gerente,vendedor,financeiro,medidor,instalador]',
    ];

    protected $validationMessages = [
        'nome' => [
            'required' => 'O nome do usuário é obrigatório.',
            'min_length' => 'O nome precisa ter pelo menos 3 caracteres.',
        ],
        'email' => [
            'required' => 'O e-mail é obrigatório.',
            'valid_email' => 'Informe um e-mail válido.',
        ],
        'perfil' => [
            'required' => 'Selecione um perfil de acesso.',
            'in_list' => 'Perfil inválido.',
        ],
    ];

    protected $skipValidation = false;
}