<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AccountSeeder extends Seeder {
    public function run(): void {

        User::create([
            'name' => 'Admin 1',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'Admin',
        ]);

        User::create([
            'name' => 'Operator 1',
            'email' => 'operator@example.com',
            'password' => Hash::make('password'),
            'role' => 'Operator',
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
    }
}
