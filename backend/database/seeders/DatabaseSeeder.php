<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AnexoSeeder::class,
            ModuloSeeder::class,
            MateriaSeeder::class,
            EtapaSeeder::class,
            AnexoCursoSeeder::class,
            CursoEtapaModuloSeeder::class,
            CursoMateriaSeeder::class,
            BloqueHorarioSeeder::class,
            HorariasPrimerAnioSeeder::class,
            HorariasSegundoAnioSeeder::class,
            HorariasTercerAnioSeeder::class,
        ]);

        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
