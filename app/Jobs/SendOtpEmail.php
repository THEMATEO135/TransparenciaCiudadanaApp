<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOtpEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número de intentos antes de fallar
     */
    public $tries = 3;

    /**
     * Timeout del job en segundos
     */
    public $timeout = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $email,
        protected string $code
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::send('emails.otp', ['code' => $this->code], function ($message) {
            $message->to($this->email)
                ->subject('Código de Verificación - Transparencia Ciudadana');
        });
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Error enviando email OTP', [
            'email' => $this->email,
            'error' => $exception->getMessage()
        ]);
    }
}
