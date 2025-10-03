<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Transparencia Ciudadana - Reportar Falla</title>
    
    <!-- Fuentes y librerías externas -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Leaflet CSS para el mapa -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Tu CSS personalizado -->
   <link rel="stylesheet" href="{{ asset('css/transparencia.css') }}">

</head>
<body>
    <div class="main-container">
        <!-- Left Section - Information -->
        <div class="header-section">
            <div class="logo-container">
                <div style="width: 160px; height: 50px; background: linear-gradient(135deg, #ff6600, #e55a00); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.1rem; margin: 0 auto;">
                    COMPENSAR
                </div>
            </div>
            <h1 class="hero-title">¿Tienes problemas con alguno de estos servicios?</h1>
            <p class="hero-subtitle">Reporta fallas de manera rápida y sencilla en nuestros servicios principales</p>
 <div class="services-grid" role="group" aria-label="Seleccionar tipo de servicio">
    <div class="service-card" onclick="selectService('energia', event)" onkeypress="if(event.key==='Enter'||event.key===' ')selectService('energia',event)" tabindex="0" role="button" aria-label="Seleccionar Energía Eléctrica">
        <i class="fas fa-bolt service-icon" aria-hidden="true"></i>
        <div class="service-title">Energía Eléctrica</div>
    </div>
    <div class="service-card" onclick="selectService('internet', event)" onkeypress="if(event.key==='Enter'||event.key===' ')selectService('internet',event)" tabindex="0" role="button" aria-label="Seleccionar Internet">
        <i class="fas fa-wifi service-icon" aria-hidden="true"></i>
        <div class="service-title">Internet</div>
    </div>
    <div class="service-card" onclick="selectService('gas', event)" onkeypress="if(event.key==='Enter'||event.key===' ')selectService('gas',event)" tabindex="0" role="button" aria-label="Seleccionar Gas Natural">
        <i class="fas fa-fire service-icon" aria-hidden="true"></i>
        <div class="service-title">Gas Natural</div>
    </div>
    <div class="service-card" onclick="selectService('agua', event)" onkeypress="if(event.key==='Enter'||event.key===' ')selectService('agua',event)" tabindex="0" role="button" aria-label="Seleccionar Acueducto">
        <i class="fas fa-tint service-icon" aria-hidden="true"></i>
        <div class="service-title">Acueducto</div>
    </div>
</div>
            <div class="cta-text">
                <i class="fas fa-headset" style="margin-right: 0.5rem;"></i>
                ¡Estamos aquí para ayudarte las 24 horas!
            </div>

            <!-- Nav Buttons - Relocated -->
            <div class="nav-buttons" style="margin-top: 2rem;">
                <a href="{{ route('reportes.historial') }}" class="btn-outline-primary" aria-label="Ver mis reportes enviados">
                    <i class="fas fa-history"></i>Ver Mis Reportes
                </a>
                <a href="{{ route('admin.login') }}" class="btn-primary-custom" aria-label="Iniciar sesión como administrador">
                    <i class="fas fa-sign-in-alt"></i>Iniciar Sesión
                </a>
            </div>
        </div>

        <!-- Right Section - Form -->
        <div class="form-container">
            <div class="form-header">
                <h2 class="form-title">Reportar Incidencia</h2>
                <p class="form-subtitle">Completa los siguientes campos para procesar tu solicitud</p>
            </div>
            <div class="form-content">
                <div class="service-prompt" id="servicePrompt">
                    <i class="fas fa-info-circle"></i>
                    <p>Por favor, selecciona un servicio arriba para habilitar el formulario</p>
                </div>

                <form id="reportForm" method="POST" action="{{ route('reportes.store') }}">
                    @csrf {{-- ¡Directiva de Laravel para el token CSRF! --}}
                    
                    <div class="form-group has-icon">
                        <label for="nombres" class="form-label">
                            <i class="fas fa-user" style="margin-right: 0.5rem; color: #ff6600;"></i>
                            Nombres y Apellidos *
                        </label>
                        <input type="text" class="form-control" id="nombres" name="nombres" placeholder="Ingresa tu nombre completo" required disabled>
                        <i class="fas fa-user input-icon"></i>
                    </div>

                    <div class="form-group has-icon">
                        <label for="correo" class="form-label">
                            <i class="fas fa-envelope" style="margin-right: 0.5rem; color: #ff6600;"></i>
                            Correo Electrónico *
                        </label>
                        <input type="email" class="form-control" id="correo" name="correo" placeholder="ejemplo@correo.com" required disabled>
                        <i class="fas fa-envelope input-icon"></i>
                    </div>

                    <div class="form-group has-icon">
                        <label for="telefono" class="form-label">
                            <i class="fas fa-phone" style="margin-right: 0.5rem; color: #ff6600;"></i>
                            Teléfono/WhatsApp *
                        </label>
                        <input type="tel" class="form-control" id="telefono" name="telefono" required disabled>
                        <i class="fas fa-phone input-icon"></i>
                    </div>

                    <!-- Resumen de selección -->
                    <div class="selection-summary" id="selectionSummary" style="display: none;">
                        <div class="summary-content">
                            <i class="fas fa-info-circle"></i>
                            <p id="summaryText">Reporte para <strong id="summaryServicio">-</strong> del proveedor <strong id="summaryProveedor">-</strong> en <strong id="summaryCiudad">-</strong></p>
                        </div>
                    </div>

                    <!-- Hidden fields -->
                    <input type="hidden" id="servicio_id" name="servicio_id">
                    <input type="hidden" id="ciudad_id" name="ciudad_id">
                    <input type="hidden" id="proveedor_id" name="proveedor_id">

                    <div class="form-group">
                        <label for="descripcion" class="form-label">
                            <i class="fas fa-comment-alt" style="margin-right: 0.5rem; color: #ff6600;"></i>
                            Descripción del Problema *
                        </label>
                        <textarea class="form-control form-textarea" id="descripcion" name="descripcion" rows="3"
                                placeholder="Describe detalladamente el problema..." required disabled></textarea>
                    </div>

                    <div class="form-group has-icon">
                        <label for="direccion" class="form-label">
                            <i class="fas fa-location-dot" style="margin-right: .5rem; color: #ff6600;"></i>
                            Dirección (opcional)
                        </label>
                        <input type="text" class="form-control" id="direccion" name="direccion"
                                placeholder="Calle 26 # 68D-21, apto 301" disabled>
                        <i class="fas fa-map-marker-alt input-icon"></i>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="localidad" class="form-label">Localidad de Bogotá</label>
                            <select class="form-select" id="localidad" name="localidad" disabled>
                                <option value="">Selecciona una localidad</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="barrio" class="form-label">Barrio</label>
                            <select class="form-select" id="barrio" name="barrio" disabled>
                                <option value="">Selecciona un barrio</option>
                            </select>
                        </div>
                    </div>

                    <!-- Hidden location fields - auto-populated on submit -->
                    <input type="hidden" id="latitude" name="latitude">
                    <input type="hidden" id="longitude" name="longitude">

                    <!-- Location feedback (only shown when needed) -->
                    <div class="location-status" id="locationStatus" style="display: none;">
                        <i class="fas fa-info-circle"></i>
                        <span id="locationStatusText"></span>
                    </div>

                    <!-- Map selector (shown only if geolocation fails) -->
                    <div id="mapSelector" style="display: none; margin-top: 1rem;">
                        <div class="form-group">
                            <label for="mapSearch" class="form-label">
                                <i class="fas fa-search" style="margin-right: 0.5rem; color: #ff6600;"></i>
                                Buscar ubicación en el mapa
                            </label>
                            <input type="text" class="form-control" id="mapSearch" placeholder="Busca tu dirección...">
                        </div>
                        <div id="map" style="width: 100%; height: 400px; border-radius: 12px; margin-top: 1rem;"></div>
                        <p style="margin-top: 0.5rem; font-size: 0.9rem; color: #666;">
                            <i class="fas fa-info-circle"></i> Busca tu dirección o haz clic en el mapa para marcar la ubicación
                        </p>
                    </div>

                    <div class="submit-container">
                        <button type="submit" class="submit-btn" id="submitBtn" disabled>
                            <i class="fas fa-paper-plane" style="margin-right: 0.5rem;"></i>
                            Enviar Reporte
                        </button>
                        <div id="result" class="result-message"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de selección de Departamento -->
    <div id="departamentoModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <button class="modal-close-btn" id="closeFromDepartamento">
                    <i class="fas fa-times"></i>
                </button>
                <h2 class="modal-title">
                    <i class="fas fa-map"></i>
                    Selecciona tu Departamento
                </h2>
                <p class="modal-subtitle">¿En qué departamento te encuentras?</p>
            </div>
            <div class="modal-body">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="departamentoSearch" placeholder="Buscar departamento..." autocomplete="off">
                </div>
                <div id="departamentosList" class="departamentos-grid">
                    <!-- Los departamentos se cargarán dinámicamente -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de selección de Ciudad -->
    <div id="ciudadModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <button class="modal-back-btn" id="backFromCiudad">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <h2 class="modal-title">
                    <i class="fas fa-map-marked-alt"></i>
                    Selecciona tu Municipio
                </h2>
                <p class="modal-subtitle" id="departamentoSeleccionado">Departamento: <strong></strong></p>
            </div>
            <div class="modal-body">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="ciudadSearch" placeholder="Buscar municipio..." autocomplete="off">
                </div>
                <div id="ciudadesList" class="ciudades-grid">
                    <!-- Las ciudades se cargarán dinámicamente -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de selección de Proveedor -->
    <div id="proveedorModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <button class="modal-back-btn" id="backFromProveedor">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <h2 class="modal-title">
                    <i class="fas fa-building"></i>
                    Selecciona tu Proveedor
                </h2>
                <p class="modal-subtitle" id="ciudadSeleccionada">Ciudad: <strong></strong></p>
            </div>
            <div class="modal-body">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="proveedorSearch" placeholder="Buscar proveedor..." autocomplete="off">
                </div>
                <div id="proveedoresList" class="proveedores-grid">
                    <!-- Los proveedores se cargarán dinámicamente -->
                </div>
                <button type="button" class="btn-skip" id="skipProveedorBtn">
                    <i class="fas fa-forward"></i>
                    Omitir (No sé mi proveedor)
                </button>
            </div>
        </div>
    </div>

    <!-- Alerta de ubicación -->
    <div id="locationAlert" class="location-alert">
        <div class="alert-content">
            <i class="fas fa-exclamation-triangle"></i>
            <div class="alert-text">
                <strong>¡Atención!</strong>
                <p>Tu ubicación GPS no coincide con la ciudad seleccionada. Por favor, verifica tu ubicación en el mapa.</p>
            </div>
            <button class="alert-close" onclick="closeLocationAlert()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Tus scripts -->
    <!-- Leaflet JS para el mapa -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script src="{{ asset('js/reporte.js') }}"></script>

</body>
</html>