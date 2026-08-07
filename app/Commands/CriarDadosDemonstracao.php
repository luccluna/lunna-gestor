<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class CriarDadosDemonstracao extends BaseCommand
{
    protected $group = 'Demo';
    protected $name = 'demo:criar-dados';
    protected $description = 'Cria ou atualiza uma massa de dados ficticia para demonstracao.';
    protected $usage = 'demo:criar-dados';

    private $db;
    private string $now;

    public function run(array $params)
    {
        $this->db = Database::connect();
        $this->now = date('Y-m-d H:i:s');

        $this->db->transBegin();

        try {
            $usuarios = $this->criarUsuarios();
            $clientes = $this->criarClientes();
            $produtos = $this->criarProdutos();
            $materiais = $this->criarEstoque($produtos);
            $this->criarFluxos($usuarios, $clientes, $produtos, $materiais);

            $this->db->transCommit();
        } catch (\Throwable $exception) {
            $this->db->transRollback();
            CLI::error('Erro ao criar dados de demonstracao: ' . $exception->getMessage());
            return;
        }

        CLI::write('Dados de demonstracao criados/atualizados com sucesso.', 'green');
        CLI::write('Senha dos usuarios de teste: Teste@123', 'yellow');
    }

    private function criarUsuarios(): array
    {
        $senha = password_hash('Teste@123', PASSWORD_DEFAULT);

        $usuarios = [
            'admin' => ['[TESTE DEMO] Administrador', 'demo.admin@teste.com', 'administrador'],
            'gerente' => ['[TESTE DEMO] Gerente', 'demo.gerente@teste.com', 'gerente'],
            'vendedor' => ['[TESTE DEMO] Vendedor', 'demo.vendedor@teste.com', 'vendedor'],
            'financeiro' => ['[TESTE DEMO] Financeiro', 'demo.financeiro@teste.com', 'financeiro'],
            'medidor' => ['[TESTE DEMO] Medidor', 'demo.medidor@teste.com', 'medidor'],
            'instalador' => ['[TESTE DEMO] Instalador', 'demo.instalador@teste.com', 'instalador'],
        ];

        $ids = [];

        foreach ($usuarios as $chave => [$nome, $email, $perfil]) {
            $ids[$chave] = $this->upsert('usuarios', ['email' => $email], [
                'nome' => $nome,
                'email' => $email,
                'senha' => $senha,
                'perfil' => $perfil,
                'ativo' => 1,
                'deleted_at' => null,
            ]);
        }

        return $ids;
    }

    private function criarClientes(): array
    {
        return [
            'ana' => $this->upsert('clientes', ['email' => 'ana.reforma@teste.com'], [
                'nome' => '[TESTE DEMO] Ana Paula Reforma',
                'tipo_cliente' => 'pessoa_fisica',
                'cpf_cnpj' => '123.456.789-09',
                'telefone' => '(38) 99911-0101',
                'whatsapp' => '(38) 99911-0101',
                'email' => 'ana.reforma@teste.com',
                'cep' => '39300-000',
                'endereco' => 'Rua das Acacias',
                'numero' => '120',
                'complemento' => 'Casa',
                'bairro' => 'Centro',
                'cidade' => 'Sao Francisco',
                'estado' => 'MG',
                'observacoes' => '[TESTE DEMO] Cliente interessado em box e espelho para reforma.',
                'ativo' => 1,
                'deleted_at' => null,
            ]),
            'construtora' => $this->upsert('clientes', ['email' => 'obras.horizonte@teste.com'], [
                'nome' => '[TESTE DEMO] Construtora Horizonte',
                'tipo_cliente' => 'construtora',
                'cpf_cnpj' => '12.345.678/0001-90',
                'telefone' => '(38) 3333-0102',
                'whatsapp' => '(38) 99922-0102',
                'email' => 'obras.horizonte@teste.com',
                'cep' => '39300-000',
                'endereco' => 'Avenida Principal',
                'numero' => '850',
                'complemento' => 'Sala 2',
                'bairro' => 'Sagrada Familia',
                'cidade' => 'Sao Francisco',
                'estado' => 'MG',
                'observacoes' => '[TESTE DEMO] Cliente recorrente para esquadrias e guarda-corpo.',
                'ativo' => 1,
                'deleted_at' => null,
            ]),
            'arquiteta' => $this->upsert('clientes', ['email' => 'clara.arq@teste.com'], [
                'nome' => '[TESTE DEMO] Clara Arquitetura',
                'tipo_cliente' => 'arquiteto',
                'cpf_cnpj' => '987.654.321-00',
                'telefone' => '(38) 99933-0103',
                'whatsapp' => '(38) 99933-0103',
                'email' => 'clara.arq@teste.com',
                'cep' => '39300-000',
                'endereco' => 'Rua Projetada',
                'numero' => '45',
                'bairro' => 'Jardim Primavera',
                'cidade' => 'Sao Francisco',
                'estado' => 'MG',
                'observacoes' => '[TESTE DEMO] Parceira para projetos com espelhos e portas de vidro.',
                'ativo' => 1,
                'deleted_at' => null,
            ]),
        ];
    }

    private function criarProdutos(): array
    {
        $categoriaVidros = $this->upsert('categorias_servicos', ['nome' => '[TESTE DEMO] Vidros e Box'], [
            'nome' => '[TESTE DEMO] Vidros e Box',
            'descricao' => 'Itens ficticios para demonstracao.',
            'ativo' => 1,
            'deleted_at' => null,
        ]);

        $categoriaServicos = $this->upsert('categorias_servicos', ['nome' => '[TESTE DEMO] Servicos'], [
            'nome' => '[TESTE DEMO] Servicos',
            'descricao' => 'Servicos ficticios para demonstracao.',
            'ativo' => 1,
            'deleted_at' => null,
        ]);

        return [
            'box' => $this->upsert('produtos_servicos', ['nome' => '[TESTE DEMO] Box vidro temperado 8mm'], [
                'categoria_id' => $categoriaVidros,
                'nome' => '[TESTE DEMO] Box vidro temperado 8mm',
                'tipo' => 'produto',
                'unidade_calculo' => 'm2',
                'valor_base' => 520.00,
                'custo_base' => 310.00,
                'margem_lucro' => 40.00,
                'descricao' => 'Box sob medida para banheiro.',
                'observacoes' => '[TESTE DEMO] Conferir medida antes da producao.',
                'ativo' => 1,
                'deleted_at' => null,
            ]),
            'espelho' => $this->upsert('produtos_servicos', ['nome' => '[TESTE DEMO] Espelho lapidado sob medida'], [
                'categoria_id' => $categoriaVidros,
                'nome' => '[TESTE DEMO] Espelho lapidado sob medida',
                'tipo' => 'produto',
                'unidade_calculo' => 'm2',
                'valor_base' => 390.00,
                'custo_base' => 220.00,
                'margem_lucro' => 35.00,
                'descricao' => 'Espelho lapidado com instalacao.',
                'observacoes' => '[TESTE DEMO] Item usado em orcamento de demonstracao.',
                'ativo' => 1,
                'deleted_at' => null,
            ]),
            'guarda_corpo' => $this->upsert('produtos_servicos', ['nome' => '[TESTE DEMO] Guarda-corpo em vidro'], [
                'categoria_id' => $categoriaVidros,
                'nome' => '[TESTE DEMO] Guarda-corpo em vidro',
                'tipo' => 'produto',
                'unidade_calculo' => 'metro_linear',
                'valor_base' => 780.00,
                'custo_base' => 480.00,
                'margem_lucro' => 38.00,
                'descricao' => 'Guarda-corpo com vidro temperado e ferragens.',
                'observacoes' => '[TESTE DEMO] Verificar ferragens em estoque.',
                'ativo' => 1,
                'deleted_at' => null,
            ]),
            'instalacao' => $this->upsert('produtos_servicos', ['nome' => '[TESTE DEMO] Instalacao tecnica'], [
                'categoria_id' => $categoriaServicos,
                'nome' => '[TESTE DEMO] Instalacao tecnica',
                'tipo' => 'servico',
                'unidade_calculo' => 'servico_fechado',
                'valor_base' => 250.00,
                'custo_base' => 120.00,
                'margem_lucro' => 50.00,
                'descricao' => 'Servico de instalacao e acabamento.',
                'observacoes' => '[TESTE DEMO] Usado para demonstrar servicos no orcamento.',
                'ativo' => 1,
                'deleted_at' => null,
            ]),
        ];
    }

    private function criarEstoque(array $produtos): array
    {
        $materiais = [
            'vidro8' => $this->upsert('estoque_materiais', ['nome' => '[TESTE DEMO] Chapa vidro temperado 8mm'], [
                'produto_servico_id' => $produtos['box'],
                'nome' => '[TESTE DEMO] Chapa vidro temperado 8mm',
                'unidade_medida' => 'm2',
                'fornecedor' => '[TESTE DEMO] Fornecedor Vidros Norte',
                'localizacao' => 'Estoque principal',
                'saldo_atual' => 38.500,
                'estoque_minimo' => 12.000,
                'custo_unitario' => 310.00,
                'descricao' => 'Material ficticio para box e portas.',
                'observacoes' => '[TESTE DEMO] Saldo criado para apresentacao.',
                'ativo' => 1,
                'deleted_at' => null,
            ]),
            'ferragem' => $this->upsert('estoque_materiais', ['nome' => '[TESTE DEMO] Kit ferragem inox box'], [
                'produto_servico_id' => $produtos['box'],
                'nome' => '[TESTE DEMO] Kit ferragem inox box',
                'unidade_medida' => 'unidade',
                'fornecedor' => '[TESTE DEMO] Ferragens Sao Lucas',
                'localizacao' => 'Prateleira A',
                'saldo_atual' => 4.000,
                'estoque_minimo' => 6.000,
                'custo_unitario' => 95.00,
                'descricao' => 'Item propositalmente abaixo do minimo.',
                'observacoes' => '[TESTE DEMO] Usado para mostrar alerta de baixo estoque.',
                'ativo' => 1,
                'deleted_at' => null,
            ]),
            'espelho' => $this->upsert('estoque_materiais', ['nome' => '[TESTE DEMO] Espelho prata 4mm'], [
                'produto_servico_id' => $produtos['espelho'],
                'nome' => '[TESTE DEMO] Espelho prata 4mm',
                'unidade_medida' => 'm2',
                'fornecedor' => '[TESTE DEMO] Distribuidora Minas Glass',
                'localizacao' => 'Cavalete 2',
                'saldo_atual' => 18.000,
                'estoque_minimo' => 5.000,
                'custo_unitario' => 220.00,
                'descricao' => 'Espelho para banheiros e salas.',
                'observacoes' => '[TESTE DEMO] Material para demonstracao.',
                'ativo' => 1,
                'deleted_at' => null,
            ]),
        ];

        foreach ($materiais as $materialId) {
            $material = $this->db->table('estoque_materiais')->where('id', $materialId)->get()->getRowArray();
            $this->upsert('estoque_movimentacoes', [
                'material_id' => $materialId,
                'documento' => '[TESTE DEMO] Saldo inicial',
            ], [
                'material_id' => $materialId,
                'pedido_id' => null,
                'usuario_id' => null,
                'tipo' => 'entrada',
                'documento' => '[TESTE DEMO] Saldo inicial',
                'quantidade' => $material['saldo_atual'],
                'custo_unitario' => $material['custo_unitario'],
                'saldo_anterior' => 0,
                'saldo_posterior' => $material['saldo_atual'],
                'data_movimentacao' => date('Y-m-d', strtotime('-12 days')),
                'observacoes' => '[TESTE DEMO] Entrada inicial para demonstracao do estoque.',
                'created_at' => $this->now,
            ], false);
        }

        return $materiais;
    }

    private function criarFluxos(array $usuarios, array $clientes, array $produtos, array $materiais): void
    {
        $hoje = date('Y-m-d');

        $orcamentoAprovado = $this->upsert('orcamentos', ['numero' => 'ORC-DEMO-2026-0001'], [
            'numero' => 'ORC-DEMO-2026-0001',
            'cliente_id' => $clientes['ana'],
            'usuario_id' => $usuarios['vendedor'],
            'data_orcamento' => date('Y-m-d', strtotime('-8 days')),
            'validade' => date('Y-m-d', strtotime('+7 days')),
            'prazo_entrega' => '7 dias uteis apos aprovacao',
            'status' => 'aprovado',
            'subtotal' => 3201.20,
            'desconto' => 101.20,
            'total' => 3100.00,
            'forma_pagamento' => 'Entrada no Pix e saldo na instalacao',
            'observacoes_cliente' => '[TESTE DEMO] Orçamento aprovado para box e espelho.',
            'observacoes_internas' => '[TESTE DEMO] Separar ferragens antes da instalacao.',
            'ativo' => 1,
            'deleted_at' => null,
        ]);

        $this->upsert('orcamento_itens', ['orcamento_id' => $orcamentoAprovado, 'descricao' => '[TESTE DEMO] Box vidro temperado 8mm - banheiro suite'], [
            'orcamento_id' => $orcamentoAprovado,
            'produto_servico_id' => $produtos['box'],
            'ambiente' => 'Banheiro suite',
            'descricao' => '[TESTE DEMO] Box vidro temperado 8mm - banheiro suite',
            'largura' => 1.650,
            'altura' => 1.900,
            'quantidade' => 1.00,
            'unidade_calculo' => 'm2',
            'area_m2' => 3.135,
            'valor_unitario' => 520.00,
            'valor_total' => 1630.20,
            'observacoes' => '[TESTE DEMO] Medida confirmada.',
        ], false);

        $this->upsert('orcamento_itens', ['orcamento_id' => $orcamentoAprovado, 'descricao' => '[TESTE DEMO] Espelho lapidado para bancada'], [
            'orcamento_id' => $orcamentoAprovado,
            'produto_servico_id' => $produtos['espelho'],
            'ambiente' => 'Banheiro social',
            'descricao' => '[TESTE DEMO] Espelho lapidado para bancada',
            'largura' => 1.800,
            'altura' => 1.200,
            'quantidade' => 1.00,
            'unidade_calculo' => 'm2',
            'area_m2' => 2.160,
            'valor_unitario' => 390.00,
            'valor_total' => 842.40,
            'observacoes' => '[TESTE DEMO] Com instalacao.',
        ], false);

        $this->upsert('orcamento_itens', ['orcamento_id' => $orcamentoAprovado, 'descricao' => '[TESTE DEMO] Instalacao e acabamentos'], [
            'orcamento_id' => $orcamentoAprovado,
            'produto_servico_id' => $produtos['instalacao'],
            'ambiente' => 'Obra',
            'descricao' => '[TESTE DEMO] Instalacao e acabamentos',
            'largura' => 0,
            'altura' => 0,
            'quantidade' => 1.00,
            'unidade_calculo' => 'servico_fechado',
            'area_m2' => 0,
            'valor_unitario' => 728.60,
            'valor_total' => 728.60,
            'observacoes' => '[TESTE DEMO] Equipe propria.',
        ], false);

        $pedido = $this->upsert('pedidos', ['numero' => 'PED-DEMO-2026-0001'], [
            'numero' => 'PED-DEMO-2026-0001',
            'orcamento_id' => $orcamentoAprovado,
            'cliente_id' => $clientes['ana'],
            'usuario_id' => $usuarios['vendedor'],
            'data_pedido' => date('Y-m-d', strtotime('-6 days')),
            'status' => 'em_producao',
            'subtotal' => 3201.20,
            'desconto' => 101.20,
            'total' => 3100.00,
            'observacoes' => '[TESTE DEMO] Pedido em produção para demonstrar fluxo completo.',
            'ativo' => 1,
            'deleted_at' => null,
        ]);

        $this->upsert('pedido_status_historico', ['pedido_id' => $pedido, 'status_novo' => 'aprovado'], [
            'pedido_id' => $pedido,
            'status_anterior' => null,
            'status_novo' => 'aprovado',
            'observacao' => '[TESTE DEMO] Pedido criado a partir de orçamento aprovado.',
            'usuario_id' => $usuarios['vendedor'],
            'created_at' => date('Y-m-d H:i:s', strtotime('-6 days')),
        ], false);

        $this->upsert('pedido_status_historico', ['pedido_id' => $pedido, 'status_novo' => 'em_producao'], [
            'pedido_id' => $pedido,
            'status_anterior' => 'aprovado',
            'status_novo' => 'em_producao',
            'observacao' => '[TESTE DEMO] Material separado e produção iniciada.',
            'usuario_id' => $usuarios['gerente'],
            'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
        ], false);

        $this->upsert('agenda', ['titulo' => '[TESTE DEMO] Instalacao box e espelho', 'pedido_id' => $pedido], [
            'cliente_id' => $clientes['ana'],
            'pedido_id' => $pedido,
            'usuario_id' => $usuarios['instalador'],
            'tipo' => 'instalacao',
            'titulo' => '[TESTE DEMO] Instalacao box e espelho',
            'data_agenda' => date('Y-m-d', strtotime('+2 days')),
            'hora_inicio' => '08:00:00',
            'hora_fim' => '11:30:00',
            'responsavel' => '[TESTE DEMO] Instalador',
            'endereco' => 'Rua das Acacias',
            'numero' => '120',
            'complemento' => 'Casa',
            'bairro' => 'Centro',
            'cidade' => 'Sao Francisco',
            'estado' => 'MG',
            'status' => 'confirmado',
            'observacoes' => '[TESTE DEMO] Levar ferragens e silicone.',
            'ativo' => 1,
            'deleted_at' => null,
        ]);

        $this->upsert('pagamentos', ['pedido_id' => $pedido, 'descricao' => '[TESTE DEMO] Entrada do pedido PED-DEMO-2026-0001'], [
            'pedido_id' => $pedido,
            'cliente_id' => $clientes['ana'],
            'usuario_id' => $usuarios['financeiro'],
            'descricao' => '[TESTE DEMO] Entrada do pedido PED-DEMO-2026-0001',
            'tipo' => 'entrada',
            'forma_pagamento' => 'pix',
            'valor' => 1200.00,
            'data_vencimento' => date('Y-m-d', strtotime('-5 days')),
            'data_pagamento' => date('Y-m-d', strtotime('-5 days')),
            'status' => 'pago',
            'observacoes' => '[TESTE DEMO] Entrada paga via Pix.',
            'ativo' => 1,
            'deleted_at' => null,
        ]);

        $this->upsert('pagamentos', ['pedido_id' => $pedido, 'descricao' => '[TESTE DEMO] Saldo final na instalacao'], [
            'pedido_id' => $pedido,
            'cliente_id' => $clientes['ana'],
            'usuario_id' => $usuarios['financeiro'],
            'descricao' => '[TESTE DEMO] Saldo final na instalacao',
            'tipo' => 'saldo_final',
            'forma_pagamento' => 'cartao_credito',
            'valor' => 1900.00,
            'data_vencimento' => date('Y-m-d', strtotime('+2 days')),
            'data_pagamento' => null,
            'status' => 'pendente',
            'observacoes' => '[TESTE DEMO] Cobrar ao finalizar instalacao.',
            'ativo' => 1,
            'deleted_at' => null,
        ]);

        $this->upsert('estoque_movimentacoes', ['material_id' => $materiais['ferragem'], 'pedido_id' => $pedido, 'documento' => '[TESTE DEMO] Saida PED-DEMO-2026-0001'], [
            'material_id' => $materiais['ferragem'],
            'pedido_id' => $pedido,
            'usuario_id' => $usuarios['gerente'],
            'tipo' => 'saida',
            'documento' => '[TESTE DEMO] Saida PED-DEMO-2026-0001',
            'quantidade' => 2.000,
            'custo_unitario' => 95.00,
            'saldo_anterior' => 6.000,
            'saldo_posterior' => 4.000,
            'data_movimentacao' => date('Y-m-d', strtotime('-2 days')),
            'observacoes' => '[TESTE DEMO] Saida vinculada ao pedido de instalacao.',
            'created_at' => $this->now,
        ], false);

        $this->criarOrcamentosComplementares($usuarios, $clientes, $produtos, $hoje);
    }

    private function criarOrcamentosComplementares(array $usuarios, array $clientes, array $produtos, string $hoje): void
    {
        $orcamentoNegociacao = $this->upsert('orcamentos', ['numero' => 'ORC-DEMO-2026-0002'], [
            'numero' => 'ORC-DEMO-2026-0002',
            'cliente_id' => $clientes['construtora'],
            'usuario_id' => $usuarios['vendedor'],
            'data_orcamento' => date('Y-m-d', strtotime('-2 days')),
            'validade' => date('Y-m-d', strtotime('+10 days')),
            'prazo_entrega' => '15 dias uteis',
            'status' => 'em_negociacao',
            'subtotal' => 9360.00,
            'desconto' => 360.00,
            'total' => 9000.00,
            'forma_pagamento' => '40% entrada e saldo por medicao',
            'observacoes_cliente' => '[TESTE DEMO] Em negociacao com construtora.',
            'observacoes_internas' => '[TESTE DEMO] Possivel ajuste de metragem.',
            'ativo' => 1,
            'deleted_at' => null,
        ]);

        $this->upsert('orcamento_itens', ['orcamento_id' => $orcamentoNegociacao, 'descricao' => '[TESTE DEMO] Guarda-corpo para varanda'], [
            'orcamento_id' => $orcamentoNegociacao,
            'produto_servico_id' => $produtos['guarda_corpo'],
            'ambiente' => 'Varandas',
            'descricao' => '[TESTE DEMO] Guarda-corpo para varanda',
            'largura' => 12.000,
            'altura' => 0,
            'quantidade' => 12.00,
            'unidade_calculo' => 'metro_linear',
            'area_m2' => 0,
            'valor_unitario' => 780.00,
            'valor_total' => 9360.00,
            'observacoes' => '[TESTE DEMO] Aguardando aprovacao do engenheiro.',
        ], false);

        $orcamentoMedicao = $this->upsert('orcamentos', ['numero' => 'ORC-DEMO-2026-0003'], [
            'numero' => 'ORC-DEMO-2026-0003',
            'cliente_id' => $clientes['arquiteta'],
            'usuario_id' => $usuarios['vendedor'],
            'data_orcamento' => $hoje,
            'validade' => date('Y-m-d', strtotime('+15 days')),
            'prazo_entrega' => 'A definir apos medicao',
            'status' => 'aguardando_medicao',
            'subtotal' => 0.00,
            'desconto' => 0.00,
            'total' => 0.00,
            'forma_pagamento' => 'A definir',
            'observacoes_cliente' => '[TESTE DEMO] Aguardando visita tecnica para espelhos.',
            'observacoes_internas' => '[TESTE DEMO] Medir sala e lavabo.',
            'ativo' => 1,
            'deleted_at' => null,
        ]);

        $this->upsert('agenda', ['titulo' => '[TESTE DEMO] Medicao projeto Clara Arquitetura', 'cliente_id' => $clientes['arquiteta']], [
            'cliente_id' => $clientes['arquiteta'],
            'pedido_id' => null,
            'usuario_id' => $usuarios['medidor'],
            'tipo' => 'medicao',
            'titulo' => '[TESTE DEMO] Medicao projeto Clara Arquitetura',
            'data_agenda' => date('Y-m-d', strtotime('+1 day')),
            'hora_inicio' => '14:00:00',
            'hora_fim' => '15:00:00',
            'responsavel' => '[TESTE DEMO] Medidor',
            'endereco' => 'Rua Projetada',
            'numero' => '45',
            'bairro' => 'Jardim Primavera',
            'cidade' => 'Sao Francisco',
            'estado' => 'MG',
            'status' => 'agendado',
            'observacoes' => '[TESTE DEMO] Levar trena laser.',
            'ativo' => 1,
            'deleted_at' => null,
        ]);
    }

    private function upsert(string $table, array $match, array $data, bool $timestamps = true): int
    {
        $existing = $this->db->table($table)->where($match)->get()->getRowArray();

        if ($timestamps && $this->temColuna($table, 'updated_at')) {
            $data['updated_at'] = $this->now;
        }

        if ($existing) {
            $this->db->table($table)->where('id', $existing['id'])->update($data);
            return (int) $existing['id'];
        }

        if ($timestamps && $this->temColuna($table, 'created_at')) {
            $data['created_at'] = $this->now;
        }

        $this->db->table($table)->insert($data);

        return (int) $this->db->insertID();
    }

    private function temColuna(string $table, string $column): bool
    {
        return in_array($column, $this->db->getFieldNames($table), true);
    }
}
