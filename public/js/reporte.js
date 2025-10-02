document.addEventListener('DOMContentLoaded', function() {
    // Cache de elementos DOM para mejor performance
    const DOM = {
        form: document.getElementById('reportForm'),
        result: document.getElementById('result'),
        submitBtn: document.getElementById('submitBtn'),
        locationStatus: document.getElementById('locationStatus'),
        getLocationBtn: document.getElementById('getLocationBtn'),
        servicePrompt: document.getElementById('servicePrompt'),
        localidadSelect: document.getElementById('localidad'),
        barrioSelect: document.getElementById('barrio'),
        latInput: document.getElementById('latitude'),
        lngInput: document.getElementById('longitude'),
        direccionInput: document.getElementById('direccion'),
        servicioInput: document.getElementById('servicio_id'),
        nombreInput: document.getElementById('nombres'),
        descTextarea: document.getElementById('descripcion')
    };

    // Variables globales del scope
    const form = DOM.form;
    const result = DOM.result;
    const submitBtn = DOM.submitBtn;
    const locationStatus = DOM.locationStatus;

    let serviceSelected = false;

    // Deshabilitar todos los controles al inicio
    function disableFormControls() {
        const form = document.getElementById('reportForm');
        if (!form) return;

        const formControls = form.querySelectorAll('input, textarea, select');
        formControls.forEach(control => {
            if (control.id !== 'submitBtn' && control.id !== 'getLocationBtn') {
                control.disabled = true;
            }
        });

        const submitBtn = document.getElementById('submitBtn');
        const getLocationBtn = document.getElementById('getLocationBtn');
        const servicePrompt = document.getElementById('servicePrompt');

        if (submitBtn) submitBtn.disabled = true;
        if (getLocationBtn) getLocationBtn.disabled = true;
        if (servicePrompt) servicePrompt.style.display = 'flex';
    }

    // Habilitar todos los controles
    function enableFormControls() {
        const form = document.getElementById('reportForm');
        if (!form) return;

        const formControls = form.querySelectorAll('input, textarea, select, button');
        formControls.forEach(control => {
            control.disabled = false;
        });

        // Mantener deshabilitados los campos de latitud/longitud
        const lat = document.getElementById('latitude');
        const lng = document.getElementById('longitude');
        if (lat) lat.disabled = true;
        if (lng) lng.disabled = true;

        const servicePrompt = document.getElementById('servicePrompt');
        if (servicePrompt) servicePrompt.style.display = 'none';

        // Scroll suave en móvil
        if (window.innerWidth < 992) {
            form.scrollIntoView({ behavior: 'smooth' });
        }

        // Foco en primer campo
        const firstName = document.getElementById('nombres');
        if (firstName) firstName.focus();
    }

    // Inicializar con controles deshabilitados
    disableFormControls();

    const BARRIOS_URL = 'https://serviciosgis.catastrobogota.gov.co/arcgis/rest/services/ordenamientoterritorial/entidadterritorial/MapServer/0/query';

    // Carga de Localidades
    async function cargarLocalidades() {
        try {
            const queryParams = new URLSearchParams({
                where: '1=1',
                outFields: 'LOCALIDAD',
                returnDistinctValues: 'true',
                orderByFields: 'LOCALIDAD',
                f: 'json',
                outSR: '4326'
            });

            const res = await fetch(`${BARRIOS_URL}?${queryParams}`);

            if (!res.ok) {
                throw new Error(`Error HTTP: ${res.status}`);
            }

            const json = await res.json();
            const selLoc = document.getElementById('localidad');
            if (!selLoc) return;

            selLoc.innerHTML = '<option value="">Selecciona una localidad</option>';

            if (json && json.features && json.features.length > 0) {
                const localidades = json.features
                    .map(f => f.attributes?.LOCALIDAD)
                    .filter(loc => loc && loc.trim() !== '')
                    .sort((a, b) => a.localeCompare(b, 'es', {sensitivity: 'base'}));

                const localidadesUnicas = [...new Set(localidades)];

                localidadesUnicas.forEach(loc => {
                    const opt = document.createElement('option');
                    opt.value = loc;
                    opt.textContent = loc;
                    selLoc.appendChild(opt);
                });
            } else {
                console.warn('No se encontraron localidades en la respuesta:', json);
                const localidadesPredefinidas = [
                    "USAQUÉN", "CHAPINERO", "SANTA FE", "SAN CRISTÓBAL",
                    "USME", "TUNJUELITO", "BOSA", "KENNEDY", "FONTIBÓN",
                    "ENGATIVÁ", "SUBA", "BARRIOS UNIDOS", "TEUSAQUILLO",
                    "LOS MÁRTIRES", "ANTONIO NARIÑO", "PUENTE ARANDA",
                    "LA CANDELARIA", "RAFAEL URIBE URIBE", "CIUDAD BOLÍVAR",
                    "SUMAPAZ"
                ];

                localidadesPredefinidas.forEach(loc => {
                    const opt = document.createElement('option');
                    opt.value = loc;
                    opt.textContent = loc;
                    selLoc.appendChild(opt);
                });
            }
        } catch (error) {
            console.error('Error cargando localidades:', error);
            const selLoc = document.getElementById('localidad');
            if (selLoc) {
                selLoc.innerHTML = '<option value="">Error cargando localidades</option>';
            }
        }
    }

    // Carga de Barrios por Localidad
    async function cargarBarriosPorLocalidad(localidad) {
        try {
            const selBar = document.getElementById('barrio');
            if (!selBar) return;

            selBar.disabled = true;
            selBar.innerHTML = '<option value="">Cargando barrios...</option>';

            const whereClause = encodeURIComponent(`LOCALIDAD = '${localidad.replace(/'/g, "''")}'`);
            const queryParams = new URLSearchParams({
                where: whereClause,
                outFields: 'BARRIOCOMU',
                returnDistinctValues: 'true',
                orderByFields: 'BARRIOCOMU',
                f: 'json',
                outSR: '4326'
            });

            const res = await fetch(`${BARRIOS_URL}?${queryParams}`);

            if (!res.ok) {
                throw new Error(`Error HTTP: ${res.status}`);
            }

            const json = await res.json();
            selBar.innerHTML = '<option value="">Selecciona un barrio</option>';

            if (json && json.features && json.features.length > 0) {
                const barrios = json.features
                    .map(f => f.attributes?.BARRIOCOMU)
                    .filter(barrio => barrio && barrio.trim() !== '')
                    .sort((a, b) => a.localeCompare(b, 'es', {sensitivity: 'base'}));

                const barriosUnicos = [...new Set(barrios)];

                barriosUnicos.forEach(barrio => {
                    const opt = document.createElement('option');
                    opt.value = barrio;
                    opt.textContent = barrio;
                    selBar.appendChild(opt);
                });
            } else {
                console.warn('No se encontraron barrios para la localidad:', localidad, json);
                selBar.innerHTML = '<option value="">No se encontraron barrios</option>';
            }

            selBar.disabled = false;
        } catch (error) {
            console.error('Error cargando los barrios:', error);
            const selBar = document.getElementById('barrio');
            if (selBar) {
                selBar.innerHTML = '<option value="">Error cargando barrios</option>';
                selBar.disabled = false;
            }
        }
    }

    // Listeners
    cargarLocalidades();

    const localidadSelect = document.getElementById('localidad');
    if (localidadSelect) {
        localidadSelect.addEventListener('change', (e) => {
            const loc = e.target.value;
            if (loc) {
                cargarBarriosPorLocalidad(loc);
            } else {
                const selBar = document.getElementById('barrio');
                if (selBar) {
                    selBar.innerHTML = '<option value="">Selecciona un barrio</option>';
                    selBar.disabled = true;
                }
            }
        });
    }

    // Service selection functionality
    window.selectService = function(serviceType, event) {
        const serviceInput = document.getElementById('servicio_id');
        if (!serviceInput) {
            console.error('Elemento #servicio_id no encontrado');
            return;
        }

        const serviceMap = {
            'energia': 1,
            'internet': 2,
            'gas': 3,
            'agua': 4
        };

        serviceInput.value = serviceMap[serviceType] || 1;

        document.querySelectorAll('.service-card').forEach(card => {
            card.classList.remove('selected');
        });

        const targetCard = event.target.closest('.service-card');
        if (targetCard) {
            targetCard.classList.add('selected');
        }

        if (!serviceSelected) {
            enableFormControls();
            serviceSelected = true;
        }
    };

    // Automatic geolocation functionality (called on submit)
    let map = null;
    let marker = null;

    function tryGetLocation() {
        return new Promise((resolve, reject) => {
            if (!window.isSecureContext && location.hostname !== 'localhost') {
                reject(new Error('HTTPS requerido'));
                return;
            }

            const options = {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            };

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        resolve({
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude
                        });
                    },
                    function (error) {
                        reject(error);
                    },
                    options
                );
            } else {
                reject(new Error('Geolocalización no soportada'));
            }
        });
    }

    function showMapSelector() {
        const mapSelector = document.getElementById('mapSelector');
        const locationStatus = document.getElementById('locationStatus');
        const locationStatusText = document.getElementById('locationStatusText');

        if (!mapSelector) return;

        mapSelector.style.display = 'block';
        locationStatus.style.display = 'block';
        locationStatus.style.color = 'var(--primary-color)';
        locationStatusText.textContent = 'No pudimos obtener tu ubicación automáticamente. Por favor, selecciónala en el mapa.';

        // Initialize map if not already initialized
        if (!map) {
            // Default to Bogotá center
            map = L.map('map').setView([4.60971, -74.08175], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Add click event to map
            map.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;

                // Update hidden inputs
                document.getElementById('latitude').value = lat.toFixed(6);
                document.getElementById('longitude').value = lng.toFixed(6);

                // Remove existing marker
                if (marker) {
                    map.removeLayer(marker);
                }

                // Add new marker
                marker = L.marker([lat, lng]).addTo(map);

                locationStatusText.textContent = 'Ubicación seleccionada correctamente en el mapa.';
                locationStatus.style.color = 'var(--success-color)';
            });

            // Search functionality
            const searchInput = document.getElementById('mapSearch');
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const query = this.value;
                        if (query) {
                            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query + ', Bogotá, Colombia')}`)
                                .then(r => r.json())
                                .then(data => {
                                    if (data && data.length > 0) {
                                        const lat = parseFloat(data[0].lat);
                                        const lon = parseFloat(data[0].lon);

                                        map.setView([lat, lon], 16);

                                        if (marker) {
                                            map.removeLayer(marker);
                                        }

                                        marker = L.marker([lat, lon]).addTo(map);

                                        document.getElementById('latitude').value = lat.toFixed(6);
                                        document.getElementById('longitude').value = lon.toFixed(6);

                                        locationStatusText.textContent = 'Ubicación encontrada y seleccionada.';
                                        locationStatus.style.color = 'var(--success-color)';
                                    } else {
                                        alert('No se encontró la dirección. Intenta con otra búsqueda.');
                                    }
                                })
                                .catch(err => {
                                    console.error('Error buscando dirección:', err);
                                    alert('Error al buscar la dirección.');
                                });
                        }
                    }
                });
            }
        }

        // Force map to resize
        setTimeout(() => {
            if (map) map.invalidateSize();
        }, 100);
    }

    // Form submission with automatic geolocation
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            result.classList.remove('show');

            // Try to get location automatically if not already set
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');

            if (!latInput.value || !lngInput.value) {
                const locationStatus = document.getElementById('locationStatus');
                const locationStatusText = document.getElementById('locationStatusText');

                locationStatus.style.display = 'block';
                locationStatus.style.color = 'var(--primary-color)';
                locationStatusText.textContent = 'Obteniendo tu ubicación...';

                try {
                    const coords = await tryGetLocation();
                    latInput.value = coords.latitude.toFixed(6);
                    lngInput.value = coords.longitude.toFixed(6);

                    locationStatus.style.color = 'var(--success-color)';
                    locationStatusText.textContent = 'Ubicación obtenida correctamente.';

                    // Continue with form submission after getting location
                    setTimeout(() => submitFormData(), 500);
                    return;
                } catch (error) {
                    console.log('Error obteniendo ubicación:', error);
                    // Show map selector
                    showMapSelector();

                    submitBtn.classList.remove('loading');
                    submitBtn.disabled = false;

                    result.textContent = 'Por favor, selecciona tu ubicación en el mapa antes de enviar el reporte.';
                    result.className = 'result-message error show';
                    return;
                }
            }

            // If location is already set, submit directly
            submitFormData();
        });
    }

    async function submitFormData() {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        // Asegurar que latitude y longitude se incluyan explícitamente
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');

        if (latInput && latInput.value) {
            data.latitude = latInput.value;
        }
        if (lngInput && lngInput.value) {
            data.longitude = lngInput.value;
        }

        console.log('Datos a enviar:', data); // Debug

        try {
            const resp = await fetch('/reportes', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            });

            if (!resp.ok) {
                throw new Error(`HTTP error! status: ${resp.status}`);
            }

            const json = await resp.json();

            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;

            if (json.ok) {
                result.textContent = '¡Reporte enviado exitosamente! ID: ' + json.id;
                result.className = 'result-message success show';

                setTimeout(() => {
                    form.reset();
                    result.classList.remove('show');
                    document.querySelectorAll('.service-card').forEach(card => {
                        card.classList.remove('selected');
                    });

                    // Hide and reset location elements
                    const locationStatus = document.getElementById('locationStatus');
                    const mapSelector = document.getElementById('mapSelector');
                    if (locationStatus) locationStatus.style.display = 'none';
                    if (mapSelector) mapSelector.style.display = 'none';
                    if (map && marker) {
                        map.removeLayer(marker);
                        marker = null;
                    }

                    disableFormControls();
                    serviceSelected = false;
                }, 5000);
            } else {
                result.textContent = 'Error al enviar el reporte: ' + (json.error || JSON.stringify(json));
                result.className = 'result-message error show';
            }
        } catch (err) {
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;

            result.textContent = 'Error de conexión: ' + err.message;
            result.className = 'result-message error show';
            console.error('Error:', err);
        }
    }



    // Enhanced form interactions
    document.querySelectorAll('.form-control, .form-select').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
        });

        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
        });
    });

    // Auto-expand textarea
    const descTextarea = document.getElementById('descripcion');
    if (descTextarea) {
        descTextarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }
});