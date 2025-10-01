@extends('admin.layouts.admin')

@section('title', 'Editar Reporte')

@section('content')
    <div class="page-header">
        <h1 class="page-title" data-icon="✏️">Editar Reporte #{{ $reporte->id }}</h1>
        <div class="page-actions">
            <a href="{{ route('admin.reportes.index') }}" class="btn btn-secondary">
                ← Volver al Listado
            </a>
        </div>
    </div>

    <!-- Información del Ciudadano -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="stat-card">
                <span class="stat-icon">👤</span>
                <div class="stat-label">Ciudadano</div>
                <div class="stat-value" style="font-size: 1.3rem;">{{ $reporte->nombres }}</div>
                <small style="color: var(--dark-gray);">{{ $reporte->email }}</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card">
                <span class="stat-icon">📅</span>
                <div class="stat-label">Fecha de Creación</div>
                <div class="stat-value" style="font-size: 1.3rem;">
                    {{ $reporte->created_at->format('d/m/Y H:i') }}
                </div>
                <small style="color: var(--dark-gray);">Hace {{ $reporte->created_at->diffForHumans() }}</small>
            </div>
        </div>
    </div>

    <!-- Formulario de Edición -->
    <form action="{{ route('admin.reportes.update', $reporte) }}" method="POST" class="fade-in">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Columna Izquierda -->
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="form-label">
                        <span style="color: var(--primary-color);">📊</span> Estado del Reporte
                    </label>
                    <select name="estado" class="form-select" required>
                        <option value="Pendiente" {{ $reporte->estado == 'Pendiente' ? 'selected' : '' }}>
                            ⏳ Pendiente
                        </option>
                        <option value="En Proceso" {{ $reporte->estado == 'En Proceso' ? 'selected' : '' }}>
                            🔄 En Proceso
                        </option>
                        <option value="Resuelto" {{ $reporte->estado == 'Resuelto' ? 'selected' : '' }}>
                            ✅ Resuelto
                        </option>
                        <option value="Rechazado" {{ $reporte->estado == 'Rechazado' ? 'selected' : '' }}>
                            ❌ Rechazado
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <span style="color: var(--primary-color);">🏢</span> Servicio
                    </label>
                    <select name="servicio_id" class="form-select" required>
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