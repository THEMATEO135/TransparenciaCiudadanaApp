@extends('admin.layouts.admin')

@section('title', 'Ciudades y Departamentos')

@section('content')
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-center">
                <i class="fas fa-map-marker-alt me-2"></i>
                Ciudades y Banderas por Departamento
            </h2>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6 offset-md-3">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" id="searchCiudades" class="form-control"
                       placeholder="Buscar ciudad o departamento...">
            </div>
        </div>
    </div>

    <div id="lista-ciudades" class="row justify-content-center">
        <div class="col-12 text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 text-muted">Cargando ciudades...</p>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let ciudades = [];

    fetch('{{ route('api.ciudades.listar') }}')
        .then(response => {
            if (!response.ok) throw new Error('Error al obtener ciudades');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                ciudades = data.data;
                renderCiudades(ciudades);
            } else {
                showError('Error en la respuesta del servidor');
            }
        })
        .catch(error => {
            console.error('Error al obtener ciudades:', error);
            showError('Error al cargar las ciudades');
        });

    // Búsqueda en tiempo real
    document.getElementById('searchCiudades').addEventListener('input', function(e) {
        const search = e.target.value.toLowerCase();
        const filtered = ciudades.filter(ciudad =>
            ciudad.nombre.toLowerCase().includes(search) ||
            ciudad.departamento.toLowerCase().includes(search)
        );
        renderCiudades(filtered);
    });

    function renderCiudades(data) {
        const contenedor = document.getElementById('lista-ciudades');
        contenedor.innerHTML = '';

        if (data.length === 0) {
            contenedor.innerHTML = `
                <div class="col-12 text-center">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No se encontraron ciudades</p>
                </div>`;
            return;
        }

        data.forEach(ciudad => {
            const card = `
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                    <div class="card shadow-sm h-100 hover-shadow">
                        <img src="${ciudad.bandera}"
                             class="card-img-top"
                             alt="Bandera de ${ciudad.departamento}"
                             style="height:120px; object-fit:cover;">
                        <div class="card-body text-center p-2">
                            <h6 class="card-title mb-1" style="font-size: 0.9rem;">${ciudad.nombre}</h6>
                            <p class="text-muted small mb-0" style="font-size: 0.75rem;">${ciudad.departamento}</p>
                        </div>
                    </div>
                </div>`;
            contenedor.insertAdjacentHTML('beforeend', card);
        });
    }

    function showError(message) {
        const contenedor = document.getElementById('lista-ciudades');
        contenedor.innerHTML = `
            <div class="col-12 text-center">
                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <p class="text-danger">${message}</p>
            </div>`;
    }
});
</script>

<style>
.hover-shadow {
    transition: transform 0.2s, box-shadow 0.2s;
}
.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2) !important;
}
</style>
@endsection
