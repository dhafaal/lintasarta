<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User; // kalau kamu ambil dari user
use App\Models\Shift; // kalau kamu pakai model Shift

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        // Ambil bulan & tahun dari request, default bulan & tahun sekarang
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        // Total hari dalam bulan tersebut
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;

        // Contoh data user + shift (sesuaikan dengan struktur database kamu)
        // Misalnya kita ambil semua user
        $users = User::all();

        $data = [];

        foreach ($users as $user) {
            $row = [
                'nama' => $user->name,
                'shifts' => [],
                'total_jam' => 0,
            ];

            // Loop tiap hari
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $date = Carbon::create($year, $month, $d);

                // Ambil shift dari database
                $shift = Shift::where('user_id', $user->id)
                    ->whereDate('date', $date)
                    ->first();

                if ($shift) {
                    $row['shifts'][$d] = [
                        'shift' => $shift->type, // contoh field type: Pagi / Siang / Malam
                        'hours' => $shift->hours, // contoh field hours: 8 jam
                    ];
                    $row['total_jam'] += $shift->hours;
                } else {
                    $row['shifts'][$d] = [
                        'shift' => '-', // kalau tidak ada jadwal
                        'hours' => 0,
                    ];
                }
            }

            $data[] = $row;
        }

        return view('admin.schedules.index', [
            'month' => $month,
            'year' => $year,
            'daysInMonth' => $daysInMonth,
            'data' => $data,
        ]);
    }
}
