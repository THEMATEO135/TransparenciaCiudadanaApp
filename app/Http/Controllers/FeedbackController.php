<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReporteFeedback;
use App\Models\Reporte;
use App\Jobs\SendReporteNotificationJob;

class FeedbackController extends Controller
{
    /**
     * Mostrar formulario de feedback
     */
    public function mostrar($token)
    {
        $feedback = ReporteFeedback::where('token', $token)
            ->whereNull('respondido_at')
            ->with('reporte.servicio')
            ->firstOrFail();

        return view('reportes.feedback', compact('feedback'));
    }

    /**
     * Procesar feedback del ciudadano
     */
    public function responder(Request $request, $token)
    {
        $validated = $request->validate([
            'resuelto' => 'required|boolean',
            'calificacion' => 'required|integer|min:1|max:5',
            'nps' => 'required|integer|min:0|max:10',
            'comentario' => 'nullable|string|max:500',
        ]);

        $feedback = ReporteFeedback::where('token', $token)
            ->whereNull('respondido_at')
            ->with('reporte')
            ->firstOrFail();

        // Actualizar feedback
        $feedback->update([
            'resuelto' => $validated['resuelto'],
            'calificacion' => $validated['calificacion'],
            'nps' => $validated['nps'],
            'comentario' => $validated['comentario'] ?? null,
            'respondido_at' => now(),
        ]);

        $reporte = $feedback->reporte;

        // Si dice que NO está resuelto, reabrir el reporte
        if (!$validated['resuelto']) {
            $reporte->cambiarEstado('reabierto',
                'El ciudadano reportó que el problema NO está resuelto. Comentario: ' .
                ($validated['comentario'] ?? 'Sin comentario')
            );

            // Notificar a admins
            $admins = \App\Models\User::where('role', 'admin')->where('is_active', true)->get();
            foreach ($admins as $admin) {
                \App\Models\Notification::createFor(
                    $admin->id,
                    'reporte_reabierto',
                    'Reporte reabierto #' . $reporte->id,
                    'El ciudadano confirmó que el problema NO está resuelto.',
                    route('admin.reportes.edit', $reporte->id)
                );
            }
        } else {
            // Si confirma que SÍ está resuelto, cerrar definitivamente
            $reporte->cambiarEstado('cerrado',
                'Ciudadano confirmó resolución. Calificación: ' . $validated['calificacion'] . '/5 estrellas. NPS: ' . $validated['nps'] . '/10'
            );
        }

        // Enviar webhook a n8n con el feedback
        $payload = [
            'feedback_id' => $feedback->id,
            'reporte_id' => $reporte->id,
            'resuelto' => $validated['resuelto'],
            'calificacion' => $validated['calificacion'],
            'nps' => $validated['nps'],
            'comentario' => $validated['comentario'] ?? null,
            'respondido_at_formatted' => $feedback->respondido_at->format('Y-m-d H:i:s'),
            'reporte' => [
                'id' => $reporte->id,
                'nombres' => $reporte->nombres,
                'correo' => $reporte->correo,
                'descripcion' => $reporte->descripcion,
                'estado' => $reporte->estado,
                'prioridad' => $reporte->prioridad,
            ],
            'categoria_nps' => $validated['nps'] >= 9 ? 'promotor' : ($validated['nps'] >= 7 ? 'pasivo' : 'detractor'),
        ];
        \App\Jobs\SendReportToN8n::dispatch($payload, 'feedback');

        return view('reportes.feedback-gracias', [
            'reporte' => $reporte,
            'feedback' => $feedback
        ]);
    }

    /**
     * Ver estadísticas de feedback (Admin)
     */
    public function estadisticas()
    {
        $feedbacks = ReporteFeedback::respondido()->get();

        $stats = [
            'total' => $feedbacks->count(),
            'resueltos' => $feedbacks->where('resuelto', true)->count(),
            'no_resueltos' => $feedbacks->where('resuelto', false)->count(),
            'calificacion_promedio' => $feedbacks->avg('calificacion'),
            'nps_promedio' => $feedbacks->avg('nps'),
            'promotores' => $feedbacks->filter(fn($f) => $f->nps >= 9)->count(),
            'pasivos' => $feedbacks->filter(fn($f) => $f->nps >= 7 && $f->nps < 9)->count(),
            'detractores' => $feedbacks->filter(fn($f) => $f->nps < 7)->count(),
        ];

        // Calcular NPS Score
        $total = $stats['total'];
        $npsScore = $total > 0
            ? (($stats['promotores'] - $stats['detractores']) / $total) * 100
            : 0;

        $stats['nps_score'] = round($npsScore, 2);

        return view('admin.feedback.estadisticas', compact('stats', 'feedbacks'));
    }
}
