@extends('admin.layouts.admin')

@section('title', 'Mapa de Reportes')

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
    <style>
        .view-toggle {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
            background: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .view-toggle button {
            margin: 0 0.25rem;
        }
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
    <header class="page-header">
        <h1 class="page-title" data-icon="🗺️">Mapa de Reportes</h1>
        <div class="page-actions">
            <a href="{{ route('admin.reportes.index') }}" class="btn btn-primary" aria-label="Ver la lista de reportes en formato de tabla">
                📑 Ver Lista de Reportes
            </a>
        </div>
    </header>

    <!-- Estadísticas del Mapa -->
    <section class="stats-grid mb-4" aria-label="Estadísticas de reportes en el mapa">
        <div class="stat-card" role="article" aria-labelledby="stat-ubicacion">
            <span class="stat-icon" aria-hidden="true">📍</span>
            <div class="stat-label" id="stat-ubicacion">Reportes con Ubicación</div>
            <div class="stat-value" id="total-markers" aria-live="polite">0</div>
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
            <div style="position: relative;">
                <div class="view-toggle">
                    <button class="btn btn-sm btn-primary" id="btnHeatmap">🔥 Mapa de Calor</button>
                    <button class="btn btn-sm btn-secondary" id="btnMarkers">📍 Marcadores</button>
                </div>
                <div id="map" class="fade-in"></div>
            </div>
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
    <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>

    <!-- Variables globales desde Laravel -->
    <script>
        window.reportes = @json($reportes);
    </script>

    <script>
        // Inicializar el mapa en Colombia
        var map = L.map('map').setView([4.5709, -74.2973], 6);

        // Capa base de OpenStreetMap con estilo mejorado
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Datos reales desde la BD
        var reportes = window.reportes || [];

        // Función para obtener color según estado
        function getMarkerColor(estado) {
            switch(estado) {
                case 'Pendiente': return '#f39c12';
                case 'En Proceso': return '#3498db';
                case 'Resuelto': return '#27ae60';
                default: return '#95a5a6';
            }
        }

        // Función para obtener icono según servicio
        function getServiceIcon(servicio) {
            const iconos = {
                'Energía Eléctrica': '⚡',
                'Internet': '📡',
                'Gas Natural': '🔥',
                'Acueducto': '💧',
                'Agua': '💧',
                'Electricidad': '⚡',
                'Gas': '🔥',
                'Alcantarillado': '🚰',
                'Basuras': '🗑️',
                'Transporte': '🚌',
                'Vías': '🛣️',
                'Alumbrado': '💡',
                'Parques': '🌳',
                'Seguridad': '🚨'
            };
            return iconos[servicio] || '📍';
        }

        // Crear iconos personalizados con emoji
        function createCustomIcon(color, servicio) {
            const emoji = getServiceIcon(servicio);
            return L.divIcon({
                className: 'custom-marker',
                html: `<div style="background-color: white; width: 36px; height: 36px; border-radius: 50%; border: 4px solid ${color}; box-shadow: 0 2px 8px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; font-size: 18px;">${emoji}</div>`,
                iconSize: [36, 36],
                iconAnchor: [18, 18]
            });
        }

        // Almacenar todos los marcadores y capas
        var allMarkers = [];
        var markerClusterGroup = L.markerClusterGroup();
        var heatLayer = null;
        var currentView = 'markers'; // 'markers' o 'heatmap'

        // Contadores por estado
        var counts = {
            total: 0,
            pending: 0,
            resolved: 0,
            process: 0
        };

        // Preparar datos para el mapa de calor
        var heatData = reportes.map(function(r) {
            return [r.lat, r.lng, 0.8]; // [lat, lng, intensidad]
        });

        // Crear capa de mapa de calor (inicialmente no agregada al mapa)
        if (heatData.length > 0) {
            heatLayer = L.heatLayer(heatData, {
                radius: 50,
                blur: 25,
                minOpacity: 0.4,
                maxZoom: 10,
                max: 1.0,
                gradient: {
                    0.0: 'blue',
                    0.3: 'cyan',
                    0.5: 'lime',
                    0.7: 'yellow',
                    1.0: 'red'
                }
            });
        }

        // Dibujar marcadores
        reportes.forEach(function(reporte) {
            if (reporte.lat && reporte.lng) {
                var color = getMarkerColor(reporte.estado);
                var icon = createCustomIcon(color, reporte.servicio);

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
                if (reporte.estado === 'En Proceso' || reporte.estado === 'En proceso') counts.process++;
            }
        });

        // Agregar capa de marcadores por defecto
        map.addLayer(markerClusterGroup);

        // Ajustar el mapa a los puntos si hay datos
        if (reportes.length > 0) {
            var bounds = L.latLngBounds(reportes.map(r => [r.lat, r.lng]));
            map.fitBounds(bounds, { padding: [50, 50] });
        }

        // Actualizar estadísticas
        document.getElementById('total-markers').textContent = counts.total;
        document.getElementById('pending-count').textContent = counts.pending;
        document.getElementById('resolved-count').textContent = counts.resolved;
        document.getElementById('process-count').textContent = counts.process;

        // Toggle entre vistas
        document.getElementById('btnHeatmap').addEventListener('click', function() {
            if (currentView !== 'heatmap' && heatLayer) {
                map.removeLayer(markerClusterGroup);
                map.addLayer(heatLayer);
                currentView = 'heatmap';
                this.classList.remove('btn-secondary');
                this.classList.add('btn-primary');
                document.getElementById('btnMarkers').classList.remove('btn-primary');
                document.getElementById('btnMarkers').classList.add('btn-secondary');
            }
        });

        document.getElementById('btnMarkers').addEventListener('click', function() {
            if (currentView !== 'markers') {
                if (heatLayer) {
                    map.removeLayer(heatLayer);
                }
                map.addLayer(markerClusterGroup);
                currentView = 'markers';
                this.classList.remove('btn-secondary');
                this.classList.add('btn-primary');
                document.getElementById('btnHeatmap').classList.remove('btn-primary');
                document.getElementById('btnHeatmap').classList.add('btn-secondary');
            }
        });

        // Función para filtrar marcadores (solo funciona en vista de marcadores)
        function filterMarkers(estado) {
            if (currentView !== 'markers') {
                // Cambiar a vista de marcadores primero
                document.getElementById('btnMarkers').click();
            }

            markerClusterGroup.clearLayers();

            allMarkers.forEach(function(marker) {
                if (estado === 'all' || marker.estado === estado) {
                    markerClusterGroup.addLayer(marker);
                }
            });
        }
    </script>
@endsection

