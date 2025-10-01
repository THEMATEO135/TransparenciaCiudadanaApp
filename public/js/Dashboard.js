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

    // Dibujar un círculo rojo en cada coordenada con popup
    coordenadas.forEach(c => {
        L.circleMarker([c.lat, c.lng], {
            radius: 8,
            color: "#ff0000",
            fillColor: "#ff0000",
            fillOpacity: 0.8,
        })
        .addTo(map)
        .bindPopup(`<b>Reporte:</b> ${c.servicio ?? "N/A"}`);
    });

    // Ajustar el mapa a los puntos si hay datos
    if (coordenadas.length > 0) {
        var bounds = L.latLngBounds(coordenadas.map(c => [c.lat, c.lng]));
        map.fitBounds(bounds);
    }

    // 👇 Solución al "rompecabezas" (cuando no carga bien)
    setTimeout(() => {
        map.invalidateSize();
    }, 400);
});
