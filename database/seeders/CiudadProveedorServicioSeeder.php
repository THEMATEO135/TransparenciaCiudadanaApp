<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CiudadProveedorServicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Servicios: 1=Energía Eléctrica, 2=Internet, 3=Gas Natural, 4=Acueducto
        // Relaciones: [ciudad_nombre, proveedor_id, servicio_id]

        $relaciones = [
            // ========== BOGOTÁ ==========
            ['Bogotá', 1, 1],   // Codensa (Enel) - Energía
            ['Bogotá', 14, 3],  // Vanti - Gas Natural
            ['Bogotá', 22, 4],  // EAAB - Acueducto
            ['Bogotá', 30, 2],  // Claro - Internet
            ['Bogotá', 31, 2],  // Movistar - Internet
            ['Bogotá', 32, 2],  // Tigo - Internet
            ['Bogotá', 33, 2],  // ETB - Internet
            ['Bogotá', 35, 2],  // WOM - Internet
            ['Bogotá', 36, 2],  // DirecTV - Internet

            // ========== MEDELLÍN ==========
            ['Medellín', 2, 1],   // EPM - Energía
            ['Medellín', 2, 3],   // EPM - Gas Natural
            ['Medellín', 2, 4],   // EPM - Acueducto
            ['Medellín', 30, 2],  // Claro - Internet
            ['Medellín', 31, 2],  // Movistar - Internet
            ['Medellín', 32, 2],  // Tigo - Internet
            ['Medellín', 34, 2],  // UNE - Internet
            ['Medellín', 35, 2],  // WOM - Internet

            // ========== CALI ==========
            ['Cali', 3, 1],   // Celsia - Energía
            ['Cali', 6, 1],   // Emcali - Energía
            ['Cali', 6, 4],   // Emcali - Acueducto
            ['Cali', 15, 3],  // Gases de Occidente - Gas Natural
            ['Cali', 30, 2],  // Claro - Internet
            ['Cali', 31, 2],  // Movistar - Internet
            ['Cali', 32, 2],  // Tigo - Internet
            ['Cali', 35, 2],  // WOM - Internet

            // ========== BARRANQUILLA ==========
            ['Barranquilla', 4, 1],   // Aire - Energía
            ['Barranquilla', 16, 3],  // Gases del Caribe - Gas Natural
            ['Barranquilla', 23, 4],  // Triple A - Acueducto
            ['Barranquilla', 30, 2],  // Claro - Internet
            ['Barranquilla', 31, 2],  // Movistar - Internet
            ['Barranquilla', 32, 2],  // Tigo - Internet
            ['Barranquilla', 35, 2],  // WOM - Internet

            // ========== CARTAGENA ==========
            ['Cartagena', 4, 1],   // Aire - Energía
            ['Cartagena', 18, 3],  // Surtigas - Gas Natural
            ['Cartagena', 25, 4],  // Acuacar - Acueducto
            ['Cartagena', 30, 2],  // Claro - Internet
            ['Cartagena', 31, 2],  // Movistar - Internet
            ['Cartagena', 32, 2],  // Tigo - Internet

            // ========== BUCARAMANGA ==========
            ['Bucaramanga', 5, 1],   // ESSA - Energía
            ['Bucaramanga', 17, 3],  // Efigas - Gas Natural
            ['Bucaramanga', 19, 3],  // Gasoriente - Gas Natural
            ['Bucaramanga', 29, 4],  // Acuaviva - Acueducto
            ['Bucaramanga', 30, 2],  // Claro - Internet
            ['Bucaramanga', 31, 2],  // Movistar - Internet
            ['Bucaramanga', 32, 2],  // Tigo - Internet
            ['Bucaramanga', 35, 2],  // WOM - Internet

            // ========== CÚCUTA ==========
            ['Cúcuta', 10, 1],  // CENS - Energía
            ['Cúcuta', 17, 3],  // Efigas - Gas Natural
            ['Cúcuta', 30, 2],  // Claro - Internet
            ['Cúcuta', 31, 2],  // Movistar - Internet
            ['Cúcuta', 32, 2],  // Tigo - Internet

            // ========== PEREIRA ==========
            ['Pereira', 7, 1],   // CHEC - Energía
            ['Pereira', 15, 3],  // Gases de Occidente - Gas Natural
            ['Pereira', 30, 2],  // Claro - Internet
            ['Pereira', 31, 2],  // Movistar - Internet
            ['Pereira', 32, 2],  // Tigo - Internet

            // ========== SANTA MARTA ==========
            ['Santa Marta', 4, 1],   // Aire - Energía
            ['Santa Marta', 16, 3],  // Gases del Caribe - Gas Natural
            ['Santa Marta', 30, 2],  // Claro - Internet
            ['Santa Marta', 31, 2],  // Movistar - Internet
            ['Santa Marta', 32, 2],  // Tigo - Internet

            // ========== NEIVA ==========
            ['Neiva', 8, 1],   // Electrohuila - Energía
            ['Neiva', 24, 4],  // Aguas del Huila - Acueducto
            ['Neiva', 30, 2],  // Claro - Internet
            ['Neiva', 31, 2],  // Movistar - Internet
            ['Neiva', 32, 2],  // Tigo - Internet
            ['Neiva', 33, 2],  // ETB - Internet

            // ========== ARMENIA ==========
            ['Armenia', 9, 1],   // Edeq - Energía
            ['Armenia', 30, 2],  // Claro - Internet
            ['Armenia', 31, 2],  // Movistar - Internet
            ['Armenia', 32, 2],  // Tigo - Internet

            // ========== MANIZALES ==========
            ['Manizales', 7, 1],   // CHEC - Energía
            ['Manizales', 26, 4],  // Aguas de Manizales - Acueducto
            ['Manizales', 30, 2],  // Claro - Internet
            ['Manizales', 31, 2],  // Movistar - Internet
            ['Manizales', 32, 2],  // Tigo - Internet

            // ========== PASTO ==========
            ['Pasto', 12, 1],  // CEDENAR - Energía
            ['Pasto', 27, 4],  // EMPAS - Acueducto
            ['Pasto', 30, 2],  // Claro - Internet
            ['Pasto', 31, 2],  // Movistar - Internet
            ['Pasto', 32, 2],  // Tigo - Internet

            // ========== VILLAVICENCIO ==========
            ['Villavicencio', 20, 3],  // Llanogas - Gas Natural
            ['Villavicencio', 30, 2],  // Claro - Internet
            ['Villavicencio', 31, 2],  // Movistar - Internet
            ['Villavicencio', 32, 2],  // Tigo - Internet

            // ========== IBAGUÉ ==========
            ['Ibagué', 30, 2],  // Claro - Internet
            ['Ibagué', 31, 2],  // Movistar - Internet
            ['Ibagué', 32, 2],  // Tigo - Internet

            // ========== BUGA ==========
            ['Buga', 3, 1],   // Celsia - Energía
            ['Buga', 28, 4],  // Aguas de Buga - Acueducto
            ['Buga', 30, 2],  // Claro - Internet
            ['Buga', 31, 2],  // Movistar - Internet

            // ========== OTRAS CIUDADES - INTERNET NACIONAL ==========
            ['Montería', 30, 2],
            ['Montería', 31, 2],
            ['Montería', 32, 2],
            ['Valledupar', 30, 2],
            ['Valledupar', 31, 2],
            ['Valledupar', 32, 2],
            ['Popayán', 30, 2],
            ['Popayán', 31, 2],
            ['Tunja', 30, 2],
            ['Tunja', 31, 2],
            ['Florencia', 30, 2],
            ['Florencia', 31, 2],
            ['Sincelejo', 30, 2],
            ['Sincelejo', 31, 2],

            // ========== COSTA ATLÁNTICA - AIRE ENERGÍA ==========
            ['Soledad', 4, 1],
            ['Malambo', 4, 1],
            ['Valledupar', 4, 1],
            ['Montería', 4, 1],
            ['Sincelejo', 4, 1],

            // ========== BOYACÁ - EBSA ==========
            ['Tunja', 11, 1],
            ['Duitama', 11, 1],
            ['Sogamoso', 11, 1],

            // ========== CAQUETÁ - DISPAC ==========
            ['Florencia', 13, 1],

            // ========== INTERNET EPM ZONA ==========
            ['Bello', 34, 2],
            ['Envigado', 34, 2],
            ['Itagüí', 34, 2],
            ['Sabaneta', 34, 2],
        ];

        foreach ($relaciones as $relacion) {
            $ciudad = \App\Models\Ciudad::where('nombre', $relacion[0])->first();

            if ($ciudad) {
                DB::table('ciudad_proveedor_servicio')->insert([
                    'ciudad_id' => $ciudad->id,
                    'proveedor_id' => $relacion[1],
                    'servicio_id' => $relacion[2],
                    'estado' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
