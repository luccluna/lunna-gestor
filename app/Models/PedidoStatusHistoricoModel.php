<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidoStatusHistoricoModel extends Model
{
    protected $table = 'pedido_status_historico';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'pedido_id',
        'status_anterior',
        'status_novo',
        'observacao',
        'usuario_id',
        'created_at'
    ];

    protected $useTimestamps = false;
}