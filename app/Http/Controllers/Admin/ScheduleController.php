<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedules;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Exports\ScheduleReportExport;
use Maatwebsite\Excel\Facades\Excel;


class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = Schedules::with(['user', 'shift']);

        // 🔍 Search by user name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        // ⏰ Filter shift
        if ($request->filled('shift_filter')) {
            $query->whereHas('shift', function ($q) use ($request) {
                $q->where('name', $request->shift_filter);
            });
        }

        // 📅 Filter tanggal
        if ($request->filled('date_filter')) {
            $query->whereDate('schedule_date', $request->date_filter);
        }

        $schedules = $query->get();

        // Hitung total jam kerja per user
        $workHours = [];
        foreach ($schedules as $schedule) {
            if ($schedule->shift) {
                $start = \Carbon\Carbon::parse($schedule->shift->start_time);
                $end   = \Carbon\Carbon::parse($schedule->shift->end_time);

                $minutes = $start->diffInMinutes($end, false);
                if ($minutes < 0) {
                    $end = $end->copy()->addDay();
                    $minutes = $start->diffInMinutes($end);
                }

                if (!isset($workHours[$schedule->user_id])) {
                    $workHours[$schedule->user_id] = 0;
                }
                $workHours[$schedule->user_id] += $minutes;
            }
        }

        // Format jam:menit
        $formattedWorkHours = [];
        foreach ($workHours as $userId => $minutes) {
            $hours = floor($minutes / 60);
            $mins  = $minutes % 60;
            $formattedWorkHours[$userId] = sprintf("%02dj %02dm", $hours, $mins);
        }

        return view('admin.schedules.index', [
            'schedules'          => $schedules,
            'workHours'          => $workHours,
            'formattedWorkHours' => $formattedWorkHours,
        ]);
    }




    public function create()
    {
        $users = User::orderBy('name')->get();
        $shifts = Shift::orderBy('name')->get();

        return view('admin.schedules.create', compact('users', 'shifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'shift_id'      => 'required|exists:shifts,id',
            'schedule_date' => 'required|date',
        ]);

        Schedules::create($request->only(['user_id', 'shift_id', 'schedule_date']));

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule berhasil ditambahkan.');
    }

    public function edit(Schedules $schedule)
    {
        $users = User::orderBy('name')->get();
        $shifts = Shift::orderBy('name')->get();

        return view('admin.schedules.edit', compact('schedule', 'users', 'shifts'));
    }

    public function update(Request $request, Schedules $schedule)
    {
        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'shift_id'      => 'required|exists:shifts,id',
            'schedule_date' => 'required|date',
        ]);

        $schedule->update($request->only(['user_id', 'shift_id', 'schedule_date']));

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule berhasil diupdate.');
    }

    public function destroy(Schedules $schedule)
    {
        $schedule->delete();

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule berhasil dihapus.');
    }

    public function calendarView()
    {
        return view('admin.schedules.calendar');
    }

    // Data untuk FullCalendar (JSON)
    public function calendarData()
    {
        $schedules = Schedules::with(['user', 'shift'])->get();

        $events = $schedules->map(function ($schedule) {
            $date = $schedule->schedule_date;

            // Tangani shift malam (end_time < start_time → berarti selesai besok)
            $endDate = $date;
            if (\Carbon\Carbon::parse($schedule->shift->end_time)
                ->lt(\Carbon\Carbon::parse($schedule->shift->start_time))
            ) {
                $endDate = \Carbon\Carbon::parse($date)->addDay()->format('Y-m-d');
            }

            return [
                'id'    => $schedule->id,
                'title' => $schedule->user->name . ' - ' . $schedule->shift->name,
                'start' => $date . 'T' . $schedule->shift->start_time,
                'end'   => $endDate . 'T' . $schedule->shift->end_time,
                'allDay' => false, // ❌ jangan pakai all-day
            ];
        });

        return response()->json($events);
    }

     // View report & export
    public function report()
    {
        return view('admin.schedules.report');
    }

    // Export Excel
    public function exportReport(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year  = $request->input('year', now()->year);

        $fileName = "Report_Jadwal_{$month}_{$year}.xlsx";
        return Excel::download(new ScheduleReportExport($month, $year), $fileName);
    }
}
