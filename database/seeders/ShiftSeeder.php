<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShiftSeeder extends Seeder
{
    public function run()
    {
        DB::table('shifts')->insert([
            [
                'shift_name' => 'Pagi Normal',
                'category' => 'Pagi',
                'start_time' => '07:00:00',
                'end_time' => '16:00:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'shift_name' => 'Siang Normal',
                'category' => 'Siang',
                'start_time' => '13:00:00',
                'end_time' => '22:00:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'shift_name' => 'Malam Normal',
                'category' => 'Malam',
                'start_time' => '22:00:00',
                'end_time' => '07:00:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],  
        ]);
    }
}
