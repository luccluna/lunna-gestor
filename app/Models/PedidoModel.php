<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidoModel extends Model
{
    protected $table = 'pedidos';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'numero',
        'orcamento_id',
        'cliente_id',
        'usuario_id',
        'data_pedido',
        'status',
        'subtotal',
        'desconto',
        'total',
        'observacoes',
        'ativo'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'numero' => 'required|max_length[30]',
        'orcamento_id' => 'required|integer',
        'cliente_id' => 'required|integer',
        'data_pedido' => 'required|valid_date',
        'status' => 'required',
    ];

    protected $skipValidation = false;
}