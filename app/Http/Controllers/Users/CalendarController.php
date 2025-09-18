<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Schedules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function calendarView()
    {
        return view('users.calendar');
    }

    public function calendarData(Request $request)
    {
        $userId = Auth::id(); 
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        $schedules = Schedules::with('shift')
            ->where('user_id', $userId)
            ->whereYear('schedule_date', $year)
            ->whereMonth('schedule_date', $month)
            ->get();

        $events = $schedules->map(function ($schedule) {
            $shiftName = $schedule->shift->shift_name ?? 'Shift';
            $startTime = Carbon::parse($schedule->shift->start_time)->format('H:i');
            $endTime   = Carbon::parse($schedule->shift->end_time)->format('H:i');

            $startDate = $schedule->schedule_date . 'T' . $schedule->shift->start_time;
            $endDate   = $schedule->schedule_date . 'T' . $schedule->shift->end_time;

            // Tangani shift malam (end < start → selesai besok)
            if (Carbon::parse($schedule->shift->end_time)
                ->lt(Carbon::parse($schedule->shift->start_time))
            ) {
                $endDate = Carbon::parse($schedule->schedule_date)
                    ->addDay()
                    ->format('Y-m-d') . 'T' . $schedule->shift->end_time;
            }

            return [
                'id'          => $schedule->id,
                'title'       => $shiftName,
                'start'       => $startDate,
                'end'         => $endDate,
                'allDay'      => false,
                'shift'       => $shiftName,
                'start_time'  => $startTime,
                'end_time'    => $endTime,
            ];
        });

        return response()->json($events);
    }
}
