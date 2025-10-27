document.addEventListener('DOMContentLoaded', function() {
    // Cache de elementos DOM para mejor performance
    const DOM = {
        form: document.getElementById('reportForm'),
        result: document.getElementById('result'),
        submitBtn: document.getElementById('submitBtn'),
        locationStatus: document.getElementById('locationStatus'),
        servicePrompt: document.getElementById('servicePrompt'),
        latInput: document.getElementById('latitude'),
        lngInput: document.getElementById('longitude'),
        servicioInput: document.getElementById('servicio_id'),
        ciudadInput: document.getElementById('ciudad_id'),
        proveedorInput: document.getElementById('proveedor_id'),
        departamentoModal: document.getElementById('departamentoModal'),
        ciudadModal: document.getElementById('ciudadModal'),
        proveedorModal: document.getElementById('proveedorModal'),
        departamentosList: document.getElementById('departamentosList'),
        ciudadesList: document.getElementById('ciudadesList'),
        proveedoresList: document.getElementById('proveedoresList'),
        departamentoSearch: document.getElementById('departamentoSearch'),
        ciudadSearch: document.getElementById('ciudadSearch'),
        proveedorSearch: document.getElementById('proveedorSearch'),
        skipProveedorBtn: document.getElementById('skipProveedorBtn'),
        locationAlert: document.getElementById('locationAlert')
    };

    // Variables globales
    let serviceSelected = false;
    let ciudadesData = [];
    let departamentosData = [];
    let proveedoresData = [];
    let departamentoSeleccionado = null;
    let ciudadSeleccionada = null;
    let map = null;
    let marker = null;

    // =====================================
    // FUNCIONES DE CONTROL DE FORMULARIO
    // =====================================
    function disableFormControls() {
        const form = document.getElementById('reportForm');
        if (!form) return;

        const formControls = form.querySelectorAll('input:not([type="hidden"]), textarea, select');
        formControls.forEach(control => {
            control.disabled = true;
        });

        if (DOM.submitBtn) DOM.submitBtn.disabled = true;
        if (DOM.servicePrompt) DOM.servicePrompt.style.display = 'flex';
    }

    function enableFormControls() {
        const form = document.getElementById('reportForm');
        if (!form) return;

        const formControls = form.querySelectorAll('input:not([type="hidden"]), textarea, select, button');
        formControls.forEach(control => {
            control.disabled = false;
        });

        if (DOM.servicePrompt) DOM.servicePrompt.style.display = 'none';

        // Scroll suave en móvil
        if (window.innerWidth < 992) {
            form.scrollIntoView({ behavior: 'smooth' });
        }

        // Foco en primer campo
        const firstName = document.getElementById('nombres');
        if (firstName) firstName.focus();
    }

    disableFormControls();

    // =====================================
    // MODAL FUNCTIONS
    // =====================================
    function openModal(modalElement) {
        if (modalElement) {
            modalElement.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(modalElement) {
        if (modalElement) {
            modalElement.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    }

    // =====================================
    // CARGAR DEPARTAMENTOS Y CIUDADES
    // =====================================
    async function cargarCiudades() {
        try {
            const res = await fetch('/api/ciudades');
            if (!res.ok) throw new Error(`Error HTTP: ${res.status}`);

            const json = await res.json();
            if (json.success && json.data) {
                ciudadesData = json.data;

                // Extraer departamentos únicos
                const departamentosMap = {};
                json.data.forEach(ciudad => {
                    if (!departamentosMap[ciudad.departamento]) {
                        departamentosMap[ciudad.departamento] = {
                            nombre: ciudad.departamento,
                            count: 0
                        };
                    }
                    departamentosMap[ciudad.departamento].count++;
                });

                departamentosData = Object.values(departamentosMap).sort((a, b) =>
                    a.nombre.localeCompare(b.nombre, 'es')
                );

                renderDepartamentos(departamentosData);
            }
        } catch (error) {
            console.error('Error cargando ciudades:', error);
            if (DOM.departamentosList) {
                DOM.departamentosList.innerHTML = '<p style="text-align: center; color: #e74c3c; padding: 2rem;">Error al cargar departamentos</p>';
            }
        }
    }

function renderDepartamentos(departamentos) {
    if (!DOM.departamentosList) return;

    DOM.departamentosList.innerHTML = '';

    if (departamentos.length === 0) {
        DOM.departamentosList.innerHTML = '<p style="text-align: center; color: #6c757d; padding: 2rem;">No se encontraron departamentos</p>';
        return;
    }

    departamentos.forEach(departamento => {
        const card = document.createElement('div');
        card.className = 'departamento-card';

        const nombreDepartamento = departamento.nombre
            .toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/\s+/g, '-');

        const banderaDepartamento = `/img/banderas/departamentos/${nombreDepartamento}.png`;

        card.innerHTML = `
            <div class="departamento-icon">
                <img src="${banderaDepartamento}" 
                    alt="${departamento.nombre}" 
                    onerror="this.onerror=null;this.src='/img/banderas/colombia.png';" 
                    class="bandera-img">
            </div>
            <div class="departamento-name">${departamento.nombre}</div>
            <div class="departamento-count">${departamento.count} ${departamento.count === 1 ? 'municipio' : 'municipios'}</div>
        `;
        card.addEventListener('click', () => seleccionarDepartamento(departamento));
        DOM.departamentosList.appendChild(card);
    });
}

    // Búsqueda de departamentos
    if (DOM.departamentoSearch) {
        DOM.departamentoSearch.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const filtrados = departamentosData.filter(dpto =>
                dpto.nombre.toLowerCase().includes(searchTerm)
            );
            renderDepartamentos(filtrados);
        });
    }

    function renderCiudades(ciudades) {
    if (!DOM.ciudadesList) return;

    DOM.ciudadesList.innerHTML = '';

    if (ciudades.length === 0) {
        DOM.ciudadesList.innerHTML = '<p style="text-align: center; color: #6c757d; padding: 2rem;">No se encontraron ciudades</p>';
        return;
    }

    ciudades.forEach(ciudad => {
        const card = document.createElement('div');
        card.className = 'ciudad-card';

        const nombreCiudad = ciudad.nombre
            .toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/\s+/g, '-');

        const nombreDepartamento = ciudad.departamento
            .toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/\s+/g, '-');

        const banderaCiudad = `/img/banderas/ciudades/${nombreCiudad}.png`;
        const banderaDepartamento = `/img/banderas/departamentos/${nombreDepartamento}.png`;
        const banderaDefault = `/img/banderas/colombia.png`;

        card.innerHTML = `
            <div class="ciudad-icon">
                <img src="${banderaCiudad}" 
                    alt="${ciudad.nombre}" 
                    onerror="this.onerror=null;this.src='${banderaDepartamento}';this.onerror=function(){this.src='${banderaDefault}';};" 
                    class="bandera-img">
            </div>
            <div class="ciudad-name">${ciudad.nombre}</div>
            <div class="ciudad-departamento">${ciudad.departamento}</div>
        `;

        card.addEventListener('click', () => seleccionarCiudad(ciudad));
        DOM.ciudadesList.appendChild(card);
    });
}


    // Búsqueda de ciudades
    if (DOM.ciudadSearch) {
        DOM.ciudadSearch.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const filtradas = ciudadesData.filter(ciudad =>
                ciudad.nombre.toLowerCase().includes(searchTerm) ||
                ciudad.departamento.toLowerCase().includes(searchTerm)
            );
            renderCiudades(filtradas);
        });
    }

    // =====================================
    // SELECCIONAR DEPARTAMENTO
    // =====================================
    function seleccionarDepartamento(departamento) {
        departamentoSeleccionado = departamento;

        closeModal(DOM.departamentoModal);

        // Filtrar ciudades por departamento
        const ciudadesFiltradas = ciudadesData.filter(ciudad =>
            ciudad.departamento === departamento.nombre
        );

        renderCiudades(ciudadesFiltradas);

        // Actualizar título del modal de ciudades
        const dptoLabel = document.querySelector('#departamentoSeleccionado strong');
        if (dptoLabel) {
            dptoLabel.textContent = departamento.nombre;
        }

        // Abrir modal de ciudades
        openModal(DOM.ciudadModal);
    }

    // =====================================
    // SELECCIONAR CIUDAD
    // =====================================
    async function seleccionarCiudad(ciudad) {
        ciudadSeleccionada = ciudad;
        DOM.ciudadInput.value = ciudad.id;

        closeModal(DOM.ciudadModal);

        // Abrir modal de proveedores
        await cargarProveedores(ciudad.id, DOM.servicioInput.value);
        openModal(DOM.proveedorModal);

        // Actualizar título del modal de proveedores
        const ciudadLabel = document.querySelector('#ciudadSeleccionada strong');
        if (ciudadLabel) {
            ciudadLabel.textContent = `${ciudad.nombre}, ${ciudad.departamento}`;
        }
    }

    // =====================================
    // CARGAR PROVEEDORES
    // =====================================
    async function cargarProveedores(ciudadId, servicioId) {
        if (!DOM.proveedoresList) return;

        DOM.proveedoresList.innerHTML = '<p style="text-align: center; color: #6c757d; padding: 2rem;">Cargando proveedores...</p>';

        try {
            const res = await fetch(`/api/proveedores?ciudad_id=${ciudadId}&servicio_id=${servicioId}`);
            if (!res.ok) throw new Error(`Error HTTP: ${res.status}`);

            const json = await res.json();

            if (json.success && json.data && json.data.length > 0) {
                proveedoresData = json.data;
                renderProveedores(proveedoresData);
            } else {
                proveedoresData = [];
                DOM.proveedoresList.innerHTML = '<p style="text-align: center; color: #6c757d; padding: 2rem;">No hay proveedores disponibles para esta ciudad y servicio</p>';
            }
        } catch (error) {
            console.error('Error cargando proveedores:', error);
            proveedoresData = [];
            DOM.proveedoresList.innerHTML = '<p style="text-align: center; color: #e74c3c; padding: 2rem;">Error al cargar proveedores</p>';
        }
    }

    function renderProveedores(proveedores) {
        if (!DOM.proveedoresList) return;

        DOM.proveedoresList.innerHTML = '';

        if (proveedores.length === 0) {
            DOM.proveedoresList.innerHTML = '<p style="text-align: center; color: #6c757d; padding: 2rem;">No se encontraron proveedores</p>';
            return;
        }

        proveedores.forEach(proveedor => {
            const card = document.createElement('div');
            card.className = 'proveedor-card';
            card.innerHTML = `
                <div class="proveedor-logo-container">
                    ${proveedor.logo_url ?
                        `<img src="${proveedor.logo_url}" alt="${proveedor.nombre}" class="proveedor-logo" onerror="this.onerror=null; this.style.display='none'; this.parentElement.innerHTML='<div class=\\'proveedor-placeholder\\'><i class=\\'fas fa-building\\'></i></div>';">` :
                        `<div class="proveedor-placeholder"><i class="fas fa-building"></i></div>`
                    }
                </div>
                <div class="proveedor-name">${proveedor.nombre}</div>
                ${proveedor.telefono || proveedor.email ?
                    `<div class="proveedor-info">
                        ${proveedor.telefono ? `<div>📞 ${proveedor.telefono}</div>` : ''}
                        ${proveedor.email ? `<div>📧 ${proveedor.email}</div>` : ''}
                    </div>` : ''
                }
            `;
            card.addEventListener('click', () => seleccionarProveedor(proveedor));
            DOM.proveedoresList.appendChild(card);
        });
    }

    // Búsqueda de proveedores
    if (DOM.proveedorSearch) {
        DOM.proveedorSearch.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const filtrados = proveedoresData.filter(proveedor =>
                proveedor.nombre.toLowerCase().includes(searchTerm)
            );
            renderProveedores(filtrados);
        });
    }

    // =====================================
    // SELECCIONAR PROVEEDOR
    // =====================================
    function seleccionarProveedor(proveedor) {
        if (proveedor) {
            DOM.proveedorInput.value = proveedor.id;
        } else {
            DOM.proveedorInput.value = '';
        }

        closeModal(DOM.proveedorModal);
        actualizarResumen(proveedor);
        enableFormControls();
    }

    // =====================================
    // ACTUALIZAR RESUMEN
    // =====================================
    function actualizarResumen(proveedor) {
        const serviciosMap = {
            '1': 'ENERGÍA ELÉCTRICA',
            '2': 'INTERNET',
            '3': 'GAS NATURAL',
            '4': 'ACUEDUCTO'
        };

        const servicioId = DOM.servicioInput.value;
        const servicioNombre = serviciosMap[servicioId] || '-';
        const proveedorNombre = proveedor ? proveedor.nombre.toUpperCase() : 'SIN ESPECIFICAR';
        const ciudadNombre = ciudadSeleccionada ? ciudadSeleccionada.nombre.toUpperCase() : '-';

        const summaryServicio = document.getElementById('summaryServicio');
        const summaryProveedor = document.getElementById('summaryProveedor');
        const summaryCiudad = document.getElementById('summaryCiudad');
        const selectionSummary = document.getElementById('selectionSummary');

        if (summaryServicio) summaryServicio.textContent = servicioNombre;
        if (summaryProveedor) summaryProveedor.textContent = proveedorNombre;
        if (summaryCiudad) summaryCiudad.textContent = ciudadNombre;
        if (selectionSummary) selectionSummary.style.display = 'block';
    }

    // Skip proveedor button
    if (DOM.skipProveedorBtn) {
        DOM.skipProveedorBtn.addEventListener('click', () => {
            seleccionarProveedor(null);
        });
    }

    // =====================================
    // SERVICE SELECTION
    // =====================================
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

        serviceSelected = true;

        // Abrir modal de departamentos
        openModal(DOM.departamentoModal);
    };

    // =====================================
    // VALIDACIÓN DE COORDENADAS
    // =====================================
    function validarCoordenadas(lat, lng, ciudadId) {
        // Coordenadas aproximadas de las principales ciudades (se puede mejorar con un API de geocoding)
        const coordenadasCiudades = {
            1: { lat: 4.710989, lng: -74.072092, radius: 50 },  // Bogotá
            2: { lat: 6.244203, lng: -75.581212, radius: 40 },  // Medellín
            3: { lat: 3.451647, lng: -76.531985, radius: 40 },  // Cali
            4: { lat: 10.963889, lng: -74.796387, radius: 35 }, // Barranquilla
            5: { lat: 10.391049, lng: -75.479426, radius: 30 }, // Cartagena
            // Agregar más ciudades según sea necesario
        };

        const ciudad = coordenadasCiudades[ciudadId];
        if (!ciudad) return true; // Si no tenemos las coordenadas de la ciudad, asumimos que está bien

        // Calcular distancia aproximada en km usando fórmula de Haversine simplificada
        const R = 6371; // Radio de la Tierra en km
        const dLat = (ciudad.lat - lat) * Math.PI / 180;
        const dLng = (ciudad.lng - lng) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat * Math.PI / 180) * Math.cos(ciudad.lat * Math.PI / 180) *
                  Math.sin(dLng/2) * Math.sin(dLng/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        const distance = R * c;

        return distance <= ciudad.radius;
    }

    function mostrarAlertaUbicacion() {
        if (DOM.locationAlert) {
            DOM.locationAlert.classList.add('show');
            setTimeout(() => {
                if (showMapSelector) showMapSelector();
            }, 2000);
        }
    }

    window.closeLocationAlert = function() {
        if (DOM.locationAlert) {
            DOM.locationAlert.classList.remove('show');
        }
    };

    // Cargar ciudades al iniciar
    cargarCiudades();

    // Close and Back buttons
    const closeFromDepartamento = document.getElementById('closeFromDepartamento');
    if (closeFromDepartamento) {
        closeFromDepartamento.addEventListener('click', () => {
            closeModal(DOM.departamentoModal);
            // Deseleccionar el servicio
            document.querySelectorAll('.service-card').forEach(card => {
                card.classList.remove('selected');
            });
            DOM.servicioInput.value = '';
            serviceSelected = false;
        });
    }

    const backFromCiudad = document.getElementById('backFromCiudad');
    if (backFromCiudad) {
        backFromCiudad.addEventListener('click', () => {
            closeModal(DOM.ciudadModal);
            openModal(DOM.departamentoModal);
        });
    }

    const backFromProveedor = document.getElementById('backFromProveedor');
    if (backFromProveedor) {
        backFromProveedor.addEventListener('click', () => {
            closeModal(DOM.proveedorModal);
            openModal(DOM.ciudadModal);
        });
    }

    // =====================================
    // GEOLOCALIZACIÓN Y MAPA
    // =====================================
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
        const locationStatus = DOM.locationStatus;
        const locationStatusText = document.getElementById('locationStatusText');

        if (!mapSelector) return;

        mapSelector.style.display = 'block';
        if (locationStatus) {
            locationStatus.style.display = 'block';
            locationStatus.style.color = 'var(--primary-color)';
        }
        if (locationStatusText) {
            locationStatusText.textContent = 'Por favor, selecciona tu ubicación en el mapa.';
        }

        if (!map) {
            // Default coordinates based on selected ciudad or Bogotá
            let defaultLat = 4.60971;
            let defaultLng = -74.08175;

            if (ciudadSeleccionada) {
                // Aquí podrías usar las coordenadas de la ciudad seleccionada
                // Por ahora usamos Bogotá por defecto
            }

            map = L.map('map').setView([defaultLat, defaultLng], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            map.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;

                DOM.latInput.value = lat.toFixed(6);
                DOM.lngInput.value = lng.toFixed(6);

                if (marker) {
                    map.removeLayer(marker);
                }

                marker = L.marker([lat, lng]).addTo(map);

                if (locationStatusText) {
                    locationStatusText.textContent = 'Ubicación seleccionada correctamente en el mapa.';
                }
                if (locationStatus) {
                    locationStatus.style.color = 'var(--success-color)';
                }

                // Cerrar alerta de ubicación si está abierta
                closeLocationAlert();
            });

            // Search functionality
            const searchInput = document.getElementById('mapSearch');
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const query = this.value;
                        if (query) {
                            const searchQuery = ciudadSeleccionada ?
                                `${query}, ${ciudadSeleccionada.nombre}, Colombia` :
                                `${query}, Colombia`;

                            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(searchQuery)}`)
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

                                        DOM.latInput.value = lat.toFixed(6);
                                        DOM.lngInput.value = lon.toFixed(6);

                                        if (locationStatusText) {
                                            locationStatusText.textContent = 'Ubicación encontrada y seleccionada.';
                                        }
                                        if (locationStatus) {
                                            locationStatus.style.color = 'var(--success-color)';
                                        }
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

        setTimeout(() => {
            if (map) map.invalidateSize();
        }, 100);
    }

    // =====================================
    // FORM SUBMISSION
    // =====================================
    if (DOM.form) {
        DOM.form.addEventListener('submit', async (e) => {
            e.preventDefault();

            DOM.submitBtn.classList.add('loading');
            DOM.submitBtn.disabled = true;
            DOM.result.classList.remove('show');

            // Try to get location automatically if not already set
            if (!DOM.latInput.value || !DOM.lngInput.value) {
                const locationStatus = DOM.locationStatus;
                const locationStatusText = document.getElementById('locationStatusText');

                if (locationStatus) locationStatus.style.display = 'block';
                if (locationStatus) locationStatus.style.color = 'var(--primary-color)';
                if (locationStatusText) locationStatusText.textContent = 'Obteniendo tu ubicación...';

                try {
                    const coords = await tryGetLocation();
                    DOM.latInput.value = coords.latitude.toFixed(6);
                    DOM.lngInput.value = coords.longitude.toFixed(6);

                    if (locationStatus) locationStatus.style.color = 'var(--success-color)';
                    if (locationStatusText) locationStatusText.textContent = 'Ubicación obtenida correctamente.';

                    // Validar coordenadas vs ciudad
                    if (DOM.ciudadInput.value) {
                        const esValida = validarCoordenadas(
                            parseFloat(coords.latitude),
                            parseFloat(coords.longitude),
                            parseInt(DOM.ciudadInput.value)
                        );

                        if (!esValida) {
                            mostrarAlertaUbicacion();
                            DOM.submitBtn.classList.remove('loading');
                            DOM.submitBtn.disabled = false;
                            DOM.result.textContent = 'Por favor, verifica tu ubicación en el mapa antes de enviar.';
                            DOM.result.className = 'result-message error show';
                            return;
                        }
                    }

                    setTimeout(() => submitFormData(), 500);
                    return;
                } catch (error) {
                    console.log('Error obteniendo ubicación:', error);
                    showMapSelector();

                    DOM.submitBtn.classList.remove('loading');
                    DOM.submitBtn.disabled = false;

                    DOM.result.textContent = 'Por favor, selecciona tu ubicación en el mapa antes de enviar el reporte.';
                    DOM.result.className = 'result-message error show';
                    return;
                }
            } else {
                // Validar coordenadas existentes vs ciudad
                if (DOM.ciudadInput.value) {
                    const esValida = validarCoordenadas(
                        parseFloat(DOM.latInput.value),
                        parseFloat(DOM.lngInput.value),
                        parseInt(DOM.ciudadInput.value)
                    );

                    if (!esValida) {
                        mostrarAlertaUbicacion();
                    }
                }
            }

            submitFormData();
        });
    }

    async function submitFormData() {
        const formData = new FormData(DOM.form);

        if (DOM.latInput && DOM.latInput.value) {
            formData.set('latitude', DOM.latInput.value);
        }
        if (DOM.lngInput && DOM.lngInput.value) {
            formData.set('longitude', DOM.lngInput.value);
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            formData.append('_token', csrfToken.getAttribute('content'));
        }

        console.log('Datos a enviar:', Object.fromEntries(formData.entries()));

        try {
            const resp = await fetch('/reportes', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : '',
                    'Accept': 'application/json'
                },
                body: formData
            });

            if (!resp.ok) {
                throw new Error(`HTTP error! status: ${resp.status}`);
            }

            const json = await resp.json();

            DOM.submitBtn.classList.remove('loading');
            DOM.submitBtn.disabled = false;

            if (json.ok) {
                DOM.result.textContent = '¡Reporte enviado exitosamente! ID: ' + json.id;
                DOM.result.className = 'result-message success show';

                setTimeout(() => {
                    DOM.form.reset();
                    DOM.result.classList.remove('show');
                    document.querySelectorAll('.service-card').forEach(card => {
                        card.classList.remove('selected');
                    });

                    // Reset variables
                    departamentoSeleccionado = null;
                    ciudadSeleccionada = null;
                    DOM.ciudadInput.value = '';
                    DOM.proveedorInput.value = '';

                    // Hide summary
                    const selectionSummary = document.getElementById('selectionSummary');
                    if (selectionSummary) selectionSummary.style.display = 'none';

                    // Hide and reset location elements
                    const locationStatus = DOM.locationStatus;
                    const mapSelector = document.getElementById('mapSelector');
                    if (locationStatus) locationStatus.style.display = 'none';
                    if (mapSelector) mapSelector.style.display = 'none';
                    if (map && marker) {
                        map.removeLayer(marker);
                        marker = null;
                    }

                    closeLocationAlert();

                    disableFormControls();
                    serviceSelected = false;
                }, 5000);
            } else {
                DOM.result.textContent = 'Error al enviar el reporte: ' + (json.error || JSON.stringify(json));
                DOM.result.className = 'result-message error show';
            }
        } catch (err) {
            DOM.submitBtn.classList.remove('loading');
            DOM.submitBtn.disabled = false;

            DOM.result.textContent = 'Error de conexión: ' + err.message;
            DOM.result.className = 'result-message error show';
            console.error('Error:', err);
        }
    }

    // =====================================
    // ENHANCED FORM INTERACTIONS
    // =====================================
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
