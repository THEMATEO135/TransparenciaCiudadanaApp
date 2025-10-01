@extends('admin.layouts.admin')

@section('title', 'Mapa de Reportes')

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
    <style>
        #map {
            height: 600px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .map-controls {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        .filter-group {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }
        .filter-group label {
            font-weight: 600;
            color: var(--secondary-color);
        }
        .leaflet-popup-content-wrapper {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .leaflet-popup-content {
            font-family: 'Inter', sans-serif;
        }
        .popup-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        .popup-info {
            color: var(--secondary-color);
            margin: 0.3rem 0;
        }
        .map-legend {
            background: white;
            padding: 1rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0.5rem 0;
        }
        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 50%;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title" data-icon="🗺️">Mapa de Reportes</h1>
        <div class="page-actions">
            <a href="{{ route('admin.reportes.index') }}" class="btn btn-primary">
                📑 Ver Lista de Reportes
            </a>
        </div>
    </div>

    <!-- Estadísticas del Mapa -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <span class="stat-icon">📍</span>
            <div class="stat-label">Reportes con Ubicación</div>
            <div class="stat-value" id="total-markers">0</div>
        </div>
        <div class="stat-card">
            <span class="stat-icon">⏳</span>
            <div class="stat-label">Pendientes</div>
            <div class="stat-value" id="pending-count">0</div>
        </div>
        <div class="stat-card">
            <span class="stat-icon">✅</span>
            <div class="stat-label">Resueltos</div>
            <div class="stat-value" id="resolved-count">0</div>
        </div>
        <div class="stat-card">
            <span class="stat-icon">🔄</span>
            <div class="stat-label">En Proceso</div>
            <div class="stat-value" id="process-count">0</div>
        </div>
    </div>

    <!-- Controles del Mapa -->
    <div class="map-controls fade-in">
        <div class="filter-group">
            <label>Filtrar por estado:</label>
            <button class="btn btn-sm btn-warning" onclick="filterMarkers('Pendiente')">⏳ Pendiente</button>
            <button class="btn btn-sm btn-info" onclick="filterMarkers('En Proceso')">🔄 En Proceso</button>
            <button class="btn btn-sm btn-success" onclick="filterMarkers('Resuelto')">✅ Resuelto</button>
            <button class="btn btn-sm btn-secondary" onclick="filterMarkers('all')">🔍 Ver Todos</button>
        </div>
    </div>

    <!-- Mapa -->
    <div class="row">
        <div class="col-lg-9">
            <div id="map" class="fade-in"></div>
        </div>
        <div class="col-lg-3">
            <div class="map-legend fade-in">
                <h5 style="color: var(--secondary-color); margin-bottom: 1rem;">📌 Leyenda</h5>
                <div class="legend-item">
                    <div class="legend-color" style="background: #f39c12;"></div>
                    <span>Pendiente</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #3498db;"></div>
                    <span>En Proceso</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #27ae60;"></div>
                    <span>Resuelto</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #95a5a6;"></div>
                    <span>Otros</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
    <script>
        // Inicializar el mapa en Colombia
        var map = L.map('map').setView([4.5709, -74.2973], 6);

        // Capa base de OpenStreetMap con estilo mejorado
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Datos de prueba (reemplazar con datos reales de la BD)
        var reportes = [
            {
                id: 1,
                titulo: "Corte de energía",
                descripcion: "Falla reportada en el suministro eléctrico",
                ubicacion: "Bogotá",
                ciudadano: "Juan Pérez",
                estado: "Pendiente",
                servicio: "Energía",
                lat: 4.6097,
                lng: -74.0817
            },
            {
                id: 2,
                titulo: "Problemas de Internet",
                descripcion: "Conexión intermitente reportada",
                ubicacion: "Medellín",
                ciudadano: "María García",
                estado: "En Proceso",
                servicio: "Internet",
                lat: 6.2518,
                lng: -75.5636
            },
            {
                id: 3,
                titulo: "Daño en red de agua",
                descripcion: "Baja presión en el servicio de agua",
                ubicacion: "Cali",
                ciudadano: "Carlos López",
                estado: "Resuelto",
                servicio: "Agua",
                lat: 3.4516,
                lng: -76.5320
            }
        ];

        // Función para obtener color según estado
        function getMarkerColor(estado) {
            switch(estado) {
                case 'Pendiente': return '#f39c12';
                case 'En Proceso': return '#3498db';
                case 'Resuelto': return '#27ae60';
                default: return '#95a5a6';
            }
        }

        // Crear iconos personalizados
        function createCustomIcon(color) {
            return L.divIcon({
                className: 'custom-marker',
                html: `<div style="background-color: ${color}; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>`,
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            });
        }

        // Almacenar todos los marcadores
        var allMarkers = [];
        var markerClusterGroup = L.markerClusterGroup();

        // Contadores por estado
        var counts = {
            total: 0,
            pending: 0,
            resolved: 0,
            process: 0
        };

        // Dibujar marcadores
        reportes.forEach(function(reporte) {
            if (reporte.lat && reporte.lng) {
                var color = getMarkerColor(reporte.estado);
                var icon = createCustomIcon(color);

                var marker = L.marker([reporte.lat, reporte.lng], { icon: icon })
                    .bindPopup(`
                        <div class="popup-title">${reporte.titulo}</div>
                        <div class="popup-info"><strong>ID:</strong> #${reporte.id}</div>
                        <div class="popup-info"><strong>Ciudadano:</strong> ${reporte.ciudadano}</div>
                        <div class="popup-info"><strong>Servicio:</strong> ${reporte.servicio}</div>
                        <div class="popup-info"><strong>Estado:</strong> <span style="color: ${color}; font-weight: 600;">${reporte.estado}</span></div>
                        <div class="popup-info"><strong>Descripción:</strong> ${reporte.descripcion}</div>
                        <div class="popup-info">📍 ${reporte.ubicacion}</div>
                        <div style="margin-top: 0.8rem;">
                            <a href="/admin/reportes/${reporte.id}/edit" class="btn btn-sm btn-warning" style="text-decoration: none;">✏️ Editar</a>
                        </div>
                    `);

                marker.estado = reporte.estado;
                allMarkers.push(marker);
                markerClusterGroup.addLayer(marker);

                // Actualizar contadores
                counts.total++;
                if (reporte.estado === 'Pendiente') counts.pending++;
                if (reporte.estado === 'Resuelto') counts.resolved++;
                if (reporte.estado === 'En Proceso') counts.process++;
            }
        });

        map.addLayer(markerClusterGroup);

        // Actualizar estadísticas
        document.getElementById('total-markers').textContent = counts.total;
        document.getElementById('pending-count').textContent = counts.pending;
        document.getElementById('resolved-count').textContent = counts.resolved;
        document.getElementById('process-count').textContent = counts.process;

        // Función para filtrar marcadores
        function filterMarkers(estado) {
            markerClusterGroup.clearLayers();

            allMarkers.forEach(function(marker) {
                if (estado === 'all' || marker.estado === estado) {
                    markerClusterGroup.addLayer(marker);
                }
            });
        }
    </script>
@endsection

