<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class LimparDadosDemonstracao extends BaseCommand
{
    protected $group = 'Demo';
    protected $name = 'demo:limpar-dados';
    protected $description = 'Mostra ou remove a massa de dados ficticia de demonstracao.';
    protected $usage = 'demo:limpar-dados [--executar]';
    protected $options = [
        '--executar' => 'Remove definitivamente os dados de demonstracao depois da previa.',
    ];

    private $db;

    public function run(array $params)
    {
        $this->db = Database::connect();
        $executar = $this->opcaoInformada('executar');

        $ids = $this->mapearIdsDemo();
        $alvos = $this->montarAlvos($ids);
        $contagens = $this->contarAlvos($alvos);

        CLI::write($executar ? 'Limpeza de dados de demonstracao' : 'Previa da limpeza de dados de demonstracao', 'yellow');
        CLI::newLine();

        foreach ($contagens as $descricao => $total) {
            CLI::write(str_pad($descricao, 38) . $total);
        }

        $totalRegistros = array_sum($contagens);
        CLI::newLine();
        CLI::write('Total de registros encontrados: ' . $totalRegistros, $totalRegistros > 0 ? 'yellow' : 'green');

        if (!$executar) {
            CLI::newLine();
            CLI::write('Nenhum dado foi apagado.', 'green');
            CLI::write('Faça backup do banco antes da limpeza definitiva.', 'yellow');
            CLI::write('Depois execute: php spark demo:limpar-dados --executar', 'yellow');
            return;
        }

        if ($totalRegistros === 0) {
            CLI::write('Nao ha dados de demonstracao para remover.', 'green');
            return;
        }

        if (!$this->existeAdministradorReal()) {
            CLI::error('Limpeza bloqueada: crie ao menos um administrador real ativo antes de remover os usuarios de teste.');
            return;
        }

        $this->db->transBegin();

        try {
            foreach ($alvos as $alvo) {
                $this->apagarAlvo($alvo['tabela'], $alvo['aplicar']);
            }

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('O banco recusou uma ou mais exclusoes.');
            }

            $this->db->transCommit();
        } catch (\Throwable $exception) {
            $this->db->transRollback();
            CLI::error('Erro ao limpar dados de demonstracao: ' . $exception->getMessage());
            return;
        }

        CLI::write('Dados de demonstracao removidos com sucesso.', 'green');
    }

    private function mapearIdsDemo(): array
    {
        $usuarios = $this->buscarIds('usuarios', static function ($builder) {
            $builder
                ->groupStart()
                    ->like('nome', '[TESTE DEMO]', 'after')
                    ->orLike('email', '@teste.com', 'before')
                ->groupEnd();
        });

        $clientes = $this->buscarIds('clientes', static function ($builder) {
            $builder
                ->groupStart()
                    ->like('nome', '[TESTE DEMO]', 'after')
                    ->orLike('email', '@teste.com', 'before')
                ->groupEnd();
        });

        $categorias = $this->buscarIds('categorias_servicos', static function ($builder) {
            $builder
                ->groupStart()
                    ->like('nome', '[TESTE DEMO]', 'after')
                    ->orLike('descricao', 'ficticios para demonstracao')
                ->groupEnd();
        });

        $produtos = $this->buscarIds('produtos_servicos', function ($builder) use ($categorias) {
            $builder
                ->groupStart()
                    ->like('nome', '[TESTE DEMO]', 'after')
                    ->orLike('observacoes', '[TESTE DEMO]', 'after');

            $this->orWhereIn($builder, 'categoria_id', $categorias);

            $builder->groupEnd();
        });

        $materiais = $this->buscarIds('estoque_materiais', function ($builder) use ($produtos) {
            $builder
                ->groupStart()
                    ->like('nome', '[TESTE DEMO]', 'after')
                    ->orLike('fornecedor', '[TESTE DEMO]', 'after')
                    ->orLike('observacoes', '[TESTE DEMO]', 'after');

            $this->orWhereIn($builder, 'produto_servico_id', $produtos);

            $builder->groupEnd();
        });

        $orcamentos = $this->buscarIds('orcamentos', function ($builder) use ($clientes) {
            $builder
                ->groupStart()
                    ->like('numero', 'ORC-DEMO-', 'after')
                    ->orLike('observacoes_cliente', '[TESTE DEMO]', 'after')
                    ->orLike('observacoes_internas', '[TESTE DEMO]', 'after');

            $this->orWhereIn($builder, 'cliente_id', $clientes);

            $builder->groupEnd();
        });

        $pedidos = $this->buscarIds('pedidos', function ($builder) use ($clientes, $orcamentos) {
            $builder
                ->groupStart()
                    ->like('numero', 'PED-DEMO-', 'after')
                    ->orLike('observacoes', '[TESTE DEMO]', 'after');

            $this->orWhereIn($builder, 'cliente_id', $clientes);
            $this->orWhereIn($builder, 'orcamento_id', $orcamentos);

            $builder->groupEnd();
        });

        return [
            'usuarios' => $usuarios,
            'clientes' => $clientes,
            'categorias' => $categorias,
            'produtos' => $produtos,
            'materiais' => $materiais,
            'orcamentos' => $orcamentos,
            'pedidos' => $pedidos,
        ];
    }

    private function montarAlvos(array $ids): array
    {
        return [
            [
                'descricao' => 'Movimentacoes de estoque',
                'tabela' => 'estoque_movimentacoes',
                'aplicar' => function ($builder) use ($ids) {
                    $builder
                        ->groupStart()
                            ->like('documento', '[TESTE DEMO]', 'after')
                            ->orLike('observacoes', '[TESTE DEMO]', 'after');

                    $this->orWhereIn($builder, 'material_id', $ids['materiais']);
                    $this->orWhereIn($builder, 'pedido_id', $ids['pedidos']);

                    $builder->groupEnd();
                },
            ],
            [
                'descricao' => 'Pagamentos',
                'tabela' => 'pagamentos',
                'aplicar' => function ($builder) use ($ids) {
                    $builder
                        ->groupStart()
                            ->like('descricao', '[TESTE DEMO]', 'after')
                            ->orLike('observacoes', '[TESTE DEMO]', 'after');

                    $this->orWhereIn($builder, 'pedido_id', $ids['pedidos']);
                    $this->orWhereIn($builder, 'cliente_id', $ids['clientes']);

                    $builder->groupEnd();
                },
            ],
            [
                'descricao' => 'Agenda',
                'tabela' => 'agenda',
                'aplicar' => function ($builder) use ($ids) {
                    $builder
                        ->groupStart()
                            ->like('titulo', '[TESTE DEMO]', 'after')
                            ->orLike('responsavel', '[TESTE DEMO]', 'after')
                            ->orLike('observacoes', '[TESTE DEMO]', 'after');

                    $this->orWhereIn($builder, 'pedido_id', $ids['pedidos']);
                    $this->orWhereIn($builder, 'cliente_id', $ids['clientes']);

                    $builder->groupEnd();
                },
            ],
            [
                'descricao' => 'Historico de pedidos',
                'tabela' => 'pedido_status_historico',
                'aplicar' => function ($builder) use ($ids) {
                    $builder
                        ->groupStart()
                            ->like('observacao', '[TESTE DEMO]', 'after');

                    $this->orWhereIn($builder, 'pedido_id', $ids['pedidos']);

                    $builder->groupEnd();
                },
            ],
            [
                'descricao' => 'Pedidos',
                'tabela' => 'pedidos',
                'aplicar' => fn ($builder) => $this->whereInOuVazio($builder, 'id', $ids['pedidos']),
            ],
            [
                'descricao' => 'Itens de orcamento',
                'tabela' => 'orcamento_itens',
                'aplicar' => function ($builder) use ($ids) {
                    $builder
                        ->groupStart()
                            ->like('descricao', '[TESTE DEMO]', 'after')
                            ->orLike('observacoes', '[TESTE DEMO]', 'after');

                    $this->orWhereIn($builder, 'orcamento_id', $ids['orcamentos']);

                    $builder->groupEnd();
                },
            ],
            [
                'descricao' => 'Orcamentos',
                'tabela' => 'orcamentos',
                'aplicar' => fn ($builder) => $this->whereInOuVazio($builder, 'id', $ids['orcamentos']),
            ],
            [
                'descricao' => 'Materiais de estoque',
                'tabela' => 'estoque_materiais',
                'aplicar' => fn ($builder) => $this->whereInOuVazio($builder, 'id', $ids['materiais']),
            ],
            [
                'descricao' => 'Produtos e servicos',
                'tabela' => 'produtos_servicos',
                'aplicar' => fn ($builder) => $this->whereInOuVazio($builder, 'id', $ids['produtos']),
            ],
            [
                'descricao' => 'Categorias',
                'tabela' => 'categorias_servicos',
                'aplicar' => fn ($builder) => $this->whereInOuVazio($builder, 'id', $ids['categorias']),
            ],
            [
                'descricao' => 'Clientes',
                'tabela' => 'clientes',
                'aplicar' => fn ($builder) => $this->whereInOuVazio($builder, 'id', $ids['clientes']),
            ],
            [
                'descricao' => 'Usuarios de teste',
                'tabela' => 'usuarios',
                'aplicar' => fn ($builder) => $this->whereInOuVazio($builder, 'id', $ids['usuarios']),
            ],
        ];
    }

    private function contarAlvos(array $alvos): array
    {
        $contagens = [];

        foreach ($alvos as $alvo) {
            $builder = $this->db->table($alvo['tabela']);
            $alvo['aplicar']($builder);
            $contagens[$alvo['descricao']] = $builder->countAllResults();
        }

        return $contagens;
    }

    private function apagarAlvo(string $tabela, callable $aplicar): void
    {
        $builder = $this->db->table($tabela);
        $aplicar($builder);
        $builder->delete();
    }

    private function buscarIds(string $tabela, callable $aplicar): array
    {
        $builder = $this->db->table($tabela)->select('id');
        $aplicar($builder);

        return array_map('intval', array_column($builder->get()->getResultArray(), 'id'));
    }

    private function existeAdministradorReal(): bool
    {
        return $this->db->table('usuarios')
            ->where('perfil', 'administrador')
            ->where('ativo', 1)
            ->where('deleted_at', null)
            ->notLike('nome', '[TESTE', 'after')
            ->notLike('email', '@teste.com', 'before')
            ->countAllResults() > 0;
    }

    private function whereInOuVazio($builder, string $campo, array $ids): void
    {
        if (empty($ids)) {
            $builder->where('1 = 0', null, false);
            return;
        }

        $builder->whereIn($campo, $ids);
    }

    private function orWhereIn($builder, string $campo, array $ids): void
    {
        if (!empty($ids)) {
            $builder->orWhereIn($campo, $ids);
        }
    }

    private function opcaoInformada(string $opcao): bool
    {
        return CLI::getOption($opcao) !== null;
    }
}
