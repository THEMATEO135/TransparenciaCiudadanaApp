<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ciudad;

class CiudadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ciudades = [
            // Amazonas
            ['nombre' => 'Leticia', 'departamento' => 'Amazonas', 'codigo_dane' => '91001'],
            ['nombre' => 'Puerto Nariño', 'departamento' => 'Amazonas', 'codigo_dane' => '91540'],

            // Antioquia
            ['nombre' => 'Medellín', 'departamento' => 'Antioquia', 'codigo_dane' => '05001'],
            ['nombre' => 'Bello', 'departamento' => 'Antioquia', 'codigo_dane' => '05088'],
            ['nombre' => 'Itagüí', 'departamento' => 'Antioquia', 'codigo_dane' => '05360'],
            ['nombre' => 'Envigado', 'departamento' => 'Antioquia', 'codigo_dane' => '05266'],
            ['nombre' => 'Apartadó', 'departamento' => 'Antioquia', 'codigo_dane' => '05045'],
            ['nombre' => 'Turbo', 'departamento' => 'Antioquia', 'codigo_dane' => '05837'],
            ['nombre' => 'Rionegro', 'departamento' => 'Antioquia', 'codigo_dane' => '05615'],

            // Arauca
            ['nombre' => 'Arauca', 'departamento' => 'Arauca', 'codigo_dane' => '81001'],
            ['nombre' => 'Tame', 'departamento' => 'Arauca', 'codigo_dane' => '81794'],
            ['nombre' => 'Saravena', 'departamento' => 'Arauca', 'codigo_dane' => '81736'],

            // Atlántico
            ['nombre' => 'Barranquilla', 'departamento' => 'Atlántico', 'codigo_dane' => '08001'],
            ['nombre' => 'Soledad', 'departamento' => 'Atlántico', 'codigo_dane' => '08758'],
            ['nombre' => 'Malambo', 'departamento' => 'Atlántico', 'codigo_dane' => '08433'],
            ['nombre' => 'Sabanalarga', 'departamento' => 'Atlántico', 'codigo_dane' => '08634'],

            // Bolívar
            ['nombre' => 'Cartagena', 'departamento' => 'Bolívar', 'codigo_dane' => '13001'],
            ['nombre' => 'Magangué', 'departamento' => 'Bolívar', 'codigo_dane' => '13430'],
            ['nombre' => 'Turbaco', 'departamento' => 'Bolívar', 'codigo_dane' => '13838'],

            // Boyacá
            ['nombre' => 'Tunja', 'departamento' => 'Boyacá', 'codigo_dane' => '15001'],
            ['nombre' => 'Duitama', 'departamento' => 'Boyacá', 'codigo_dane' => '15238'],
            ['nombre' => 'Sogamoso', 'departamento' => 'Boyacá', 'codigo_dane' => '15759'],
            ['nombre' => 'Chiquinquirá', 'departamento' => 'Boyacá', 'codigo_dane' => '15176'],

            // Caldas
            ['nombre' => 'Manizales', 'departamento' => 'Caldas', 'codigo_dane' => '17001'],
            ['nombre' => 'Villamaría', 'departamento' => 'Caldas', 'codigo_dane' => '17873'],
            ['nombre' => 'La Dorada', 'departamento' => 'Caldas', 'codigo_dane' => '17380'],

            // Caquetá
            ['nombre' => 'Florencia', 'departamento' => 'Caquetá', 'codigo_dane' => '18001'],
            ['nombre' => 'San Vicente del Caguán', 'departamento' => 'Caquetá', 'codigo_dane' => '18753'],

            // Casanare
            ['nombre' => 'Yopal', 'departamento' => 'Casanare', 'codigo_dane' => '85001'],
            ['nombre' => 'Aguazul', 'departamento' => 'Casanare', 'codigo_dane' => '85010'],
            ['nombre' => 'Villanueva', 'departamento' => 'Casanare', 'codigo_dane' => '85440'],

            // Cauca
            ['nombre' => 'Popayán', 'departamento' => 'Cauca', 'codigo_dane' => '19001'],
            ['nombre' => 'Santander de Quilichao', 'departamento' => 'Cauca', 'codigo_dane' => '19698'],

            // Cesar
            ['nombre' => 'Valledupar', 'departamento' => 'Cesar', 'codigo_dane' => '20001'],
            ['nombre' => 'Aguachica', 'departamento' => 'Cesar', 'codigo_dane' => '20011'],
            ['nombre' => 'Bosconia', 'departamento' => 'Cesar', 'codigo_dane' => '20175'],

            // Chocó
            ['nombre' => 'Quibdó', 'departamento' => 'Chocó', 'codigo_dane' => '27001'],
            ['nombre' => 'Istmina', 'departamento' => 'Chocó', 'codigo_dane' => '27361'],

            // Córdoba
            ['nombre' => 'Montería', 'departamento' => 'Córdoba', 'codigo_dane' => '23001'],
            ['nombre' => 'Lorica', 'departamento' => 'Córdoba', 'codigo_dane' => '23417'],
            ['nombre' => 'Cereté', 'departamento' => 'Córdoba', 'codigo_dane' => '23162'],
            ['nombre' => 'Sahagún', 'departamento' => 'Córdoba', 'codigo_dane' => '23660'],

            // Cundinamarca
            ['nombre' => 'Bogotá', 'departamento' => 'Cundinamarca', 'codigo_dane' => '11001'],
            ['nombre' => 'Soacha', 'departamento' => 'Cundinamarca', 'codigo_dane' => '25754'],
            ['nombre' => 'Facatativá', 'departamento' => 'Cundinamarca', 'codigo_dane' => '25269'],
            ['nombre' => 'Zipaquirá', 'departamento' => 'Cundinamarca', 'codigo_dane' => '25899'],
            ['nombre' => 'Chía', 'departamento' => 'Cundinamarca', 'codigo_dane' => '25175'],
            ['nombre' => 'Fusagasugá', 'departamento' => 'Cundinamarca', 'codigo_dane' => '25290'],
            ['nombre' => 'Girardot', 'departamento' => 'Cundinamarca', 'codigo_dane' => '25307'],

            // Guainía
            ['nombre' => 'Inírida', 'departamento' => 'Guainía', 'codigo_dane' => '94001'],

            // Guaviare
            ['nombre' => 'San José del Guaviare', 'departamento' => 'Guaviare', 'codigo_dane' => '95001'],

            // Huila
            ['nombre' => 'Neiva', 'departamento' => 'Huila', 'codigo_dane' => '41001'],
            ['nombre' => 'Pitalito', 'departamento' => 'Huila', 'codigo_dane' => '41551'],
            ['nombre' => 'Garzón', 'departamento' => 'Huila', 'codigo_dane' => '41298'],

            // La Guajira
            ['nombre' => 'Riohacha', 'departamento' => 'La Guajira', 'codigo_dane' => '44001'],
            ['nombre' => 'Maicao', 'departamento' => 'La Guajira', 'codigo_dane' => '44430'],
            ['nombre' => 'Uribia', 'departamento' => 'La Guajira', 'codigo_dane' => '44847'],

            // Magdalena
            ['nombre' => 'Santa Marta', 'departamento' => 'Magdalena', 'codigo_dane' => '47001'],
            ['nombre' => 'Ciénaga', 'departamento' => 'Magdalena', 'codigo_dane' => '47189'],
            ['nombre' => 'Fundación', 'departamento' => 'Magdalena', 'codigo_dane' => '47288'],

            // Meta
            ['nombre' => 'Villavicencio', 'departamento' => 'Meta', 'codigo_dane' => '50001'],
            ['nombre' => 'Acacías', 'departamento' => 'Meta', 'codigo_dane' => '50006'],
            ['nombre' => 'Granada', 'departamento' => 'Meta', 'codigo_dane' => '50313'],

            // Nariño
            ['nombre' => 'Pasto', 'departamento' => 'Nariño', 'codigo_dane' => '52001'],
            ['nombre' => 'Tumaco', 'departamento' => 'Nariño', 'codigo_dane' => '52835'],
            ['nombre' => 'Ipiales', 'departamento' => 'Nariño', 'codigo_dane' => '52356'],

            // Norte de Santander
            ['nombre' => 'Cúcuta', 'departamento' => 'Norte de Santander', 'codigo_dane' => '54001'],
            ['nombre' => 'Ocaña', 'departamento' => 'Norte de Santander', 'codigo_dane' => '54498'],
            ['nombre' => 'Pamplona', 'departamento' => 'Norte de Santander', 'codigo_dane' => '54518'],

            // Putumayo
            ['nombre' => 'Mocoa', 'departamento' => 'Putumayo', 'codigo_dane' => '86001'],
            ['nombre' => 'Puerto Asís', 'departamento' => 'Putumayo', 'codigo_dane' => '86568'],

            // Quindío
            ['nombre' => 'Armenia', 'departamento' => 'Quindío', 'codigo_dane' => '63001'],
            ['nombre' => 'Calarcá', 'departamento' => 'Quindío', 'codigo_dane' => '63130'],
            ['nombre' => 'La Tebaida', 'departamento' => 'Quindío', 'codigo_dane' => '63401'],

            // Risaralda
            ['nombre' => 'Pereira', 'departamento' => 'Risaralda', 'codigo_dane' => '66001'],
            ['nombre' => 'Dosquebradas', 'departamento' => 'Risaralda', 'codigo_dane' => '66170'],
            ['nombre' => 'Santa Rosa de Cabal', 'departamento' => 'Risaralda', 'codigo_dane' => '66682'],

            // San Andrés y Providencia
            ['nombre' => 'San Andrés', 'departamento' => 'San Andrés y Providencia', 'codigo_dane' => '88001'],
            ['nombre' => 'Providencia', 'departamento' => 'San Andrés y Providencia', 'codigo_dane' => '88564'],

            // Santander
            ['nombre' => 'Bucaramanga', 'departamento' => 'Santander', 'codigo_dane' => '68001'],
            ['nombre' => 'Floridablanca', 'departamento' => 'Santander', 'codigo_dane' => '68276'],
            ['nombre' => 'Girón', 'departamento' => 'Santander', 'codigo_dane' => '68307'],
            ['nombre' => 'Barrancabermeja', 'departamento' => 'Santander', 'codigo_dane' => '68081'],
            ['nombre' => 'Piedecuesta', 'departamento' => 'Santander', 'codigo_dane' => '68547'],

            // Sucre
            ['nombre' => 'Sincelejo', 'departamento' => 'Sucre', 'codigo_dane' => '70001'],
            ['nombre' => 'Corozal', 'departamento' => 'Sucre', 'codigo_dane' => '70215'],

            // Tolima
            ['nombre' => 'Ibagué', 'departamento' => 'Tolima', 'codigo_dane' => '73001'],
            ['nombre' => 'Espinal', 'departamento' => 'Tolima', 'codigo_dane' => '73268'],
            ['nombre' => 'Melgar', 'departamento' => 'Tolima', 'codigo_dane' => '73449'],

            // Valle del Cauca
            ['nombre' => 'Cali', 'departamento' => 'Valle del Cauca', 'codigo_dane' => '76001'],
            ['nombre' => 'Palmira', 'departamento' => 'Valle del Cauca', 'codigo_dane' => '76520'],
            ['nombre' => 'Buenaventura', 'departamento' => 'Valle del Cauca', 'codigo_dane' => '76109'],
            ['nombre' => 'Tuluá', 'departamento' => 'Valle del Cauca', 'codigo_dane' => '76834'],
            ['nombre' => 'Cartago', 'departamento' => 'Valle del Cauca', 'codigo_dane' => '76147'],
            ['nombre' => 'Buga', 'departamento' => 'Valle del Cauca', 'codigo_dane' => '76111'],

            // Vaupés
            ['nombre' => 'Mitú', 'departamento' => 'Vaupés', 'codigo_dane' => '97001'],

            // Vichada
            ['nombre' => 'Puerto Carreño', 'departamento' => 'Vichada', 'codigo_dane' => '99001'],
        ];

        foreach ($ciudades as $ciudad) {
            Ciudad::create($ciudad);
        }
    }
}
