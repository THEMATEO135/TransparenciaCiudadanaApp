<?php

namespace App\Jobs;

use App\Models\Reporte;
use App\Models\ReporteFeedback;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\FeedbackRequestMail;

class SendFeedbackRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    protected $reporte;
    protected $feedback;

    /**
     * Create a new job instance.
     */
    public function __construct(Reporte $reporte, ReporteFeedback $feedback)
    {
        $this->reporte = $reporte;
        $this->feedback = $feedback;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->reporte->correo)->send(
            new FeedbackRequestMail($this->reporte, $this->feedback)
        );
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Error enviando feedback request', [
            'reporte_id' => $this->reporte->id,
            'error' => $exception->getMessage()
        ]);
    }
}
