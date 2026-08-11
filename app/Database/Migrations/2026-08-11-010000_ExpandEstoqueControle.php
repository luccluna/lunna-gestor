<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ExpandEstoqueControle extends Migration
{
    public function up()
    {
        $colunasMateriais = [
            'tipo_controle' => [
                'type' => 'ENUM',
                'constraint' => ['unidade', 'metro_linear', 'metro_quadrado', 'chapa', 'retalho', 'servico_sem_estoque'],
                'default' => 'unidade',
                'after' => 'unidade_medida',
            ],
            'espessura' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true, 'after' => 'tipo_controle'],
            'largura' => ['type' => 'DECIMAL', 'constraint' => '10,3', 'default' => '0.000', 'after' => 'espessura'],
            'altura' => ['type' => 'DECIMAL', 'constraint' => '10,3', 'default' => '0.000', 'after' => 'largura'],
            'comprimento' => ['type' => 'DECIMAL', 'constraint' => '10,3', 'default' => '0.000', 'after' => 'altura'],
            'lote' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true, 'after' => 'localizacao'],
            'origem' => [
                'type' => 'ENUM',
                'constraint' => ['manual', 'nota_compra', 'ajuste', 'retalho'],
                'default' => 'manual',
                'after' => 'lote',
            ],
            'nf_numero' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true, 'after' => 'origem'],
            'nf_chave_acesso' => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true, 'after' => 'nf_numero'],
            'data_compra' => ['type' => 'DATE', 'null' => true, 'after' => 'nf_chave_acesso'],
        ];

        $colunasMovimentacoes = [
            'origem' => [
                'type' => 'ENUM',
                'constraint' => ['manual', 'nota_compra', 'pedido', 'ajuste', 'retalho'],
                'default' => 'manual',
                'after' => 'tipo',
            ],
            'nf_numero' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true, 'after' => 'documento'],
            'nf_chave_acesso' => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true, 'after' => 'nf_numero'],
            'fornecedor' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true, 'after' => 'nf_chave_acesso'],
            'lote' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true, 'after' => 'fornecedor'],
        ];

        $this->adicionarColunasAusentes('estoque_materiais', $colunasMateriais);
        $this->adicionarColunasAusentes('estoque_movimentacoes', $colunasMovimentacoes);
    }

    public function down()
    {
        $this->removerColunasExistentes('estoque_movimentacoes', [
            'origem',
            'nf_numero',
            'nf_chave_acesso',
            'fornecedor',
            'lote',
        ]);

        $this->removerColunasExistentes('estoque_materiais', [
            'tipo_controle',
            'espessura',
            'largura',
            'altura',
            'comprimento',
            'lote',
            'origem',
            'nf_numero',
            'nf_chave_acesso',
            'data_compra',
        ]);
    }

    private function adicionarColunasAusentes(string $tabela, array $colunas): void
    {
        $ausentes = [];

        foreach ($colunas as $nome => $definicao) {
            if (!$this->db->fieldExists($nome, $tabela)) {
                $ausentes[$nome] = $definicao;
            }
        }

        if (!empty($ausentes)) {
            $this->forge->addColumn($tabela, $ausentes);
        }
    }

    private function removerColunasExistentes(string $tabela, array $colunas): void
    {
        $existentes = [];

        foreach ($colunas as $coluna) {
            if ($this->db->fieldExists($coluna, $tabela)) {
                $existentes[] = $coluna;
            }
        }

        foreach ($existentes as $coluna) {
            $this->forge->dropColumn($tabela, $coluna);
        }
    }
}
