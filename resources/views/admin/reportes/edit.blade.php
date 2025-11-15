@extends('admin.layouts.admin')

@section('title', 'Editar Reporte')

@section('content')
    <header class="page-header">
        <h1 class="page-title" data-icon="✏️">Editar Reporte #{{ $reporte->id }}</h1>
        <div class="page-actions">
            <a href="{{ route('admin.reportes.index') }}" class="btn btn-secondary" aria-label="Volver a la lista de reportes">
                ← Volver al Listado
            </a>
        </div>
    </header>

    <!-- Información del Ciudadano -->
    <section class="row mb-4" aria-label="Información del reporte">
        <div class="col-md-6">
            <div class="stat-card" role="article" aria-labelledby="info-ciudadano">
                <span class="stat-icon" aria-hidden="true">👤</span>
                <div class="stat-label" id="info-ciudadano">Ciudadano</div>
                <div class="stat-value" style="font-size: 1.3rem;">{{ $reporte->nombres }}</div>
                <small style="color: var(--dark-gray);">{{ $reporte->email }}</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card" role="article" aria-labelledby="info-fecha">
                <span class="stat-icon" aria-hidden="true">📅</span>
                <div class="stat-label" id="info-fecha">Fecha de Creación</div>
                <div class="stat-value" style="font-size: 1.3rem;">
                    {{ $reporte->created_at->format('d/m/Y H:i') }}
                </div>
                <small style="color: var(--dark-gray);">Hace {{ $reporte->created_at->diffForHumans() }}</small>
            </div>
        </div>
    </section>

    <!-- Formulario de Edición -->
    <form action="{{ route('admin.reportes.update', $reporte) }}" method="POST" class="fade-in" aria-label="Formulario de edición de reporte">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Columna Izquierda -->
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="estado_id" class="form-label">
                        <span style="color: var(--primary-color);" aria-hidden="true">📊</span> Estado del Reporte
                    </label>
                    <select name="estado_id" id="estado_id" class="form-select" required aria-required="true">
                        @foreach($estados as $estado)
                            <option value="{{ $estado->id }}"
                                    {{ $reporte->estado_id == $estado->id ? 'selected' : '' }}>
                                {{ $estado->icono }} {{ $estado->etiqueta }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="servicio_id" class="form-label">
                        <span style="color: var(--primary-color);" aria-hidden="true">🏢</span> Servicio
                    </label>
                    <select name="servicio_id" id="servicio_id" class="form-select" required aria-required="true">
                        @foreach($servicios as $servicio)
                            <option value="{{ $servicio->id }}"
                                    {{ $reporte->servicio_id == $servicio->id ? 'selected' : '' }}>
                                {{ $servicio->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if($reporte->telefono)
                    <div class="form-group">
                        <label class="form-label">
                            <span style="color: var(--primary-color);">📞</span> Teléfono
                        </label>
                        <input type="text" class="form-control" value="{{ $reporte->telefono }}" readonly>
                    </div>
                @endif

                @if($reporte->direccion)
                    <div class="form-group">
                        <label class="form-label">
                            <span style="color: var(--primary-color);">📍</span> Dirección
                        </label>
                        <input type="text" class="form-control" value="{{ $reporte->direccion }}" readonly>
                    </div>
                @endif
            </div>

            <!-- Columna Derecha -->
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="form-label">
                        <span style="color: var(--primary-color);">📝</span> Descripción del Problema
                    </label>
                    <textarea name="descripcion"
                              class="form-control"
                              rows="8"
                              required>{{ $reporte->descripcion }}</textarea>
                    <small style="color: var(--dark-gray);">
                        Descripción detallada del problema reportado
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <span style="color: var(--primary-color);">💬</span> Notas del Administrador (Opcional)
                    </label>
                    <textarea name="notas_admin"
                              class="form-control"
                              rows="4"
                              placeholder="Agrega notas internas sobre este reporte...">{{ $reporte->notas_admin ?? '' }}</textarea>
                    <small style="color: var(--dark-gray);">
                        Estas notas son internas y no serán visibles para el ciudadano
                    </small>
                </div>
            </div>
        </div>

        <!-- Coordenadas si existen -->
        @if($reporte->latitud && $reporte->longitud)
            <div class="alert alert-info mt-3">
                <strong>📍 Ubicación del Reporte:</strong><br>
                Latitud: {{ $reporte->latitud }} | Longitud: {{ $reporte->longitud }}
                <a href="https://www.google.com/maps?q={{ $reporte->latitud }},{{ $reporte->longitud }}"
                   target="_blank"
                   class="btn btn-sm btn-info ms-2">
                    Ver en Google Maps
                </a>
            </div>
        @endif

        <!-- Botones de Acción -->
        <div class="d-flex gap-3 justify-content-end mt-4">
            <a href="{{ route('admin.reportes.index') }}" class="btn btn-secondary">
                ❌ Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
                💾 Guardar Cambios
            </button>
        </div>
    </form>
@endsection