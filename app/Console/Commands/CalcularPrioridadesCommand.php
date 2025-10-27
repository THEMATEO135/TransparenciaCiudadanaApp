<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PriorityCalculationService;

class CalcularPrioridadesCommand extends Command
{
    protected $signature = 'reportes:calcular-prioridades';
    protected $description = 'Recalcula las prioridades de todos los reportes abiertos';

    public function handle(PriorityCalculationService $service)
    {
        $this->info('Iniciando recálculo de prioridades...');

        $count = $service->recalculateAll();

        $this->info("✅ Prioridades recalculadas: {$count} reportes actualizados.");

        return Command::SUCCESS;
    }
}
