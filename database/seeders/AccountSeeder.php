<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AccountSeeder extends Seeder {
    public function run(): void {

        User::create([
            'name' => 'Mikasa',
            'email' => 'mikasa@example.com',
            'password' => Hash::make('password'),
            'role' => 'Admin',
        ]);
        User::create([
            'name' => 'Mursidi',
            'email' => 'mursidi@example.com',
            'password' => Hash::make('password'),
            'role' => 'User',
        ]);
    }
}
