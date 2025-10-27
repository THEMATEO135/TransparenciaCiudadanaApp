<?php

namespace App\Jobs;

use App\Models\Reporte;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReporteNotificationMail;

class SendReporteNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    protected $reporte;
    protected $tipo;
    protected $destinatario;

    /**
     * Create a new job instance.
     */
    public function __construct(Reporte $reporte, string $tipo, $destinatario = null)
    {
        $this->reporte = $reporte;
        $this->tipo = $tipo; // 'nuevo', 'asignado', 'cambio_estado', 'comentario'
        $this->destinatario = $destinatario;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $email = $this->destinatario ?? $this->reporte->correo;

        Mail::to($email)->send(
            new ReporteNotificationMail($this->reporte, $this->tipo)
        );
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Error enviando notificación de reporte', [
            'reporte_id' => $this->reporte->id,
            'tipo' => $this->tipo,
            'error' => $exception->getMessage()
        ]);
    }
}
