<?php

namespace Database\Seeders;

use App\Models\Etapa;
use Illuminate\Database\Seeder;

class EtapaSeeder extends Seeder
{
    public function run(): void
    {
        $etapas = [
            ['orden' => 1, 'nombre' => '1ª Cuatrimestre'],
            ['orden' => 2, 'nombre' => '2ª Cuatrimestre'],
        ];

        foreach ($etapas as $etapa) {
            Etapa::updateOrCreate(
                ['orden' => $etapa['orden']],
                ['nombre' => $etapa['nombre']]
            );
        }
    }
}
