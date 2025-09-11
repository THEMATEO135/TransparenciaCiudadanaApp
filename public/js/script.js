
function obtenerUbicacion() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.querySelector('input[name="latitud"]').value = position.coords.latitude;
            document.querySelector('input[name="longitud"]').value = position.coords.longitude;
        }, function(error) {
            alert('No se pudo obtener la ubicación: ' + error.message);
        });
    } else {
        alert('Geolocalización no soportada por este navegador.');
    }
}
