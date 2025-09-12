<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendReportToN8n implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reportData;

    public function __construct($reportData)
    {
        $this->reportData = $reportData;
    }

    public function handle()
    {
        try {
            Http::timeout(10)
                ->post('https://n8n-webhook-url.com', $this->reportData);
        } catch (\Exception $e) {
            Log::error('Job: Error enviando webhook a n8n: ' . $e->getMessage());
            // Este job se reintentará automáticamente (configurable)
            throw $e; // Para que Laravel lo reintente
        }
    }
}