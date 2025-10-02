document.addEventListener("DOMContentLoaded", function () {
    // 📊 Gráfica de servicios
    new Chart(document.getElementById("chartServicios"), {
        type: "bar",
        data: {
            labels: window.labelsServicios,
            datasets: [
                {
                    label: "Reportes por Servicio",
                    data: window.valoresServicios,
                    backgroundColor: "rgba(54, 162, 235, 0.7)",
                    borderRadius: 6,
                },
            ],
        },
    });

    // 📈 Gráfica de reportes por mes
    new Chart(document.getElementById("chartMeses"), {
        type: "line",
        data: {
            labels: window.labelsMeses,
            datasets: [
                {
                    label: "Reportes por Mes",
                    data: window.valoresMeses,
                    borderColor: "rgba(255, 99, 132, 1)",
                    backgroundColor: "rgba(255, 99, 132, 0.2)",
                    fill: true,
                    tension: 0.3,
                },
            ],
        },
    });

    // 🌍 Inicializar mapa centrado en Colombia
    var map = L.map("map").setView([4.6097, -74.0817], 6);

    // Capa base bonita (Carto Light)
    L.tileLayer("https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png", {
        attribution: '&copy; OpenStreetMap &copy; <a href="https://carto.com/">CARTO</a>',
        subdomains: "abcd",
        maxZoom: 19,
    }).addTo(map);

    // 📍 Datos desde Laravel
    var coordenadas = window.coordenadas || [];
    var currentView = 'heatmap'; // Vista por defecto

    // Preparar datos para el mapa de calor
    var heatData = coordenadas.map(c => [c.lat, c.lng, 0.8]); // [lat, lng, intensidad]

    // Crear capa de mapa de calor (solo si hay datos)
    var heatLayer = null;
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
        }).addTo(map);
    }

    // Crear capa de marcadores (no agregada por defecto)
    var markersLayer = L.layerGroup();
    coordenadas.forEach(c => {
        L.circleMarker([c.lat, c.lng], {
            radius: 8,
            color: "#ff0000",
            fillColor: "#ff0000",
            fillOpacity: 0.8,
        })
        .bindPopup(`<b>Reporte:</b> ${c.servicio ?? "N/A"}`)
        .addTo(markersLayer);
    });

    // Ajustar el mapa a los puntos si hay datos
    if (coordenadas.length > 0) {
        var bounds = L.latLngBounds(coordenadas.map(c => [c.lat, c.lng]));
        map.fitBounds(bounds, { padding: [50, 50] });
    }

    // Toggle entre vistas
    document.getElementById('btnHeatmap').addEventListener('click', function() {
        if (currentView !== 'heatmap' && heatLayer) {
            map.removeLayer(markersLayer);
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
            map.addLayer(markersLayer);
            currentView = 'markers';
            this.classList.remove('btn-secondary');
            this.classList.add('btn-primary');
            document.getElementById('btnHeatmap').classList.remove('btn-primary');
            document.getElementById('btnHeatmap').classList.add('btn-secondary');
        }
    });

    // 👇 Solución al "rompecabezas" (cuando no carga bien)
    setTimeout(() => {
        map.invalidateSize();
    }, 400);
});
