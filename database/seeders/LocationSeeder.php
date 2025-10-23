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
                'name' => 'TBS',
                'latitude' => -6.2909797985328195,
                'longitude' => 106.78580949615643,
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
