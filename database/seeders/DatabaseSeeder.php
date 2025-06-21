<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(AkunUser::class);
        $this->call(RakSeeder::class);
        $this->call(ObatSeeder::class);
        $this->call(TransaksiSeeder::class);
    }
}
