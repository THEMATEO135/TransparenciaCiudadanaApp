@extends('admin.layouts.admin')

@section('title', 'Configurar Autenticación de Dos Factores')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-shield-alt me-2"></i>
                        Configurar Autenticación de Dos Factores (2FA)
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Escanea el código QR con tu aplicación de autenticación (Google Authenticator, Authy, etc.)
                    </div>

                    <div class="text-center mb-4">
                        <img src="{{ $qrCodeUrl }}" alt="QR Code" class="img-fluid" style="max-width: 300px;">
                    </div>

                    <div class="alert alert-warning">
                        <strong>Clave secreta manual:</strong>
                        <code class="d-block mt-2">{{ $secret }}</code>
                        <small class="text-muted">Guarda esta clave en un lugar seguro por si pierdes acceso a tu aplicación</small>
                    </div>

                    <form method="POST" action="{{ route('admin.2fa.verify') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="code" class="form-label">Código de Verificación</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                   id="code" name="code" placeholder="000000" maxlength="6" required autofocus>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Ingresa el código de 6 dígitos de tu aplicación</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check me-2"></i>Verificar y Activar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
