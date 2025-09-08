<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedules;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\ScheduleReportExport;
use Maatwebsite\Excel\Facades\Excel;

class ScheduleController extends Controller
{
    // daftar schedule / index
    public function index(Request $request)
    {
        $query = Schedules::with(['user', 'shift']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('shift_filter')) {
            $query->whereHas('shift', function ($q) use ($request) {
                $q->where('name', $request->shift_filter);
            });
        }

        if ($request->filled('date_filter')) {
            $query->whereDate('schedule_date', $request->date_filter);
        }

        $schedules = $query->orderBy('schedule_date', 'asc')->get();

        $workHours = [];
        foreach ($schedules as $schedule) {
            $workHours[$schedule->user_id] = ($workHours[$schedule->user_id] ?? 0) + ($schedule->duration_in_minutes ?? 0);
        }

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
        $users  = User::orderBy('name')->get();
        $shifts = Shift::orderBy('name')->get();

        return view('admin.schedules.create', compact('users', 'shifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'         => 'required|array|min:1',
            'user_id.*'       => 'exists:users,id',
            'shift_id'        => 'required|array|min:1',
            'shift_id.*'      => 'exists:shifts,id',
            'schedule_date'   => 'required|array|min:1',
            'schedule_date.*' => 'date',
        ]);

        DB::transaction(function () use ($request) {
            $count = count($request->user_id);
            for ($i = 0; $i < $count; $i++) {
                // Skip jika shift_id kosong
                if (empty($request->shift_id[$i])) {
                    continue;
                }

                Schedules::updateOrCreate(
                    [
                        'user_id'       => $request->user_id[$i],
                        'schedule_date' => $request->schedule_date[$i],
                    ],
                    [
                        'shift_id' => $request->shift_id[$i],
                    ]
                );
            }
        });

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule berhasil ditambahkan.');
    }

    public function edit(Schedules $schedule)
    {
        $users  = User::orderBy('name')->get();
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

    public function calendarView(Request $request)
    {
        $month = (int) $request->query('month', now()->month);
        $year  = (int) $request->query('year', now()->year);

        [$data, $daysInMonth] = $this->buildMonthlyTableData($month, $year);

        return view('admin.schedules.calendar', compact('data', 'month', 'year', 'daysInMonth'));
    }

    public function calendarData()
    {
        $schedules = Schedules::with(['user', 'shift'])->get();

        $events = $schedules->map(function ($schedule) {
            $start = $schedule->schedule_date . 'T' . $schedule->shift->start_time;
            $end   = Carbon::parse($schedule->schedule_date . ' ' . $schedule->shift->end_time);

            if ($end->lt(Carbon::parse($start))) {
                $end->addDay();
            }

            return [
                'id'     => $schedule->id,
                'title'  => "{$schedule->user->name} - {$schedule->shift->name}",
                'start'  => $start,
                'end'    => $end->toDateTimeString(),
                'allDay' => false,
                'extendedProps' => [
                    'shift'      => $schedule->shift->name,
                    'start_time' => $schedule->shift->start_time,
                    'end_time'   => $schedule->shift->end_time,
                    'user'       => $schedule->user->name,
                ],
            ];
        });

        return response()->json($events);
    }

    public function report()
    {
        return view('admin.schedules.report');
    }

    public function exportReport(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year  = $request->input('year', now()->year);

        $fileName = "Report_Jadwal_{$month}_{$year}.xlsx";
        return Excel::download(new ScheduleReportExport($month, $year), $fileName);
    }

    public function bulkCreate()
    {
        $users  = User::orderBy('name')->get();
        $shifts = Shift::orderBy('name')->get();

        return view('admin.schedules.bulk-create', compact('users', 'shifts'));
    }

    public function bulkStore(Request $request)
    {
        // Handle single schedule creation
        if ($request->has('single')) {
            $request->validate([
                'single_user_id'       => 'required|exists:users,id',
                'single_shift_id'      => 'required|exists:shifts,id',
                'single_schedule_date' => 'required|date',
            ]);

            Schedules::updateOrCreate(
                [
                    'user_id'       => $request->single_user_id,
                    'schedule_date' => $request->single_schedule_date,
                ],
                [
                    'shift_id' => $request->single_shift_id,
                ]
            );

            return redirect()->route('admin.schedules.index')
                ->with('success', 'Jadwal tunggal berhasil disimpan.');
        }

        // Handle bulk schedule creation (monthly)
        if ($request->has('bulk_monthly')) {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'month'   => 'required|integer|min:1|max:12',
                'year'    => 'required|integer|min:2000',
                'shifts'  => 'array', // Tidak required, bisa kosong
            ]);

            $userId = $request->user_id;
            $month  = $request->month;
            $year   = $request->year;
            $shifts = $request->shifts ?? [];

            DB::transaction(function () use ($shifts, $userId, $month, $year) {
                $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
                
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
                    $shiftId = $shifts[$day] ?? null;

                    if (empty($shiftId) || $shiftId === '') {
                        // Jika shift kosong atau null, hapus jadwal yang ada
                        Schedules::where('user_id', $userId)
                            ->whereDate('schedule_date', $date)
                            ->delete();
                    } else {
                        // Jika ada shift, buat atau update
                        Schedules::updateOrCreate(
                            [
                                'user_id'       => $userId,
                                'schedule_date' => $date,
                            ],
                            [
                                'shift_id' => $shiftId,
                            ]
                        );
                    }
                }
            });

            return redirect()->route('admin.schedules.index')
                ->with('success', 'Jadwal bulanan berhasil diperbarui.');
        }

        // Handle bulk creation with multiple dates and users
        if ($request->has('bulk_multiple')) {
            $request->validate([
                'users'          => 'required|array|min:1',
                'users.*'        => 'exists:users,id',
                'dates'          => 'required|array|min:1',
                'dates.*'        => 'date',
                'shift_id'       => 'required|exists:shifts,id',
            ]);

            DB::transaction(function () use ($request) {
                foreach ($request->users as $userId) {
                    foreach ($request->dates as $date) {
                        Schedules::updateOrCreate(
                            [
                                'user_id'       => $userId,
                                'schedule_date' => $date,
                            ],
                            [
                                'shift_id' => $request->shift_id,
                            ]
                        );
                    }
                }
            });

            $userCount = count($request->users);
            $dateCount = count($request->dates);
            
            return redirect()->route('admin.schedules.index')
                ->with('success', "Berhasil membuat {$userCount} user dengan {$dateCount} tanggal jadwal.");
        }

        // Handle bulk creation with same shift for multiple dates
        if ($request->has('bulk_same_shift')) {
            $request->validate([
                'user_id'        => 'required|exists:users,id',
                'shift_id'       => 'required|exists:shifts,id',
                'start_date'     => 'required|date',
                'end_date'       => 'required|date|after_or_equal:start_date',
                'selected_days'  => 'array', // Array hari yang dipilih (0=Minggu, 1=Senin, dst)
            ]);

            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $selectedDays = $request->selected_days ?? []; // Jika kosong, semua hari

            DB::transaction(function () use ($request, $startDate, $endDate, $selectedDays) {
                $currentDate = $startDate->copy();
                
                while ($currentDate->lte($endDate)) {
                    // Jika tidak ada hari yang dipilih, atau hari ini termasuk yang dipilih
                    if (empty($selectedDays) || in_array($currentDate->dayOfWeek, $selectedDays)) {
                        Schedules::updateOrCreate(
                            [
                                'user_id'       => $request->user_id,
                                'schedule_date' => $currentDate->format('Y-m-d'),
                            ],
                            [
                                'shift_id' => $request->shift_id,
                            ]
                        );
                    }
                    
                    $currentDate->addDay();
                }
            });

            return redirect()->route('admin.schedules.index')
                ->with('success', 'Jadwal berhasil dibuat untuk periode yang dipilih.');
        }

        return redirect()->back()->withErrors(['error' => 'Tipe bulk tidak valid.']);
    }

    public function table(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year  = $request->input('year', now()->year);

        [$data, $daysInMonth] = $this->buildMonthlyTableData($month, $year);

        return view('admin.schedules.calendar', compact('data', 'month', 'year', 'daysInMonth'));
    }

    private function buildMonthlyTableData(int $month, int $year): array
    {
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        $users = User::whereHas('schedules', function ($q) use ($year, $month) {
            $q->whereYear('schedule_date', $year)->whereMonth('schedule_date', $month);
        })
            ->whereIn('role', ['user', 'operator'])
            ->orderBy('name')
            ->get();

        $data = [];
        foreach ($users as $user) {
            $row = [
                'nama' => $user->name,
                'shifts' => [],
                'total_jam' => '0j'
            ];

            $totalMinutes = 0;

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = Carbon::createFromDate($year, $month, $day)->format('Y-m-d');

                $schedule = Schedules::with('shift')
                    ->where('user_id', $user->id)
                    ->whereDate('schedule_date', $date)
                    ->first();

                if ($schedule && $schedule->shift) {
                    $start = Carbon::parse($schedule->shift->start_time);
                    $end   = Carbon::parse($schedule->shift->end_time);
                    if ($end->lt($start)) $end->addDay();

                    $minutes = $start->diffInMinutes($end);
                    $totalMinutes += $minutes;

                    $row['shifts'][$day] = [
                        'shift' => strtoupper(substr($schedule->shift->name, 0, 1)),
                        'hours' => round($minutes / 60, 1) . 'j',
                    ];
                } else {
                    $row['shifts'][$day] = [
                        'shift' => '',
                        'hours' => '',
                    ];
                }
            }

            $row['total_jam'] = round($totalMinutes / 60, 1) . 'j';
            $data[] = $row;
        }

        return [$data, $daysInMonth];
    }
}