<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLunnaGestorSchema extends Migration
{
    public function up()
    {
        $this->criarUsuarios();
        $this->criarClientes();
        $this->criarCategoriasServicos();
        $this->criarProdutosServicos();
        $this->criarOrcamentos();
        $this->criarOrcamentoItens();
        $this->criarPedidos();
        $this->criarPedidoStatusHistorico();
        $this->criarAgenda();
        $this->criarPagamentos();
    }

    public function down()
    {
        $this->forge->dropTable('pagamentos', true);
        $this->forge->dropTable('agenda', true);
        $this->forge->dropTable('pedido_status_historico', true);
        $this->forge->dropTable('pedidos', true);
        $this->forge->dropTable('orcamento_itens', true);
        $this->forge->dropTable('orcamentos', true);
        $this->forge->dropTable('produtos_servicos', true);
        $this->forge->dropTable('categorias_servicos', true);
        $this->forge->dropTable('clientes', true);
        $this->forge->dropTable('usuarios', true);
    }

    private function criarUsuarios(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'nome' => ['type' => 'VARCHAR', 'constraint' => 120],
            'email' => ['type' => 'VARCHAR', 'constraint' => 120],
            'senha' => ['type' => 'VARCHAR', 'constraint' => 255],
            'perfil' => [
                'type' => 'ENUM',
                'constraint' => ['administrador', 'gerente', 'vendedor', 'financeiro', 'medidor', 'instalador'],
                'default' => 'vendedor',
            ],
            'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'ultimo_acesso' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('email', 'email');
        $this->forge->createTable('usuarios', true, $this->tableAttributes());
    }

    private function criarClientes(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'nome' => ['type' => 'VARCHAR', 'constraint' => 150],
            'tipo_cliente' => [
                'type' => 'ENUM',
                'constraint' => ['pessoa_fisica', 'pessoa_juridica', 'construtora', 'condominio', 'arquiteto', 'outro'],
                'default' => 'pessoa_fisica',
            ],
            'cpf_cnpj' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'telefone' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'whatsapp' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'cep' => ['type' => 'VARCHAR', 'constraint' => 12, 'null' => true],
            'endereco' => ['type' => 'VARCHAR', 'constraint' => 180, 'null' => true],
            'numero' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'complemento' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'bairro' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'cidade' => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'São Francisco'],
            'estado' => ['type' => 'CHAR', 'constraint' => 2, 'default' => 'MG'],
            'observacoes' => ['type' => 'TEXT', 'null' => true],
            'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('clientes', true, $this->tableAttributes());
    }

    private function criarCategoriasServicos(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'nome' => ['type' => 'VARCHAR', 'constraint' => 100],
            'descricao' => ['type' => 'TEXT', 'null' => true],
            'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('categorias_servicos', true, $this->tableAttributes());
    }

    private function criarProdutosServicos(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'categoria_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true],
            'nome' => ['type' => 'VARCHAR', 'constraint' => 150],
            'tipo' => ['type' => 'ENUM', 'constraint' => ['produto', 'servico'], 'default' => 'produto'],
            'unidade_calculo' => [
                'type' => 'ENUM',
                'constraint' => ['m2', 'metro_linear', 'unidade', 'servico_fechado'],
                'default' => 'm2',
            ],
            'valor_base' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00'],
            'custo_base' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00'],
            'margem_lucro' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => '0.00'],
            'descricao' => ['type' => 'TEXT', 'null' => true],
            'observacoes' => ['type' => 'TEXT', 'null' => true],
            'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('categoria_id', false, false, 'fk_produtos_categoria');
        $this->forge->addForeignKey('categoria_id', 'categorias_servicos', 'id', '', 'SET NULL', 'fk_produtos_categoria');
        $this->forge->createTable('produtos_servicos', true, $this->tableAttributes());
    }

    private function criarOrcamentos(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'numero' => ['type' => 'VARCHAR', 'constraint' => 30],
            'cliente_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'usuario_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true],
            'data_orcamento' => ['type' => 'DATE'],
            'validade' => ['type' => 'DATE', 'null' => true],
            'prazo_entrega' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['novo', 'aguardando_medicao', 'em_elaboracao', 'enviado', 'em_negociacao', 'aprovado', 'recusado', 'cancelado'],
                'default' => 'novo',
            ],
            'subtotal' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00'],
            'desconto' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00'],
            'total' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00'],
            'forma_pagamento' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'observacoes_cliente' => ['type' => 'TEXT', 'null' => true],
            'observacoes_internas' => ['type' => 'TEXT', 'null' => true],
            'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('numero', 'numero');
        $this->forge->addKey('cliente_id', false, false, 'fk_orcamentos_cliente');
        $this->forge->addForeignKey('cliente_id', 'clientes', 'id', '', '', 'fk_orcamentos_cliente');
        $this->forge->createTable('orcamentos', true, $this->tableAttributes());
    }

    private function criarOrcamentoItens(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'orcamento_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'produto_servico_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true],
            'ambiente' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'descricao' => ['type' => 'VARCHAR', 'constraint' => 255],
            'largura' => ['type' => 'DECIMAL', 'constraint' => '10,3', 'default' => '0.000'],
            'altura' => ['type' => 'DECIMAL', 'constraint' => '10,3', 'default' => '0.000'],
            'quantidade' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '1.00'],
            'unidade_calculo' => [
                'type' => 'ENUM',
                'constraint' => ['m2', 'metro_linear', 'unidade', 'servico_fechado'],
                'default' => 'm2',
            ],
            'area_m2' => ['type' => 'DECIMAL', 'constraint' => '10,3', 'default' => '0.000'],
            'valor_unitario' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00'],
            'valor_total' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00'],
            'observacoes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('orcamento_id', false, false, 'fk_itens_orcamento');
        $this->forge->addKey('produto_servico_id', false, false, 'fk_itens_produto');
        $this->forge->addForeignKey('orcamento_id', 'orcamentos', 'id', '', 'CASCADE', 'fk_itens_orcamento');
        $this->forge->addForeignKey('produto_servico_id', 'produtos_servicos', 'id', '', 'SET NULL', 'fk_itens_produto');
        $this->forge->createTable('orcamento_itens', true, $this->tableAttributes());
    }

    private function criarPedidos(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'numero' => ['type' => 'VARCHAR', 'constraint' => 30],
            'orcamento_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'cliente_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'usuario_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true],
            'data_pedido' => ['type' => 'DATE'],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['aprovado', 'aguardando_entrada', 'aguardando_material', 'em_producao', 'pronto_para_instalacao', 'instalacao_agendada', 'em_instalacao', 'instalado', 'finalizado', 'cancelado'],
                'default' => 'aprovado',
            ],
            'subtotal' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00'],
            'desconto' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00'],
            'total' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00'],
            'observacoes' => ['type' => 'TEXT', 'null' => true],
            'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('numero', 'numero');
        $this->forge->addKey('orcamento_id', false, false, 'fk_pedidos_orcamento');
        $this->forge->addKey('cliente_id', false, false, 'fk_pedidos_cliente');
        $this->forge->addForeignKey('cliente_id', 'clientes', 'id', '', '', 'fk_pedidos_cliente');
        $this->forge->addForeignKey('orcamento_id', 'orcamentos', 'id', '', '', 'fk_pedidos_orcamento');
        $this->forge->createTable('pedidos', true, $this->tableAttributes());
    }

    private function criarPedidoStatusHistorico(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'pedido_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'status_anterior' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'status_novo' => ['type' => 'VARCHAR', 'constraint' => 50],
            'observacao' => ['type' => 'TEXT', 'null' => true],
            'usuario_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('pedido_id', false, false, 'fk_historico_pedido');
        $this->forge->addForeignKey('pedido_id', 'pedidos', 'id', '', 'CASCADE', 'fk_historico_pedido');
        $this->forge->createTable('pedido_status_historico', true, $this->tableAttributes());
    }

    private function criarAgenda(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'cliente_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'pedido_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true],
            'usuario_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true],
            'tipo' => [
                'type' => 'ENUM',
                'constraint' => ['medicao', 'instalacao', 'manutencao', 'retorno', 'entrega', 'visita_comercial'],
                'default' => 'medicao',
            ],
            'titulo' => ['type' => 'VARCHAR', 'constraint' => 150],
            'data_agenda' => ['type' => 'DATE'],
            'hora_inicio' => ['type' => 'TIME', 'null' => true],
            'hora_fim' => ['type' => 'TIME', 'null' => true],
            'responsavel' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'endereco' => ['type' => 'VARCHAR', 'constraint' => 180, 'null' => true],
            'numero' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'complemento' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'bairro' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'cidade' => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'São Francisco'],
            'estado' => ['type' => 'CHAR', 'constraint' => 2, 'default' => 'MG'],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['agendado', 'confirmado', 'em_rota', 'em_andamento', 'concluido', 'reagendado', 'cancelado'],
                'default' => 'agendado',
            ],
            'observacoes' => ['type' => 'TEXT', 'null' => true],
            'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('cliente_id', false, false, 'fk_agenda_cliente');
        $this->forge->addKey('pedido_id', false, false, 'fk_agenda_pedido');
        $this->forge->addForeignKey('cliente_id', 'clientes', 'id', '', '', 'fk_agenda_cliente');
        $this->forge->addForeignKey('pedido_id', 'pedidos', 'id', '', 'SET NULL', 'fk_agenda_pedido');
        $this->forge->createTable('agenda', true, $this->tableAttributes());
    }

    private function criarPagamentos(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'pedido_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'cliente_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'usuario_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true],
            'descricao' => ['type' => 'VARCHAR', 'constraint' => 150],
            'tipo' => [
                'type' => 'ENUM',
                'constraint' => ['entrada', 'parcela', 'saldo_final', 'pagamento_unico', 'outro'],
                'default' => 'parcela',
            ],
            'forma_pagamento' => [
                'type' => 'ENUM',
                'constraint' => ['pix', 'dinheiro', 'cartao_debito', 'cartao_credito', 'boleto', 'transferencia', 'cheque', 'outro'],
                'default' => 'pix',
            ],
            'valor' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00'],
            'data_vencimento' => ['type' => 'DATE'],
            'data_pagamento' => ['type' => 'DATE', 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['pendente', 'pago', 'atrasado', 'cancelado'], 'default' => 'pendente'],
            'observacoes' => ['type' => 'TEXT', 'null' => true],
            'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('pedido_id', false, false, 'fk_pagamentos_pedido');
        $this->forge->addKey('cliente_id', false, false, 'fk_pagamentos_cliente');
        $this->forge->addForeignKey('cliente_id', 'clientes', 'id', '', '', 'fk_pagamentos_cliente');
        $this->forge->addForeignKey('pedido_id', 'pedidos', 'id', '', '', 'fk_pagamentos_pedido');
        $this->forge->createTable('pagamentos', true, $this->tableAttributes());
    }

    private function tableAttributes(): array
    {
        return [
            'ENGINE' => 'InnoDB',
            'CHARACTER SET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_general_ci',
        ];
    }
}
