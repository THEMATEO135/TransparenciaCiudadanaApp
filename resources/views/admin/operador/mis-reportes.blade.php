@extends('admin.layouts.admin')

@section('title', 'Mis Reportes')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-clipboard-list"></i> Mis Reportes Asignados</h2>
        <a href="{{ route('admin.operador.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Dashboard
        </a>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.operador.misReportes') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="">Todos</option>
                            @foreach($estados as $estado)
                                <option value="{{ $estado->nombre }}" {{ request('estado') == $estado->nombre ? 'selected' : '' }}>
                                    {{ $estado->icono }} {{ $estado->etiqueta }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Prioridad</label>
                        <select name="prioridad" class="form-select">
                            <option value="">Todas</option>
                            <option value="critica" {{ request('prioridad') == 'critica' ? 'selected' : '' }}>Crítica</option>
                            <option value="alta" {{ request('prioridad') == 'alta' ? 'selected' : '' }}>Alta</option>
                            <option value="media" {{ request('prioridad') == 'media' ? 'selected' : '' }}>Media</option>
                            <option value="baja" {{ request('prioridad') == 'baja' ? 'selected' : '' }}>Baja</option>
                        </select>
                    </div>

                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <a href="{{ route('admin.operador.misReportes') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Reportes -->
    <div class="card shadow-sm">
        <div class="card-header" style="background: #ff6600; color: white;">
            <h5 class="mb-0">
                <i class="fas fa-list"></i>
                Reportes ({{ $reportes->total() }})
            </h5>
        </div>
        <div class="card-body">
            @if($reportes->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Servicio</th>
                            <th>Ciudadano</th>
                            <th>Ubicación</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                            <th>Fecha Asignación</th>
                            <th>Deadline</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportes as $reporte)
                        <tr class="{{ $reporte->estaVencido() ? 'table-danger' : '' }}">
                            <td><strong>#{{ $reporte->id }}</strong></td>
                            <td>
                                <i class="fas {{ $reporte->servicio->icono ?? 'fa-wrench' }}"></i>
                                {{ $reporte->servicio->nombre ?? 'N/A' }}
                            </td>
                            <td>
                                {{ $reporte->nombres }}
                                <br>
                                <small class="text-muted">{{ $reporte->correo }}</small>
                            </td>
                            <td>
                                {{ $reporte->barrio ?? $reporte->localidad ?? 'N/A' }}
                                <br>
                                <small class="text-muted">{{ $reporte->ciudad->nombre ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $reporte->color_prioridad }}">
                                    <i class="fas fa-flag"></i> {{ ucfirst($reporte->prioridad) }}
                                </span>
                            </td>
                            <td>
                                @if($reporte->estado)
                                    <span class="badge" style="background-color: {{ $reporte->estado->color }}; color: white;">
                                        {{ $reporte->estado->icono }} {{ $reporte->estado->etiqueta }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Sin estado</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $reporte->assigned_at ? $reporte->assigned_at->format('d/m/Y H:i') : 'N/A' }}</small>
                            </td>
                            <td>
                                @if($reporte->deadline)
                                <small class="{{ $reporte->estaVencido() ? 'text-danger fw-bold' : '' }}">
                                    {{ $reporte->deadline->format('d/m/Y H:i') }}
                                    @if($reporte->estaVencido())
                                    <br><span class="badge bg-danger">VENCIDO</span>
                                    @endif
                                </small>
                                @else
                                <small class="text-muted">Sin deadline</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.reportes.edit', $reporte->id) }}"
                                       class="btn btn-primary"
                                       title="Gestionar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('reportes.timeline', $reporte->id) }}"
                                       class="btn btn-info"
                                       title="Timeline"
                                       target="_blank">
                                        <i class="fas fa-history"></i>
                                    </a>
                                </div>

                                @if($reporte->estado && $reporte->estado->nombre === 'asignado')
                                <button class="btn btn-success btn-sm mt-1 w-100"
                                        onclick="aceptarReporte({{ $reporte->id }})">
                                    <i class="fas fa-check"></i> Aceptar
                                </button>
                                @endif

                                @if($reporte->estado && in_array($reporte->estado->nombre, ['en_proceso', 'en_revision']))
                                <button class="btn btn-warning btn-sm mt-1 w-100"
                                        onclick="requiereInformacion({{ $reporte->id }})">
                                    <i class="fas fa-question-circle"></i> Requiere Info
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-3">
                {{ $reportes->links() }}
            </div>
            @else
            <p class="text-center text-muted py-5">
                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                No tienes reportes asignados con estos filtros
            </p>
            @endif
        </div>
    </div>
</div>

<!-- Modal: Requiere Información -->
<div class="modal fade" id="modalRequiereInfo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-question-circle"></i> Solicitar Información</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="reporteIdInfo">
                <div class="mb-3">
                    <label class="form-label">¿Qué información necesitas del ciudadano?</label>
                    <textarea id="comentarioInfo" class="form-control" rows="4"
                              placeholder="Ejemplo: Necesitamos que nos indiques el número de medidor o una foto más cercana del problema..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" onclick="enviarRequiereInfo()">
                    <i class="fas fa-paper-plane"></i> Enviar Solicitud
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
async function aceptarReporte(id) {
    if (!confirm('¿Deseas aceptar este reporte y comenzar a trabajar en él?')) {
        return;
    }

    try {
        const response = await fetch(`/admin/operador/reportes/${id}/aceptar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (data.ok) {
            alert('Reporte aceptado exitosamente');
            window.location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    } catch (error) {
        alert('Error de conexión');
    }
}

function requiereInformacion(id) {
    document.getElementById('reporteIdInfo').value = id;
    document.getElementById('comentarioInfo').value = '';
    new bootstrap.Modal(document.getElementById('modalRequiereInfo')).show();
}

async function enviarRequiereInfo() {
    const id = document.getElementById('reporteIdInfo').value;
    const comentario = document.getElementById('comentarioInfo').value;

    if (!comentario.trim()) {
        alert('Por favor escribe qué información necesitas');
        return;
    }

    try {
        const response = await fetch(`/admin/operador/reportes/${id}/requiere-informacion`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ comentario })
        });

        const data = await response.json();

        if (data.ok) {
            alert('Solicitud enviada al ciudadano');
            bootstrap.Modal.getInstance(document.getElementById('modalRequiereInfo')).hide();
            window.location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    } catch (error) {
        alert('Error de conexión');
    }
}
</script>
@endsection
