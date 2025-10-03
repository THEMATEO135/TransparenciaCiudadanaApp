<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Otp;
use App\Models\Reporte;

class HistorialReportesController extends Controller
{
    /**
     * Muestra el formulario para solicitar el OTP
     */
    public function index()
    {
        return view('reportes.historial-solicitar');
    }

    /**
     * Envía el OTP al correo electrónico
     */
    public function enviarOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->email;

        // Verificar que existan reportes con ese correo
        $reportesCount = Reporte::where('correo', $email)->count();

        if ($reportesCount === 0) {
            return back()->with('error', 'No se encontraron reportes asociados a este correo electrónico.');
        }

        // Invalidar OTPs anteriores no utilizados
        Otp::where('email', $email)
            ->where('verified', false)
            ->delete();

        // Generar código OTP de 6 dígitos
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Crear el OTP con 10 minutos de validez
        $otp = Otp::create([
            'email' => $email,
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
            'verified' => false
        ]);

        // Enviar el correo usando queue
        try {
            \App\Jobs\SendOtpEmail::dispatch($email, $code);

            Log::info("OTP generado para {$email} y enviado a queue");

            return redirect()->route('reportes.historial.verificar', ['email' => $email])
                ->with('success', 'Se ha enviado un código de verificación a tu correo electrónico.');

        } catch (\Exception $e) {
            Log::error("Error al encolar envío de OTP: " . $e->getMessage());

            return back()->with('error', 'Hubo un error al procesar tu solicitud. Por favor intenta nuevamente.');
        }
    }

    /**
     * Muestra el formulario para ingresar el OTP
     */
    public function mostrarVerificacion(Request $request)
    {
        $email = $request->query('email');

        if (!$email) {
            return redirect()->route('reportes.historial');
        }

        return view('reportes.historial-verificar', compact('email'));
    }

    /**
     * Verifica el OTP y muestra los reportes
     */
    public function verificarOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6'
        ]);

        $email = $request->email;
        $code = $request->code;

        // Buscar el OTP
        $otp = Otp::where('email', $email)
            ->where('code', $code)
            ->where('verified', false)
            ->first();

        if (!$otp) {
            return back()->with('error', 'Código de verificación inválido.');
        }

        if ($otp->isExpired()) {
            return back()->with('error', 'El código ha expirado. Por favor solicita uno nuevo.');
        }

        // Marcar como verificado
        $otp->verified = true;
        $otp->save();

        // Obtener reportes del usuario
        $reportes = Reporte::where('correo', $email)
            ->with(['servicio', 'ciudad', 'proveedor'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reportes.historial-ver', compact('reportes', 'email'));
    }
}
