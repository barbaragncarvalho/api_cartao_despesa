<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin Mestre',
            'email' => 'admin@teste.com',
            'password' => Hash::make('admin123'),
            'is_admin' => true,
        ]);

        User::create([
            'name' => 'Usuario Comum',
            'email' => 'comum@teste.com',
            'password' => Hash::make('secreto123'),
            'is_admin' => false,
        ]);
    }
}
