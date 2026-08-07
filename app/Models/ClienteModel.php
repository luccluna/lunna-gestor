<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType = 'array';

    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'nome',
        'tipo_cliente',
        'cpf_cnpj',
        'telefone',
        'whatsapp',
        'email',
        'cep',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'observacoes',
        'ativo'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'nome' => 'required|min_length[3]|max_length[150]',
        'email' => 'permit_empty|valid_email',
    ];

    protected $validationMessages = [
        'nome' => [
            'required' => 'O nome do cliente é obrigatório.',
            'min_length' => 'O nome precisa ter pelo menos 3 caracteres.',
        ],
        'email' => [
            'valid_email' => 'Informe um e-mail válido.',
        ],
    ];

    protected $skipValidation = false;
}