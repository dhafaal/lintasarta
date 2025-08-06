<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Schedules;
use App\Models\User;
use App\Models\Shift;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedules::with(['user', 'shift'])->get();
        return view('admin.schedules.index', compact('schedules'));
    }

    public function create()
    {
        $users = User::all();
        $shifts = Shift::all();
        return view('admin.schedules.create', compact('users', 'shifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_id' => 'required|exists:shifts,id',
            'schedule_date' => 'required|date',
        ]);

        Schedules::create($request->all());
        return redirect()->route('admin.schedules.index')->with('success', 'Schedule created successfully.');
    }

    public function edit(Schedules $schedule)
    {
        $users = User::all();
        $shifts = Shift::all();
        return view('admin.schedules.edit', compact('schedule', 'users', 'shifts'));
    }

    public function update(Request $request, Schedules $schedule)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_id' => 'required|exists:shifts,id',
            'schedule_date' => 'required|date',
        ]);

        $schedule->update($request->all());
        return redirect()->route('admin.schedules.index')->with('success', 'Schedule updated successfully.');
    }

    public function destroy(Schedules $schedule)
    {
        $schedule->delete();
        return redirect()->route('admin.schedules.index')->with('success', 'Schedule deleted successfully.');
    }
}
