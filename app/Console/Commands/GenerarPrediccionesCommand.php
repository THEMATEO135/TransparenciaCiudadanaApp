<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ProcessPredictionsJob;

class GenerarPrediccionesCommand extends Command
{
    protected $signature = 'predicciones:generar {--horas=72}';
    protected $description = 'Genera predicciones de problemas usando Machine Learning';

    public function handle()
    {
        $horas = $this->option('horas');

        $this->info("Generando predicciones para las próximas {$horas} horas...");

        ProcessPredictionsJob::dispatch();

        $this->info('✅ Job de predicciones despachado a la cola.');
        $this->comment('Usa "php artisan queue:work" para procesar.');

        return Command::SUCCESS;
    }
}
