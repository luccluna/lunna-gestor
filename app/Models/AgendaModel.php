<?php

namespace App\Models;

use CodeIgniter\Model;

class AgendaModel extends Model
{
    protected $table = 'agenda';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'cliente_id',
        'pedido_id',
        'usuario_id',
        'tipo',
        'titulo',
        'data_agenda',
        'hora_inicio',
        'hora_fim',
        'responsavel',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'status',
        'observacoes',
        'ativo'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'cliente_id' => 'required|integer',
        'tipo' => 'required|in_list[medicao,instalacao,manutencao,retorno,entrega,visita_comercial]',
        'titulo' => 'required|min_length[3]|max_length[150]',
        'data_agenda' => 'required',
        'status' => 'required|in_list[agendado,confirmado,em_rota,em_andamento,concluido,reagendado,cancelado]',
    ];

    protected $validationMessages = [
        'cliente_id' => [
            'required' => 'Selecione um cliente.',
        ],
        'titulo' => [
            'required' => 'Informe o título do compromisso.',
            'min_length' => 'O título precisa ter pelo menos 3 caracteres.',
        ],
        'data_agenda' => [
            'required' => 'Informe a data do compromisso.',
        ],
    ];

    protected $skipValidation = false;
}