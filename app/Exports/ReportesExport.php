<?php

namespace App\Exports;

use App\Models\Reporte;
use Illuminate\Contracts\View\View;

class ReportesExport
{
    protected $reportes;

    public function __construct($reportes)
    {
        $this->reportes = $reportes;
    }

    public function view(): View
    {
        return view('admin.exports.reportes', [
            'reportes' => $this->reportes
        ]);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Email',
            'Teléfono',
            'Servicio',
            'Descripción',
            'Dirección',
            'Estado',
            'Fecha'
        ];
    }

    public function map($reporte): array
    {
        return [
            $reporte->id,
            $reporte->nombres,
            $reporte->correo,
            $reporte->telefono,
            $reporte->servicio->nombre ?? 'N/A',
            $reporte->descripcion,
            $reporte->direccion,
            $reporte->estado,
            $reporte->created_at->format('Y-m-d H:i:s')
        ];
    }
}
