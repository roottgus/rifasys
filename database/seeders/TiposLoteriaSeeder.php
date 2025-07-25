<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposLoteriaSeeder extends Seeder
{
    public function run()
    {
        DB::table('tipos_loteria')->upsert([
            ['nombre' => 'Triple A'],
            ['nombre' => 'Triple B'],
            ['nombre' => 'Triple Zodiacal'],
        ], ['nombre']);
    }
}
