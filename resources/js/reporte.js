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