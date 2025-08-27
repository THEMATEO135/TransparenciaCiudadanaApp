# TransparenciaCiudadana

Proyecto demo en **PHP + SQLite** que guarda reportes ciudadanos y notifica mediante **n8n** (WhatsApp + correo).
Incluye:
- API PHP para crear reportes (`api/save_report.php`)
- Inicializador de base de datos SQLite (`api/init_db.php`) y archivo `data/transparencia.sqlite`
- Frontend mínimo en `public/index.html`
- Workflow de n8n exportable `n8n/transparencia_workflow.json`
- Script PowerShell para desplegar n8n en Docker y configurarlo `setup_n8n.ps1`

**Instrucciones rápidas**
1. Coloca el ZIP en tu servidor y extrae `TransparenciaCiudadana`.
2. Asegúrate de tener PHP 8+, SQLite y un servidor web (Apache/Nginx). Configura `public` como carpeta pública.
3. Ejecuta `php api/init_db.php` para crear la base de datos (también viene ya creada).
4. Ajusta variables en `api/config.php` (RUTA_DB, N8N_WEBHOOK_URL).
5. Importa `n8n/transparencia_workflow.json` en tu instancia de n8n.
6. Para levantar n8n con Docker desde PowerShell, ejecuta `.\setup_n8n.ps1` (revisa y ajusta variables).

**Archivos importantes**
- `api/save_report.php` — endpoint POST JSON para crear reportes y llamar a n8n.
- `public/index.html` — formulario de ejemplo.
- `n8n/transparencia_workflow.json` — workflow que envía email y WhatsApp usando HTTP Request.

