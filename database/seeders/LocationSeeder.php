<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Kantor Pusat Jakarta',
                'latitude' => -6.2087634,
                'longitude' => 106.845599,
                'radius' => 500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kantor Cabang Bandung',
                'latitude' => -6.9174639,
                'longitude' => 107.6191228,
                'radius' => 300,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kantor Cabang Surabaya',
                'latitude' => -7.2574719,
                'longitude' => 112.7520883,
                'radius' => 400,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
