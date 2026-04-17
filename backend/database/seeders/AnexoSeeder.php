<?php

namespace Database\Seeders;

use App\Models\Anexo;
use Illuminate\Database\Seeder;

class AnexoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $anexos = [
            'SAN PEDRO',
            'PERICO',
            'HUMAHUACA',
            'CENTRAL',
            'EL CARMEN',
            'PALPALA',
            'LA QUIACA',
        ];

        foreach ($anexos as $nombre) {
            Anexo::query()->firstOrCreate([
                'nombre' => $nombre,
            ]);
        }
    }
}
