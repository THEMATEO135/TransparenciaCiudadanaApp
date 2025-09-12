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
 <div class="services-grid">
    <div class="service-card" onclick="selectService('energia', event)">
        <i class="fas fa-bolt service-icon"></i>
        <div class="service-title">Energía Eléctrica</div>
    </div>
    <div class="service-card" onclick="selectService('internet', event)">
        <i class="fas fa-wifi service-icon"></i>
        <div class="service-title">Internet</div>
    </div>
    <div class="service-card" onclick="selectService('gas', event)">
        <i class="fas fa-fire service-icon"></i>
        <div class="service-title">Gas Natural</div>
    </div>
    <div class="service-card" onclick="selectService('agua', event)">
        <i class="fas fa-tint service-icon"></i>
        <div class="service-title">Acueducto</div>
    </div>
</div>
            <div class="cta-text">
                <i class="fas fa-headset" style="margin-right: 0.5rem;"></i>
                ¡Estamos aquí para ayudarte las 24 horas!
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

                <form id="reportForm" method="POST">
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
                        <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="+57 300 123 4567" required disabled>
                        <i class="fas fa-phone input-icon"></i>
                    </div>

                    <div class="form-group">
                        <label for="servicio_id" class="form-label">
                            <i class="fas fa-cogs" style="margin-right: 0.5rem; color: #ff6600;"></i>
                            Tipo de Servicio *
                        </label>
                     <select class="form-select" id="servicio_id" name="servicio_id" required disabled>
    <option value="">Selecciona el servicio afectado</option>
    <option value="1">⚡ Energía Eléctrica</option>
    <option value="2">📶 Internet</option>
    <option value="3">🔥 Gas Natural</option>
    <option value="4">💧 Acueducto</option>
</select>
                    </div>

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

                    <div class="location-section">
                        <div class="location-header">
                            <span class="location-title">
                                <i class="fas fa-map-marker-alt" style="margin-right: 0.5rem; color: #ff6600;"></i>
                                Ubicación (Opcional)
                            </span>
                            <button type="button" class="get-location-btn" id="getLocationBtn" onclick="getCurrentLocation(event)" disabled>
                                <i class="fas fa-crosshairs"></i>
                                Obtener ubicación
                            </button>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="latitude" class="form-label">Latitud</label>
                                <input type="text" class="form-control" id="latitude" name="latitude" placeholder="Auto-detectar" readonly disabled>
                            </div>
                            <div class="form-group">
                                <label for="longitude" class="form-label">Longitud</label>
                                <input type="text" class="form-control" id="longitude" name="longitude" placeholder="Auto-detectar" readonly disabled>
                            </div>
                        </div>
                        <div class="location-status" id="locationStatus">
                            <i class="fas fa-info-circle"></i>
                            Presiona "Obtener ubicación" para incluir tu ubicación actual
                        </div>
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

    <!-- Tus scripts -->

<script src="{{ asset('js/reporte.js') }}"></script>

</body>
</html>