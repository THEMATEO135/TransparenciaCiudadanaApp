document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reportForm');
    const result = document.getElementById('result');
    const submitBtn = document.getElementById('submitBtn');
    const locationStatus = document.getElementById('locationStatus');
    const getLocationBtn = document.getElementById('getLocationBtn');
    const servicePrompt = document.getElementById('servicePrompt');
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

    // Geolocation functionality
    window.getCurrentLocation = function(evt) {
        if (!serviceSelected) return;

        const btn = evt?.currentTarget || evt?.target || document.querySelector('.get-location-btn');
        if (!btn) return;

        const originalContent = btn.innerHTML;

        if (!window.isSecureContext && location.hostname !== 'localhost') {
            locationStatus.innerHTML = '<i class="fas fa-lock"></i> Activa HTTPS (o prueba en localhost) para usar geolocalización.';
            locationStatus.style.color = 'var(--danger-color)';
            return;
        }

        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Obteniendo...';
        btn.disabled = true;

        locationStatus.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Obteniendo ubicación...';
        locationStatus.style.color = 'var(--primary-color)';

        const options = {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 300000
        };

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const latInput = document.getElementById('latitude');
                    const lngInput = document.getElementById('longitude');
                    if (latInput) latInput.value = position.coords.latitude.toFixed(6);
                    if (lngInput) lngInput.value = position.coords.longitude.toFixed(6);

                    fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${position.coords.latitude}&lon=${position.coords.longitude}`)
                        .then(r => r.json())
                        .then(d => {
                            if (d && d.display_name) {
                                const dir = document.getElementById('direccion');
                                if (dir && !dir.value) dir.value = d.display_name;
                            }
                        })
                        .catch(()=>{});

                    btn.innerHTML = '<i class="fas fa-check"></i> Ubicación obtenida';
                    btn.style.background = 'var(--success-color)';

                    locationStatus.innerHTML = '<i class="fas fa-check-circle"></i> Ubicación obtenida correctamente';
                    locationStatus.style.color = 'var(--success-color)';

                    setTimeout(() => {
                        btn.innerHTML = originalContent;
                        btn.style.background = 'var(--accent-color)';
                        btn.disabled = false;
                    }, 2000);
                },
                function (error) {
                    let errorMessage = 'Error al obtener ubicación';
                    if (error.code === error.PERMISSION_DENIED)  errorMessage = 'Permiso denegado para acceder a la ubicación';
                    if (error.code === error.POSITION_UNAVAILABLE) errorMessage = 'Ubicación no disponible';
                    if (error.code === error.TIMEOUT)             errorMessage = 'Tiempo de espera agotado';

                    btn.innerHTML = '<i class="fas fa-times"></i> Error';
                    btn.style.background = 'var(--danger-color)';

                    locationStatus.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + errorMessage;
                    locationStatus.style.color = 'var(--danger-color)';

                    setTimeout(() => {
                        btn.innerHTML = originalContent;
                        btn.style.background = 'var(--accent-color)';
                        btn.disabled = false;
                    }, 3000);
                },
                options
            );
        } else {
            btn.innerHTML = '<i class="fas fa-times"></i> No compatible';
            btn.style.background = 'var(--danger-color)';
            locationStatus.innerHTML = '<i class="fas fa-times-circle"></i> Geolocalización no soportada';
            locationStatus.style.color = 'var(--danger-color)';
            setTimeout(() => {
                btn.innerHTML = originalContent;
                btn.style.background = 'var(--accent-color)';
                btn.disabled = false;
            }, 3000);
        }
    };

    // Form submission
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            result.classList.remove('show');

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

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
                        if (locationStatus) {
                            locationStatus.innerHTML = '<i class="fas fa-info-circle"></i> Presiona "Obtener ubicación" para incluir tu ubicación actual';
                            locationStatus.style.color = 'var(--dark-gray)';
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
        });
    }

const phoneInput = document.getElementById('telefono');
if (phoneInput) {
    phoneInput.addEventListener('input', function(e) {
        const cursorPosition = e.target.selectionStart;
        let value = e.target.value;
        
        // Limpiar el valor: quitar todo excepto números
        let cleaned = value.replace(/\D/g, '');
        
        // Si empieza con 57, quitarlo (prefijo colombiano)
        if (cleaned.startsWith('57') && cleaned.length > 10) {
            cleaned = cleaned.substring(2);
        }
        
        // Limitar a 10 dígitos máximo
        cleaned = cleaned.substring(0, 10);
        
        // Validar que empiece por 3 (celulares colombianos)
        if (cleaned.length > 0 && !cleaned.startsWith('3')) {
            // Si no empieza por 3, mantener solo números válidos o limpiar
            if (cleaned.length === 1) {
                cleaned = ''; // Borrar si el primer dígito no es 3
            }
        }
        
        // Formatear el número
        let formatted = '';
        if (cleaned.length > 0) {
            if (cleaned.length <= 3) {
                formatted = `+57 ${cleaned}`;
            } else if (cleaned.length <= 6) {
                formatted = `+57 ${cleaned.slice(0, 3)} ${cleaned.slice(3)}`;
            } else {
                formatted = `+57 ${cleaned.slice(0, 3)} ${cleaned.slice(3, 6)} ${cleaned.slice(6)}`;
            }
        }
        
        // Actualizar el valor
        e.target.value = formatted;
        
        // Calcular nueva posición del cursor
        const oldLength = value.length;
        const newLength = formatted.length;
        const diff = newLength - oldLength;
        const newPosition = Math.min(cursorPosition + diff, formatted.length);
        
        // Restaurar posición del cursor
        setTimeout(() => {
            e.target.setSelectionRange(newPosition, newPosition);
        }, 0);
    });

    // Validación al salir del campo
    phoneInput.addEventListener('blur', function(e) {
        const cleaned = e.target.value.replace(/\D/g, '');
        
        // Si empieza con 57, quitarlo
        let finalNumber = cleaned;
        if (cleaned.startsWith('57') && cleaned.length > 10) {
            finalNumber = cleaned.substring(2);
        }
        
        // Validaciones
        if (finalNumber.length > 0) {
            if (finalNumber.length !== 10) {
                console.warn('El número debe tener exactamente 10 dígitos');
            } else if (!finalNumber.startsWith('3')) {
                console.warn('Los números de celular colombianos deben empezar por 3');
            } else {
                console.log('Número válido:', finalNumber);
            }
        }
    });

    // Manejar pegado de texto
    phoneInput.addEventListener('paste', function(e) {
        e.preventDefault();
        const pastedData = e.clipboardData.getData('text');
        let cleaned = pastedData.replace(/\D/g, '');
        
        // Si empieza con 57, quitarlo
        if (cleaned.startsWith('57') && cleaned.length > 10) {
            cleaned = cleaned.substring(2);
        }
        
        // Limitar a 10 dígitos y validar que empiece por 3
        cleaned = cleaned.substring(0, 10);
        if (cleaned.startsWith('3')) {
            // Simular evento input para formatear
            e.target.value = cleaned;
            e.target.dispatchEvent(new Event('input', { bubbles: true }));
        }
    });
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