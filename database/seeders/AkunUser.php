<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AkunUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'name' => 'adminn',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email' => 'admin@gmail.com',
        ]);

        User::create([
            'username' => 'user',
            'name' => 'userr',
            'password' => Hash::make('user123'),
            'role' => 'user',
            'email' => 'user@gmail.com',
        ]);
        //
    }
}
