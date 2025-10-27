@extends('admin.layouts.admin')

@section('title', 'Gestión de Plantillas')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-file-alt"></i> Plantillas de Respuesta</h2>
        <a href="{{ route('admin.plantillas.create') }}" class="btn text-white" style="background: #ff6600;">
            <i class="fas fa-plus"></i> Nueva Plantilla
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header" style="background: #ff6600; color: white;">
            <h5 class="mb-0"><i class="fas fa-list"></i> Todas las Plantillas ({{ $plantillas->count() }})</h5>
        </div>
        <div class="card-body">
            @if($plantillas->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 20%;">Nombre</th>
                            <th style="width: 25%;">Asunto</th>
                            <th style="width: 15%;">Tipo</th>
                            <th style="width: 10%;">Estado</th>
                            <th style="width: 10%;">Usos</th>
                            <th style="width: 15%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plantillas as $plantilla)
                        <tr>
                            <td><strong>#{{ $plantilla->id }}</strong></td>
                            <td>{{ $plantilla->nombre }}</td>
                            <td>
                                <small class="text-muted">{{ Str::limit($plantilla->asunto, 40) }}</small>
                            </td>
                            <td>
                                @php
                                    $tipoColors = [
                                        'resolucion' => 'success',
                                        'informacion' => 'info',
                                        'mantenimiento' => 'warning',
                                        'escalado' => 'danger',
                                        'otro' => 'secondary'
                                    ];
                                    $tipoIconos = [
                                        'resolucion' => 'fa-check-circle',
                                        'informacion' => 'fa-info-circle',
                                        'mantenimiento' => 'fa-tools',
                                        'escalado' => 'fa-exclamation-triangle',
                                        'otro' => 'fa-file'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $tipoColors[$plantilla->tipo] ?? 'secondary' }}">
                                    <i class="fas {{ $tipoIconos[$plantilla->tipo] ?? 'fa-file' }}"></i>
                                    {{ ucfirst($plantilla->tipo) }}
                                </span>
                            </td>
                            <td>
                                @if($plantilla->activa)
                                <span class="badge bg-success">
                                    <i class="fas fa-check"></i> Activa
                                </span>
                                @else
                                <span class="badge bg-secondary">
                                    <i class="fas fa-times"></i> Inactiva
                                </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $plantilla->veces_usada }} veces
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="verVista({{ $plantilla->id }})" title="Vista previa">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="{{ route('admin.plantillas.edit', $plantilla->id) }}" class="btn btn-outline-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-outline-danger" onclick="eliminar({{ $plantilla->id }})" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-center text-muted py-5">
                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                No hay plantillas creadas. <a href="{{ route('admin.plantillas.create') }}">Crea la primera plantilla</a>
            </p>
            @endif
        </div>
    </div>

    <!-- Info Card -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Variables Disponibles</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2">Puedes usar estas variables en tus plantillas (asunto y contenido):</p>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li><code>{nombre_ciudadano}</code> - Nombre del ciudadano</li>
                                <li><code>{reporte_id}</code> - ID del reporte</li>
                                <li><code>{servicio}</code> - Nombre del servicio</li>
                                <li><code>{ciudad}</code> - Nombre de la ciudad</li>
                                <li><code>{barrio}</code> - Barrio o localidad</li>
                                <li><code>{descripcion}</code> - Descripción del problema</li>
                                <li><code>{proveedor}</code> - Nombre del proveedor</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li><code>{estado}</code> - Estado del reporte</li>
                                <li><code>{prioridad}</code> - Prioridad del reporte</li>
                                <li><code>{fecha_reporte}</code> - Fecha del reporte</li>
                                <li><code>{fecha_asignacion}</code> - Fecha de asignación</li>
                                <li><code>{operador}</code> - Nombre del operador asignado</li>
                                <li><code>{timeline_url}</code> - URL del timeline</li>
                                <li><code>{correo}</code> - Correo del ciudadano</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Vista Previa -->
<div class="modal fade" id="modalVista" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: #ff6600; color: white;">
                <h5 class="modal-title"><i class="fas fa-eye"></i> Vista Previa de Plantilla</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="vistaContent">
                    <div class="text-center py-5">
                        <div class="spinner-border" style="color: #ff6600;" role="status"></div>
                        <p class="mt-2">Cargando vista previa...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Eliminación -->
<div class="modal fade" id="modalEliminar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar esta plantilla?</p>
                <p class="text-danger"><strong>Esta acción no se puede deshacer.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formEliminar" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
async function verVista(id) {
    const modal = new bootstrap.Modal(document.getElementById('modalVista'));
    modal.show();

    try {
        const response = await fetch(`/admin/plantillas/${id}/preview`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await response.json();

        if (data.ok) {
            document.getElementById('vistaContent').innerHTML = `
                <div class="mb-3">
                    <h6 class="text-muted">Asunto:</h6>
                    <div class="alert alert-info">${data.asunto}</div>
                </div>
                <div>
                    <h6 class="text-muted">Contenido:</h6>
                    <div class="border p-3 rounded" style="background: #f9f9f9;">
                        ${data.contenido.replace(/\n/g, '<br>')}
                    </div>
                </div>
                <div class="alert alert-warning mt-3">
                    <small><i class="fas fa-info-circle"></i> Esta es una vista previa con datos de ejemplo del primer reporte del sistema.</small>
                </div>
            `;
        } else {
            document.getElementById('vistaContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> ${data.error}
                </div>
            `;
        }
    } catch (error) {
        document.getElementById('vistaContent').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> Error al cargar la vista previa
            </div>
        `;
    }
}

function eliminar(id) {
    const form = document.getElementById('formEliminar');
    form.action = `/admin/plantillas/${id}`;
    new bootstrap.Modal(document.getElementById('modalEliminar')).show();
}
</script>
@endsection
