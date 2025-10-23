<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AccountSeeder extends Seeder {
    public function run(): void {

        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'Admin',
        ]);
        User::create([
            'name' => 'Mursidi',
            'email' => 'murmur@example.com',
            'password' => Hash::make('password'),
            'role' => 'User',
        ]);
        User::create([
            'name' => 'Dirman',
            'email' => 'dir@example.com',
            'password' => Hash::make('password'),
            'role' => 'User',
        ]);
            User::create([
            'name' => 'Dayat',
            'email' => 'dayat@example.com',
            'password' => Hash::make('password'),
            'role' => 'User',
        ]);
        User::create([
            'name' => 'Herman',
            'email' => 'herman@example.com',
            'password' => Hash::make('password'),
            'role' => 'User',
        ]);
        User::create([
            'name' => 'Surya',
            'email' => 'surya@example.com',
            'password' => Hash::make('password'),
            'role' => 'User',
        ]);
        User::create([
            'name' => 'Agus',
            'email' => 'agus@example.com',
            'password' => Hash::make('password'),
            'role' => 'User',
        ]);
    }
}
