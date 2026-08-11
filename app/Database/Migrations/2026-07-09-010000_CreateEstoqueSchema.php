<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEstoqueSchema extends Migration
{
    public function up()
    {
        $this->criarEstoqueMateriais();
        $this->criarEstoqueMovimentacoes();
    }

    public function down()
    {
        $this->forge->dropTable('estoque_movimentacoes', true);
        $this->forge->dropTable('estoque_materiais', true);
    }

    private function criarEstoqueMateriais(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'produto_servico_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true],
            'nome' => ['type' => 'VARCHAR', 'constraint' => 150],
            'unidade_medida' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'unidade'],
            'tipo_controle' => [
                'type' => 'ENUM',
                'constraint' => ['unidade', 'metro_linear', 'metro_quadrado', 'chapa', 'retalho', 'servico_sem_estoque'],
                'default' => 'unidade',
            ],
            'espessura' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'largura' => ['type' => 'DECIMAL', 'constraint' => '10,3', 'default' => '0.000'],
            'altura' => ['type' => 'DECIMAL', 'constraint' => '10,3', 'default' => '0.000'],
            'comprimento' => ['type' => 'DECIMAL', 'constraint' => '10,3', 'default' => '0.000'],
            'fornecedor' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'localizacao' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'lote' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'origem' => [
                'type' => 'ENUM',
                'constraint' => ['manual', 'nota_compra', 'ajuste', 'retalho'],
                'default' => 'manual',
            ],
            'nf_numero' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'nf_chave_acesso' => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'data_compra' => ['type' => 'DATE', 'null' => true],
            'saldo_atual' => ['type' => 'DECIMAL', 'constraint' => '10,3', 'default' => '0.000'],
            'estoque_minimo' => ['type' => 'DECIMAL', 'constraint' => '10,3', 'default' => '0.000'],
            'custo_unitario' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00'],
            'descricao' => ['type' => 'TEXT', 'null' => true],
            'observacoes' => ['type' => 'TEXT', 'null' => true],
            'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('produto_servico_id', false, false, 'fk_estoque_material_produto');
        $this->forge->addKey('nome', false, false, 'idx_estoque_material_nome');
        $this->forge->addForeignKey('produto_servico_id', 'produtos_servicos', 'id', '', 'SET NULL', 'fk_estoque_material_produto');
        $this->forge->createTable('estoque_materiais', true, $this->tableAttributes());
    }

    private function criarEstoqueMovimentacoes(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'material_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'pedido_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true],
            'usuario_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true],
            'tipo' => ['type' => 'ENUM', 'constraint' => ['entrada', 'saida'], 'default' => 'entrada'],
            'origem' => [
                'type' => 'ENUM',
                'constraint' => ['manual', 'nota_compra', 'pedido', 'ajuste', 'retalho'],
                'default' => 'manual',
            ],
            'documento' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'nf_numero' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'nf_chave_acesso' => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'fornecedor' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'lote' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'quantidade' => ['type' => 'DECIMAL', 'constraint' => '10,3'],
            'custo_unitario' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00'],
            'saldo_anterior' => ['type' => 'DECIMAL', 'constraint' => '10,3', 'default' => '0.000'],
            'saldo_posterior' => ['type' => 'DECIMAL', 'constraint' => '10,3', 'default' => '0.000'],
            'data_movimentacao' => ['type' => 'DATE'],
            'observacoes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('material_id', false, false, 'fk_estoque_mov_material');
        $this->forge->addKey('pedido_id', false, false, 'fk_estoque_mov_pedido');
        $this->forge->addForeignKey('material_id', 'estoque_materiais', 'id', '', 'CASCADE', 'fk_estoque_mov_material');
        $this->forge->addForeignKey('pedido_id', 'pedidos', 'id', '', 'SET NULL', 'fk_estoque_mov_pedido');
        $this->forge->createTable('estoque_movimentacoes', true, $this->tableAttributes());
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
