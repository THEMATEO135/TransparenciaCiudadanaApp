@extends('admin.layouts.admin')


@section('head')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map { height: 500px; border-radius: 8px; }
    </style>
@endsection

@section('content')
    <h3>🗺️ Mapa de reportes - Transparencia Ciudadana</h3>
    <div id="map"></div>
@endsection

@section('scripts')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Inicializar el mapa en Colombia
        var map = L.map('map').setView([4.5709, -74.2973], 6);

        // Capa base de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // 🔹 Datos de prueba (no vienen de la BD todavía)
var reportes = [
    {
        titulo: "Corte de energía",
        descripcion: "Falla reportada en el suministro eléctrico",
        ubicacion: "Bogotá",
        lat: 4.6097,
        lng: -74.0817
    },
    {
        titulo: "Problemas de Internet",
        descripcion: "Conexión intermitente reportada",
        ubicacion: "Medellín",
        lat: 6.2518,
        lng: -75.5636
    },
    {
        titulo: "Daño en red de agua",
        descripcion: "Baja presión en el servicio de agua",
        ubicacion: "Cali",
        lat: 3.4516,
        lng: -76.5320
    }
];


        // 🔹 Dibujar cada reporte en el mapa
        reportes.forEach(function(reporte) {
            if (reporte.lat && reporte.lng) {
                L.marker([reporte.lat, reporte.lng])
                    .addTo(map)
                    .bindPopup(`
    <b>\${reporte.titulo}</b><br>
    \${reporte.descripcion}<br>
    📍 <i>\${reporte.ubicacion}</i>
`);

            }
        });
    </script>
@endsection

