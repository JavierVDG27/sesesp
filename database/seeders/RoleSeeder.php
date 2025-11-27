<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder {
    public function run(): void {
        DB::table('roles')->insert([
            ['nombre' => 'admin', 'descripcion' => 'Administrador del sistema'],
            ['nombre' => 'validador', 'descripcion' => 'Valida expedientes'],
            ['nombre' => 'capturista', 'descripcion' => 'Captura expedientes'],
        ]);
    }
}
