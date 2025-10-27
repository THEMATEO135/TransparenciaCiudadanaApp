@extends('admin.layouts.admin')

@section('title', 'Crear Plantilla')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-plus-circle"></i> Nueva Plantilla</h2>
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

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header" style="background: #ff6600; color: white;">
                    <h5 class="mb-0"><i class="fas fa-file-alt"></i> Información de la Plantilla</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.plantillas.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de la Plantilla <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                   id="nombre" name="nombre" value="{{ old('nombre') }}"
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
                                <option value="resolucion" {{ old('tipo') == 'resolucion' ? 'selected' : '' }}>
                                    🟢 Resolución - Cuando se resuelve el problema
                                </option>
                                <option value="informacion" {{ old('tipo') == 'informacion' ? 'selected' : '' }}>
                                    🔵 Información - Para solicitar datos adicionales
                                </option>
                                <option value="mantenimiento" {{ old('tipo') == 'mantenimiento' ? 'selected' : '' }}>
                                    🟡 Mantenimiento - Trabajos programados
                                </option>
                                <option value="escalado" {{ old('tipo') == 'escalado' ? 'selected' : '' }}>
                                    🔴 Escalado - Para casos complejos
                                </option>
                                <option value="otro" {{ old('tipo') == 'otro' ? 'selected' : '' }}>
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
                                   id="asunto" name="asunto" value="{{ old('asunto') }}"
                                   placeholder="Ej: Tu reporte #{reporte_id} sobre {servicio} ha sido recibido" required>
                            @error('asunto')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">El asunto que verá el ciudadano en su correo</small>
                        </div>

                        <div class="mb-3">
                            <label for="contenido" class="form-label">Contenido del Email <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('contenido') is-invalid @enderror"
                                      id="contenido" name="contenido" rows="12" required>{{ old('contenido') }}</textarea>
                            @error('contenido')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">El mensaje que recibirá el ciudadano. Usa las variables disponibles.</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="activa" name="activa" value="1"
                                       {{ old('activa', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activa">
                                    Plantilla activa (disponible para uso)
                                </label>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn text-white" style="background: #ff6600;">
                                <i class="fas fa-save"></i> Crear Plantilla
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="vistaPrevia()">
                                <i class="fas fa-eye"></i> Vista Previa
                            </button>
                            <a href="{{ route('admin.plantillas.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
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
</script>
@endsection
