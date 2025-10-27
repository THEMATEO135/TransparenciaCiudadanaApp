<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reporte;
use App\Jobs\DetectDuplicatesJob;

class DetectarDuplicadosCommand extends Command
{
    protected $signature = 'reportes:detectar-duplicados {--dias=1}';
    protected $description = 'Detecta y agrupa reportes duplicados';

    public function handle()
    {
        $dias = $this->option('dias');

        $this->info("Buscando duplicados en reportes de los últimos {$dias} días...");

        $reportes = Reporte::where('created_at', '>=', now()->subDays($dias))
            ->whereNull('parent_id')
            ->get();

        $this->info("Reportes a analizar: {$reportes->count()}");

        $bar = $this->output->createProgressBar($reportes->count());
        $bar->start();

        $duplicadosEncontrados = 0;

        foreach ($reportes as $reporte) {
            $similar = $reporte->detectarDuplicados();

            if ($similar) {
                $reporte->marcarComoDuplicado($similar->id);
                $duplicadosEncontrados++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("✅ Duplicados encontrados y agrupados: {$duplicadosEncontrados}");

        return Command::SUCCESS;
    }
}
