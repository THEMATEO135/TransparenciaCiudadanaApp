<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Prediccion;

class VerificarPrediccionesCommand extends Command
{
    protected $signature = 'predicciones:verificar-cumplimiento';
    protected $description = 'Verifica si las predicciones pasadas se cumplieron';

    public function handle()
    {
        $this->info('Verificando predicciones pasadas...');

        $predicciones = Prediccion::pasadas()
            ->whereNull('se_cumplio')
            ->get();

        if ($predicciones->isEmpty()) {
            $this->comment('No hay predicciones para verificar.');
            return Command::SUCCESS;
        }

        $this->info("Predicciones a verificar: {$predicciones->count()}");

        $cumplidas = 0;
        $noCumplidas = 0;

        foreach ($predicciones as $prediccion) {
            $seCumplio = $prediccion->verificarCumplimiento();

            if ($seCumplio) {
                $cumplidas++;
                $this->line("✓ Predicción #{$prediccion->id} se cumplió");
            } else {
                $noCumplidas++;
                $this->line("✗ Predicción #{$prediccion->id} no se cumplió");
            }
        }

        $this->newLine();
        $this->info("Cumplidas: {$cumplidas}");
        $this->info("No cumplidas: {$noCumplidas}");

        $precision = $cumplidas > 0 ? round(($cumplidas / ($cumplidas + $noCumplidas)) * 100, 2) : 0;
        $this->info("Precisión del modelo: {$precision}%");

        return Command::SUCCESS;
    }
}
