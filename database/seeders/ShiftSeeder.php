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
                'shift_name' => 'Shift A',
                'category' => 'Pagi',
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'shift_name' => 'Shift B',
                'category' => 'Siang',
                'start_time' => '11:00:00',
                'end_time' => '20:00:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'shift_name' => 'Shift C',
                'category' => 'Malam',
                'start_time' => '20:00:00',
                'end_time' => '08:00:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],  
        ]);
    }
}
