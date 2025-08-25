<?php

use App\Models\Schedules;
use Illuminate\Support\Facades\Route;

Route::get('/calendar', function () {
    $schedules = Schedules::with('user', 'shift')->get();

    $events = $schedules->map(function ($schedule) {
        $start = \Carbon\Carbon::parse($schedule->schedule_date . ' ' . $schedule->shift->start_time);
        $end   = \Carbon\Carbon::parse($schedule->schedule_date . ' ' . $schedule->shift->end_time);

        if ($end->lt($start)) {
            $end->addDay();
        }

        return [
            'title' => $schedule->user->name . ' - ' . $schedule->shift->name,
            'start' => $start->format('Y-m-d\TH:i:s'),
            'end'   => $end->format('Y-m-d\TH:i:s'),
        ];
    });

    return response()->json($events);
});
