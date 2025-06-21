<?php

namespace Database\Seeders;

use App\Models\Rak;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Rak::create([
            'nama_rak' => 'A1',
            'kapasitas' => 50,
            'terisi' => 20,
            'kosong' => 30,
        ]);

        Rak::create([
            'nama_rak' => 'A2',
            'kapasitas' => 50,
            'terisi' => 30,
            'kosong' => 20,
        ]);
    }
}
