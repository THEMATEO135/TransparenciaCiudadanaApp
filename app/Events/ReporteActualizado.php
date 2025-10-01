<?php

namespace App\Events;

use App\Models\Reporte;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReporteActualizado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reporte;

    public function __construct(Reporte $reporte)
    {
        $this->reporte = $reporte->load('servicio');
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('reportes'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->reporte->id,
            'servicio' => $this->reporte->servicio->nombre ?? 'N/A',
            'estado' => $this->reporte->estado,
            'updated_at' => $this->reporte->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
