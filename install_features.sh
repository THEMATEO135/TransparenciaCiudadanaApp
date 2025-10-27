#!/bin/bash

echo "🚀 Instalando Nuevas Funcionalidades - Transparencia Ciudadana"
echo "=============================================================="

# Colores
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}Paso 1: Instalando dependencias de Composer...${NC}"
composer require predis/predis
composer require laravel/horizon
composer require intervention/image
composer require php-ai/php-ml

echo -e "${GREEN}✓ Dependencias instaladas${NC}"

echo -e "${BLUE}Paso 2: Publicando configuración de Horizon...${NC}"
php artisan horizon:install

echo -e "${GREEN}✓ Horizon configurado${NC}"

echo -e "${BLUE}Paso 3: Ejecutando migraciones...${NC}"
php artisan migrate --force

echo -e "${GREEN}✓ Migraciones ejecutadas${NC}"

echo -e "${BLUE}Paso 4: Creando storage link...${NC}"
php artisan storage:link

echo -e "${GREEN}✓ Storage link creado${NC}"

echo -e "${BLUE}Paso 5: Ejecutando seeders...${NC}"
php artisan db:seed --class=PlantillasRespuestaSeeder
php artisan db:seed --class=AdminUsersSeeder

echo -e "${GREEN}✓ Seeders ejecutados${NC}"

echo -e "${BLUE}Paso 6: Limpiando caché...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo -e "${GREEN}✓ Caché limpiado${NC}"

echo -e "${YELLOW}Paso 7: Generando clave de aplicación si es necesario...${NC}"
php artisan key:generate

echo ""
echo -e "${GREEN}=============================================================="
echo "✅ INSTALACIÓN COMPLETADA"
echo "=============================================================="
echo ""
echo "Próximos pasos:"
echo "1. Configurar Redis en .env (REDIS_HOST, REDIS_PORT)"
echo "2. Configurar QUEUE_CONNECTION=redis en .env"
echo "3. Ejecutar: php artisan horizon"
echo "4. Ejecutar: php artisan queue:work (en otra terminal)"
echo ""
echo "Comandos disponibles:"
echo "- php artisan reportes:calcular-prioridades"
echo "- php artisan predicciones:generar"
echo "- php artisan reportes:detectar-duplicados"
echo "- php artisan reportes-estadisticos:ejecutar"
echo ""
echo -e "${GREEN}¡Disfruta de las nuevas funcionalidades!${NC}"
