<?php

namespace Database\Seeders;

use App\Models\Obat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Obat::create([
            'kode' => 'PR-1',
            'nama_obat' => 'Paracetamol'
        ]);
        Obat::create([
            'kode' => 'BD-1',
            'nama_obat' => 'Bodrex'
        ]);
    }
}
