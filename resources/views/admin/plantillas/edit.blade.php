@extends('admin.layouts.admin')

@section('title', 'Editar Plantilla')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-edit"></i> Editar Plantilla: {{ $plantilla->nombre }}</h2>
        <a href="{{ route('admin.plantillas.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <strong><i class="fas fa-exclamation-triangle"></i> Errores de validación:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header" style="background: #ff6600; color: white;">
                    <h5 class="mb-0"><i class="fas fa-file-alt"></i> Información de la Plantilla</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.plantillas.update', $plantilla->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Esta plantilla ha sido usada <strong>{{ $plantilla->veces_usada }}</strong> veces
                        </div>

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de la Plantilla <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                   id="nombre" name="nombre" value="{{ old('nombre', $plantilla->nombre) }}"
                                   placeholder="Ej: Reporte Recibido - Energía" required>
                            @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Un nombre descriptivo para identificar la plantilla</small>
                        </div>

                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo de Plantilla <span class="text-danger">*</span></label>
                            <select class="form-select @error('tipo') is-invalid @enderror" id="tipo" name="tipo" required>
                                <option value="">Selecciona un tipo...</option>
                                <option value="resolucion" {{ old('tipo', $plantilla->tipo) == 'resolucion' ? 'selected' : '' }}>
                                    🟢 Resolución - Cuando se resuelve el problema
                                </option>
                                <option value="informacion" {{ old('tipo', $plantilla->tipo) == 'informacion' ? 'selected' : '' }}>
                                    🔵 Información - Para solicitar datos adicionales
                                </option>
                                <option value="mantenimiento" {{ old('tipo', $plantilla->tipo) == 'mantenimiento' ? 'selected' : '' }}>
                                    🟡 Mantenimiento - Trabajos programados
                                </option>
                                <option value="escalado" {{ old('tipo', $plantilla->tipo) == 'escalado' ? 'selected' : '' }}>
                                    🔴 Escalado - Para casos complejos
                                </option>
                                <option value="otro" {{ old('tipo', $plantilla->tipo) == 'otro' ? 'selected' : '' }}>
                                    ⚪ Otro - Uso general
                                </option>
                            </select>
                            @error('tipo')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="asunto" class="form-label">Asunto del Email <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('asunto') is-invalid @enderror"
                                   id="asunto" name="asunto" value="{{ old('asunto', $plantilla->asunto) }}"
                                   placeholder="Ej: Tu reporte #{reporte_id} sobre {servicio} ha sido recibido" required>
                            @error('asunto')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">El asunto que verá el ciudadano en su correo</small>
                        </div>

                        <div class="mb-3">
                            <label for="contenido" class="form-label">Contenido del Email <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('contenido') is-invalid @enderror"
                                      id="contenido" name="contenido" rows="12" required>{{ old('contenido', $plantilla->contenido) }}</textarea>
                            @error('contenido')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">El mensaje que recibirá el ciudadano. Usa las variables disponibles.</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="activa" name="activa" value="1"
                                       {{ old('activa', $plantilla->activa) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activa">
                                    Plantilla activa (disponible para uso)
                                </label>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn text-white" style="background: #ff6600;">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="vistaPrevia()">
                                <i class="fas fa-eye"></i> Vista Previa
                            </button>
                            <button type="button" class="btn btn-outline-success" onclick="verVistaConDatos()">
                                <i class="fas fa-file-invoice"></i> Vista con Datos Reales
                            </button>
                            <a href="{{ route('admin.plantillas.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Historial de Uso -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="fas fa-history"></i> Información de Uso</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h4>{{ $plantilla->veces_usada }}</h4>
                            <small class="text-muted">Veces usada</small>
                        </div>
                        <div class="col-md-4">
                            <h4>{{ $plantilla->created_at->format('d/m/Y') }}</h4>
                            <small class="text-muted">Fecha de creación</small>
                        </div>
                        <div class="col-md-4">
                            <h4>{{ $plantilla->updated_at->format('d/m/Y') }}</h4>
                            <small class="text-muted">Última modificación</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Variables Card -->
            <div class="card shadow-sm mb-3 sticky-top" style="top: 20px;">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-code"></i> Variables Disponibles</h6>
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    <small class="text-muted d-block mb-2">Haz clic en una variable para copiarla:</small>
                    @foreach($variables as $variable => $descripcion)
                    <div class="mb-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary w-100 text-start"
                                onclick="copiarVariable('{{ $variable }}')" title="{{ $descripcion }}">
                            <code>{{ $variable }}</code>
                        </button>
                        <small class="text-muted d-block ps-2">{{ $descripcion }}</small>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Tips Card -->
            <div class="card shadow-sm border-warning">
                <div class="card-header bg-warning">
                    <h6 class="mb-0"><i class="fas fa-lightbulb"></i> Tips</h6>
                </div>
                <div class="card-body">
                    <ul class="small mb-0">
                        <li>Usa un tono amable y profesional</li>
                        <li>Sé claro y conciso</li>
                        <li>Incluye información relevante</li>
                        <li>Personaliza con variables</li>
                        <li>Revisa la ortografía</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Vista Previa -->
<div class="modal fade" id="modalVista" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: #ff6600; color: white;">
                <h5 class="modal-title"><i class="fas fa-eye"></i> Vista Previa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <small><i class="fas fa-info-circle"></i> Las variables se reemplazarán con datos reales al enviar el email.</small>
                </div>
                <div class="mb-3">
                    <h6 class="text-muted">Asunto:</h6>
                    <div class="alert alert-info" id="previewAsunto"></div>
                </div>
                <div>
                    <h6 class="text-muted">Contenido:</h6>
                    <div class="border p-3 rounded" style="background: #f9f9f9; white-space: pre-wrap;" id="previewContenido"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Vista con Datos Reales -->
<div class="modal fade" id="modalVistaDatos" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-file-invoice"></i> Vista con Datos Reales</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="vistaDatosContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-success" role="status"></div>
                    <p class="mt-2">Cargando vista previa con datos reales...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function copiarVariable(variable) {
    const textarea = document.getElementById('contenido');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;

    textarea.value = text.substring(0, start) + variable + text.substring(end);
    textarea.focus();
    textarea.selectionStart = textarea.selectionEnd = start + variable.length;

    // Toast notification
    const toast = document.createElement('div');
    toast.className = 'position-fixed top-0 end-0 p-3';
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        <div class="toast show" role="alert">
            <div class="toast-header bg-success text-white">
                <i class="fas fa-check-circle me-2"></i>
                <strong class="me-auto">Copiado</strong>
                <button type="button" class="btn-close btn-close-white" onclick="this.closest('.position-fixed').remove()"></button>
            </div>
            <div class="toast-body">
                Variable ${variable} insertada
            </div>
        </div>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

function vistaPrevia() {
    const asunto = document.getElementById('asunto').value;
    const contenido = document.getElementById('contenido').value;

    if (!asunto || !contenido) {
        alert('Por favor completa el asunto y el contenido para ver la vista previa');
        return;
    }

    document.getElementById('previewAsunto').textContent = asunto;
    document.getElementById('previewContenido').textContent = contenido;

    new bootstrap.Modal(document.getElementById('modalVista')).show();
}

async function verVistaConDatos() {
    const modal = new bootstrap.Modal(document.getElementById('modalVistaDatos'));
    modal.show();

    try {
        const response = await fetch('/admin/plantillas/{{ $plantilla->id }}/preview', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await response.json();

        if (data.ok) {
            document.getElementById('vistaDatosContent').innerHTML = `
                <div class="mb-3">
                    <h6 class="text-muted">Asunto:</h6>
                    <div class="alert alert-info">${data.asunto}</div>
                </div>
                <div>
                    <h6 class="text-muted">Contenido:</h6>
                    <div class="border p-3 rounded" style="background: #f9f9f9; white-space: pre-wrap;">
                        ${data.contenido}
                    </div>
                </div>
                <div class="alert alert-success mt-3">
                    <small><i class="fas fa-info-circle"></i> Esta vista usa datos del primer reporte del sistema como ejemplo.</small>
                </div>
            `;
        } else {
            document.getElementById('vistaDatosContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> ${data.error}
                </div>
            `;
        }
    } catch (error) {
        document.getElementById('vistaDatosContent').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> Error al cargar la vista previa
            </div>
        `;
    }
}
</script>
@endsection
