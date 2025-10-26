<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Crypt;

class TwoFactorController extends Controller
{
    public function enable()
    {
        $user = auth()->user();
        $google2fa = new Google2FA();

        $secret = $google2fa->generateSecretKey();
        $user->two_factor_secret = Crypt::encryptString($secret);
        $user->save();

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return view('admin.auth.2fa-setup', [
            'qrCodeUrl' => $qrCodeUrl,
            'secret' => $secret
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric'
        ]);

        $user = auth()->user();
        $google2fa = new Google2FA();
        $secret = Crypt::decryptString($user->two_factor_secret);

        $valid = $google2fa->verifyKey($secret, $request->code);

        if ($valid) {
            $user->two_factor_enabled = true;
            $user->two_factor_confirmed_at = now();
            $user->save();

            \App\Models\ActivityLog::log('2fa_enabled', 'Autenticación de dos factores activada');

            return redirect()->route('admin.dashboard')->with('success', '2FA activado exitosamente');
        }

        return back()->withErrors(['code' => 'Código inválido']);
    }

    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password'
        ]);

        $user = auth()->user();
        $user->two_factor_enabled = false;
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        \App\Models\ActivityLog::log('2fa_disabled', 'Autenticación de dos factores desactivada');

        return back()->with('success', '2FA desactivado exitosamente');
    }

    public function challenge()
    {
        return view('admin.auth.2fa-challenge');
    }

    public function verifyChallenge(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric'
        ]);

        $user = auth()->user();
        $google2fa = new Google2FA();
        $secret = Crypt::decryptString($user->two_factor_secret);

        $valid = $google2fa->verifyKey($secret, $request->code);

        if ($valid) {
            session(['2fa_verified' => true]);
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors(['code' => 'Código inválido']);
    }
}
