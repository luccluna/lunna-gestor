<?php

namespace App\Commands;

use App\Services\PagamentoAtrasadoService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AtualizarPagamentosAtrasados extends BaseCommand
{
    protected $group = 'Pagamentos';
    protected $name = 'pagamentos:atualizar-atrasados';
    protected $description = 'Atualiza pagamentos pendentes vencidos para atrasado.';
    protected $usage = 'pagamentos:atualizar-atrasados';

    public function run(array $params)
    {
        $service = new PagamentoAtrasadoService();
        $totalAtualizado = $service->atualizar();

        CLI::write($totalAtualizado . ' pagamento(s) atualizado(s) para atrasado.', 'green');
    }
}
