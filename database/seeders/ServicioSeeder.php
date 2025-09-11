
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Servicio;

class ServicioSeeder extends Seeder
{
    public function run() {
        $servicios = ['Energía Eléctrica', 'Internet', 'Gas Natural', 'Acueducto', 'Residuos'];
        foreach ($servicios as $nombre) {
            Servicio::create(['nombre' => $nombre]);
        }
    }
}
