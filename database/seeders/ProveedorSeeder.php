<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Proveedor;

class ProveedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $proveedores = [
            // ========== ENERGÍA ELÉCTRICA ==========
            ['nombre' => 'Codensa (Enel)', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/84/Enel_2016.svg/512px-Enel_2016.svg.png', 'sitio_web' => 'https://www.enel.com.co', 'telefono' => '115', 'email' => 'atencioncliente@enel.com', 'descripcion' => 'Distribuidora de energía en Bogotá y Cundinamarca'],
            ['nombre' => 'EPM', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6a/Logo_EPM.svg/512px-Logo_EPM.svg.png', 'sitio_web' => 'https://www.epm.com.co', 'telefono' => '44 44 115', 'email' => 'contacto@epm.com.co', 'descripcion' => 'Empresas Públicas de Medellín - Energía, Agua y Gas'],
            ['nombre' => 'Celsia', 'logo_url' => 'https://www.celsia.com/wp-content/uploads/2021/05/Grupo-697.svg', 'sitio_web' => 'https://www.celsia.com', 'telefono' => '01 8000 115 115', 'email' => 'contacto@celsia.com', 'descripcion' => 'Energía renovable en Valle del Cauca, Cauca y Tolima'],
            ['nombre' => 'Aire', 'logo_url' => 'https://www.aire.com/sites/default/files/2021-06/logo-aire.svg', 'sitio_web' => 'https://www.aire.com', 'telefono' => '115', 'email' => 'info@aire.com', 'descripcion' => 'Distribuidora de energía en la Costa Atlántica'],
            ['nombre' => 'ESSA', 'logo_url' => 'https://www.essa.com.co/SiteAssets/img/logo-essa-header.svg', 'sitio_web' => 'https://www.essa.com.co', 'telefono' => '018000 915 115', 'email' => 'servicioalcliente@essa.com.co', 'descripcion' => 'Electrificadora de Santander'],
            ['nombre' => 'Emcali', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/91/Emcali_logo.svg/512px-Emcali_logo.svg.png', 'sitio_web' => 'https://www.emcali.com.co', 'telefono' => '195', 'email' => 'contacto@emcali.com.co', 'descripcion' => 'Empresa Municipal de Cali - Energía, Agua y Telecomunicaciones'],
            ['nombre' => 'CHEC', 'logo_url' => 'https://www.chec.com.co/sites/default/files/2023-04/logo-chec.svg', 'sitio_web' => 'https://www.chec.com.co', 'telefono' => '018000 510 909', 'email' => 'atencioncliente@chec.com.co', 'descripcion' => 'Central Hidroeléctrica de Caldas'],
            ['nombre' => 'Electrohuila', 'logo_url' => 'https://electrohuila.net/wp-content/uploads/2023/05/logo-electrohuila.svg', 'sitio_web' => 'https://www.electrohuila.net', 'telefono' => '018000 936 115', 'email' => 'info@electrohuila.net', 'descripcion' => 'Electrificadora del Huila'],
            ['nombre' => 'Edeq', 'logo_url' => 'https://www.edeq.com.co/Portals/_default/Skins/Edeq2023/img/logo-edeq.svg', 'sitio_web' => 'https://www.edeq.com.co', 'telefono' => '116', 'email' => 'contacto@edeq.com.co', 'descripcion' => 'Empresa de Energía del Quindío'],
            ['nombre' => 'CENS', 'logo_url' => 'https://www.cens.com.co/wp-content/uploads/2023/01/logo-cens.png', 'sitio_web' => 'https://www.cens.com.co', 'telefono' => '116', 'email' => 'info@cens.com.co', 'descripcion' => 'Centrales Eléctricas del Norte de Santander'],
            ['nombre' => 'EBSA', 'logo_url' => 'https://www.ebsa.com.co/sitio/images/logo-ebsa.svg', 'sitio_web' => 'https://www.ebsa.com.co', 'telefono' => '018000 918 001', 'email' => 'contacto@ebsa.com.co', 'descripcion' => 'Electrificadora de Boyacá'],
            ['nombre' => 'CEDENAR', 'logo_url' => 'https://www.cedenar.com/wp-content/uploads/2022/07/logo-cedenar.png', 'sitio_web' => 'https://www.cedenar.com', 'telefono' => '116', 'email' => 'info@cedenar.com', 'descripcion' => 'Centrales Eléctricas de Nariño'],
            ['nombre' => 'Dispac', 'logo_url' => 'https://www.dispac.com.co/wp-content/uploads/2023/02/logo-dispac.png', 'sitio_web' => 'https://www.dispac.com.co', 'telefono' => '116', 'email' => 'contacto@dispac.com.co', 'descripcion' => 'Distribuidora del Pacífico - Caquetá'],

            // ========== GAS NATURAL ==========
            ['nombre' => 'Vanti', 'logo_url' => 'https://www.vanti.com.co/themes/custom/vanti_theme/logo.svg', 'sitio_web' => 'https://www.vanti.com.co', 'telefono' => '164', 'email' => 'info@vanti.com.co', 'descripcion' => 'Distribuidora de gas natural en Bogotá y Cundinamarca'],
            ['nombre' => 'Gases de Occidente', 'logo_url' => 'https://www.gasesdeoccidente.com/wp-content/uploads/2023/03/logo-gases-occidente.svg', 'sitio_web' => 'https://www.gasesdeoccidente.com', 'telefono' => '116', 'email' => 'info@gasesdeoccidente.com', 'descripcion' => 'Distribución de gas en Valle del Cauca'],
            ['nombre' => 'Gases del Caribe', 'logo_url' => 'https://www.gascaribe.com/themes/custom/gascaribe/logo.svg', 'sitio_web' => 'https://www.gascaribe.com', 'telefono' => '018000 180 164', 'email' => 'contacto@gascaribe.com', 'descripcion' => 'Distribución de gas en la Costa Caribe'],
            ['nombre' => 'Efigas', 'logo_url' => 'https://www.efigas.com.co/wp-content/uploads/2023/01/logo-efigas.svg', 'sitio_web' => 'https://www.efigas.com.co', 'telefono' => '01 8000 115 115', 'email' => 'contacto@efigas.com.co', 'descripcion' => 'Empresa Ferre de Gas del Oriente - Santander'],
            ['nombre' => 'Surtigas', 'logo_url' => 'https://www.surtigas.com.co/Portals/_default/Skins/SurtigasSkin/img/logo.svg', 'sitio_web' => 'https://www.surtigas.com.co', 'telefono' => '116', 'email' => 'servicioalcliente@surtigas.com.co', 'descripcion' => 'Distribuidora de gas en Cartagena y Bolívar'],
            ['nombre' => 'Gasoriente', 'logo_url' => 'https://www.gasoriente.com/wp-content/uploads/2023/02/logo-gasoriente.png', 'sitio_web' => 'https://www.gasoriente.com', 'telefono' => '116', 'email' => 'info@gasoriente.com', 'descripcion' => 'Gas natural en Santander'],
            ['nombre' => 'Llanogas', 'logo_url' => 'https://www.llanogas.com.co/wp-content/uploads/2022/11/logo-llanogas.png', 'sitio_web' => 'https://www.llanogas.com.co', 'telefono' => '116', 'email' => 'contacto@llanogas.com.co', 'descripcion' => 'Distribuidora de gas en los Llanos Orientales'],
            ['nombre' => 'Gases de Barrancabermeja', 'logo_url' => 'https://www.gasesdebarrancabermeja.com/wp-content/uploads/2023/01/logo.png', 'sitio_web' => 'https://www.gasesdebarrancabermeja.com', 'telefono' => '116', 'email' => 'info@gasesdebarrancabermeja.com', 'descripcion' => 'Gas natural en Barrancabermeja'],

            // ========== ACUEDUCTO Y ALCANTARILLADO ==========
            ['nombre' => 'EAAB', 'logo_url' => 'https://www.acueducto.com.co/wps/wcm/connect/EAB/d8c8c8c8-c8c8-c8c8-c8c8-c8c8c8c8c8c8/logo-acueducto.png', 'sitio_web' => 'https://www.acueducto.com.co', 'telefono' => '116', 'email' => 'correspondencia@acueducto.com.co', 'descripcion' => 'Empresa de Acueducto y Alcantarillado de Bogotá'],
            ['nombre' => 'Triple A', 'logo_url' => 'https://www.aaa.com.co/wp-content/uploads/2023/06/logo-triple-a.svg', 'sitio_web' => 'https://www.aaa.com.co', 'telefono' => '116', 'email' => 'servicioalcliente@aaa.com.co', 'descripcion' => 'Acueducto de Barranquilla'],
            ['nombre' => 'Aguas del Huila', 'logo_url' => 'https://www.lasceibas.gov.co/wp-content/uploads/2023/01/logo-las-ceibas.svg', 'sitio_web' => 'https://www.lasceibas.gov.co', 'telefono' => '018000 115 115', 'email' => 'contacto@lasceibas.gov.co', 'descripcion' => 'Empresa de Acueducto de Neiva y Huila'],
            ['nombre' => 'Acuacar', 'logo_url' => 'https://www.acuacar.com/Portals/_default/Skins/AcuacarSkin/img/logo-acuacar.svg', 'sitio_web' => 'https://www.acuacar.com', 'telefono' => '116', 'email' => 'info@acuacar.com', 'descripcion' => 'Aguas de Cartagena'],
            ['nombre' => 'Aguas de Manizales', 'logo_url' => 'https://www.aguasdemanizales.com.co/wp-content/uploads/2023/01/logo-aguas-manizales.svg', 'sitio_web' => 'https://www.aguasdemanizales.com.co', 'telefono' => '116', 'email' => 'contacto@aguasdemanizales.com.co', 'descripcion' => 'Acueducto y Alcantarillado de Manizales'],
            ['nombre' => 'EMPAS', 'logo_url' => 'https://www.empopasto.gov.co/wp-content/uploads/2023/02/logo-empas.png', 'sitio_web' => 'https://www.empopasto.gov.co', 'telefono' => '116', 'email' => 'info@empopasto.gov.co', 'descripcion' => 'Empresa Metropolitana de Aseo de Pasto'],
            ['nombre' => 'Aguas de Buga', 'logo_url' => 'https://www.aguasdebuga.net/wp-content/uploads/2022/12/logo-aguas-buga.png', 'sitio_web' => 'https://www.aguasdebuga.net', 'telefono' => '116', 'email' => 'info@aguasdebuga.net', 'descripcion' => 'Acueducto de Guadalajara de Buga'],
            ['nombre' => 'Acuaviva', 'logo_url' => 'https://www.acuaviva.gov.co/wp-content/uploads/2023/01/logo-acuaviva.png', 'sitio_web' => 'https://www.acuaviva.gov.co', 'telefono' => '116', 'email' => 'contacto@acuaviva.gov.co', 'descripcion' => 'Acueducto de Bucaramanga'],

            // ========== INTERNET Y TELECOMUNICACIONES ==========
            ['nombre' => 'Claro', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/91/Claro.svg/512px-Claro.svg.png', 'sitio_web' => 'https://www.claro.com.co', 'telefono' => '01 800 091 1200', 'email' => 'contacto@claro.com.co', 'descripcion' => 'Operador líder de telecomunicaciones en Colombia'],
            ['nombre' => 'Movistar', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e0/Movistar.svg/512px-Movistar.svg.png', 'sitio_web' => 'https://www.movistar.co', 'telefono' => '01 8000 915 000', 'email' => 'contacto@movistar.com.co', 'descripcion' => 'Telefonía, internet y televisión'],
            ['nombre' => 'Tigo', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f8/Tigo_logo.svg/512px-Tigo_logo.svg.png', 'sitio_web' => 'https://www.tigo.com.co', 'telefono' => '01 8000 110 000', 'email' => 'contacto@tigo.com.co', 'descripcion' => 'Internet, telefonía móvil y televisión'],
            ['nombre' => 'ETB', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a0/ETB_logo.svg/512px-ETB_logo.svg.png', 'sitio_web' => 'https://www.etb.com.co', 'telefono' => '01 8000 11 22 11', 'email' => 'info@etb.com.co', 'descripcion' => 'Empresa de Telecomunicaciones de Bogotá'],
            ['nombre' => 'UNE', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f8/Tigo_logo.svg/512px-Tigo_logo.svg.png', 'sitio_web' => 'https://www.tigo.com.co/une', 'telefono' => '01 8000 110 000', 'email' => 'contacto@une.net.co', 'descripcion' => 'Telecomunicaciones UNE - EPM'],
            ['nombre' => 'WOM', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/68/WOM_Logo.svg/512px-WOM_Logo.svg.png', 'sitio_web' => 'https://www.wom.co', 'telefono' => '01 8000 916 100', 'email' => 'contacto@wom.co', 'descripcion' => 'Operador móvil y de internet'],
            ['nombre' => 'DirecTV', 'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4d/DirecTV_logo.svg/512px-DirecTV_logo.svg.png', 'sitio_web' => 'https://www.directv.com.co', 'telefono' => '01 8000 514 814', 'email' => 'info@directv.com.co', 'descripcion' => 'Televisión satelital e internet'],
        ];

        foreach ($proveedores as $proveedor) {
            Proveedor::create($proveedor);
        }
    }
}
