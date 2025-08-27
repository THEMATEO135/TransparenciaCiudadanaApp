<#
 Script para instalar y arrancar n8n en Windows usando npm (sin Docker).
 Asegurate de tener Node.js (LTS recomendado) instalado:
   https://nodejs.org/en/download/
 Uso:
   1. Guarda este archivo como setup_n8n.ps1
   2. Abre PowerShell como Administrador
   3. Ejecuta: Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
   4. Ejecuta: .\setup_n8n.ps1
#>

param(
    [string]$N8N_PORT = "5678",
    [string]$N8N_HOST = "127.0.0.1",
    [string]$N8N_BASIC_AUTH_USER = "admin",
    [string]$N8N_BASIC_AUTH_PASSWORD = "admin_password"
)

# --- Verificar Node.js y npm ---
Write-Host "Verificando Node.js y npm..." -ForegroundColor Cyan

try {
    $nodeVer = & node --version 2>$null
    $npmVer = & npm --version 2>$null
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "Node.js $nodeVer y npm $npmVer detectados." -ForegroundColor Green
    } else {
        throw "Node.js o npm no encontrados"
    }
} catch {
    Write-Host "Node.js no esta instalado. Descargalo en: https://nodejs.org/en/download/" -ForegroundColor Red
    Read-Host "Presiona Enter para salir"
    exit 1
}

# --- Verificar instalacion de n8n ---
Write-Host "Verificando n8n..." -ForegroundColor Cyan

try {
    $n8nCheck = & npm list -g n8n 2>$null
    $n8nInstalled = $LASTEXITCODE -eq 0
} catch {
    $n8nInstalled = $false
}

if (-not $n8nInstalled) {
    Write-Host "Instalando n8n globalmente..." -ForegroundColor Yellow
    try {
        & npm install -g n8n
        if ($LASTEXITCODE -ne 0) {
            throw "Error en la instalacion de n8n"
        }
        Write-Host "n8n instalado correctamente." -ForegroundColor Green
    } catch {
        Write-Host "Error al instalar n8n. Asegurate de ejecutar PowerShell como administrador." -ForegroundColor Red
        Read-Host "Presiona Enter para salir"
        exit 1
    }
} else {
    Write-Host "n8n ya esta instalado." -ForegroundColor Green
}

# --- Configurar variables de entorno ---
Write-Host "Configurando variables de entorno..." -ForegroundColor Cyan

$env:N8N_BASIC_AUTH_ACTIVE = "true"
$env:N8N_BASIC_AUTH_USER = $N8N_BASIC_AUTH_USER
$env:N8N_BASIC_AUTH_PASSWORD = $N8N_BASIC_AUTH_PASSWORD
$env:N8N_PORT = $N8N_PORT
$env:N8N_HOST = $N8N_HOST
$env:WEBHOOK_URL_BASE = "http://$N8N_HOST`:$N8N_PORT"

# --- Arrancar n8n ---
Write-Host "Iniciando n8n en puerto $N8N_PORT ..." -ForegroundColor Yellow

try {
    # Verificar que n8n este disponible
    & n8n --version > $null 2>&1
    if ($LASTEXITCODE -ne 0) {
        throw "n8n no esta disponible en PATH"
    }
    
    # Iniciar n8n en una nueva ventana
    Write-Host "Iniciando n8n en una nueva ventana..." -ForegroundColor Yellow
    Start-Process -FilePath "cmd.exe" -ArgumentList "/c", "n8n" -WindowStyle Normal
    
    # Esperar un momento para que n8n inicie
    Start-Sleep -Seconds 3
    
} catch {
    Write-Host "Error al iniciar n8n: $($_.Exception.Message)" -ForegroundColor Red
    Read-Host "Presiona Enter para salir"
    exit 1
}

Write-Host ""
Write-Host "n8n se esta iniciando..." -ForegroundColor Green
Write-Host "URL de acceso: http://$N8N_HOST`:$N8N_PORT" -ForegroundColor Cyan
Write-Host "Usuario: $N8N_BASIC_AUTH_USER / Password: $N8N_BASIC_AUTH_PASSWORD" -ForegroundColor Cyan
Write-Host ""
Write-Host "Webhook para TransparenciaCiudadana:" -ForegroundColor Yellow
Write-Host "$($env:WEBHOOK_URL_BASE)/webhook/transparencia_webhook" -ForegroundColor White
Write-Host ""
Write-Host "Espera unos segundos y luego abre la URL en tu navegador." -ForegroundColor Green
Write-Host "Para detener n8n, cierra la ventana que se abrio." -ForegroundColor Yellow

Read-Host "Presiona Enter para continuar"