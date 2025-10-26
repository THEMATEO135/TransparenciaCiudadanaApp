@extends('layouts.app') {{-- o usa la plantilla que tu proyecto tenga, por ejemplo: admin.layouts.admin --}}

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-4">🌎 Ciudades y Banderas por Departamento</h2>

    <div id="lista-ciudades" class="row justify-content-center">
        <!-- Aquí se insertarán las tarjetas dinámicamente -->
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('{{ route('api.ciudades.listar') }}')
        .then(response => {
            if (!response.ok) throw new Error('Error al obtener ciudades');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const contenedor = document.getElementById('lista-ciudades');
                contenedor.innerHTML = ''; // Limpiar antes de insertar

                data.data.forEach(ciudad => {
                    const card = `
                        <div class="col-md-3 col-sm-6 mb-4">
                            <div class="card shadow-sm h-100">
                                <img src="${ciudad.bandera}" 
                                     class="card-img-top" 
                                     alt="Bandera de ${ciudad.departamento}"
                                     style="height:100px; object-fit:cover;">
                                <div class="card-body text-center">
                                    <h5 class="card-title mb-1">${ciudad.nombre}</h5>
                                    <p class="text-muted small">${ciudad.departamento}</p>
                                </div>
                            </div>
                        </div>`;
                    contenedor.insertAdjacentHTML('beforeend', card);
                });
            } else {
                console.error('Error en la respuesta del servidor:', data.message);
            }
        })
        .catch(error => {
            console.error('Error al obtener ciudades:', error);
        });
});
</script>
@endsection
