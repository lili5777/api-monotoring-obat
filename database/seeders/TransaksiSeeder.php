<?php

namespace Database\Seeders;

use App\Models\Transaksi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Transaksi::create([
            'id_obat' => 1,
            'id_rak' => 1,
            'jumlah' => 20,
            'masuk' => '2025-01-01',
            'kadaluarsa' => '2025-02-01',

        ]);
        Transaksi::create([
            'id_obat' => 2,
            'id_rak' => 2,
            'jumlah' => 30,
            'masuk' => '2025-01-03',
            'kadaluarsa' => '2025-02-02',
        ]);
    }
}
