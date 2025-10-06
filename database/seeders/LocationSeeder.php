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
                'latitude' => -6.291279019274493,
                'longitude' => 106.78518762684719,
                'radius' => 500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
