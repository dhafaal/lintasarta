<?php

namespace App\Exports;

use App\Models\Attendance;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;
use Carbon\Carbon;

class AttendancesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    protected string $type; // monthly|yearly|user
    protected ?int $month;  // 1-12 or null
    protected ?int $year;   // YYYY or null
    protected ?int $userId; // user id or null

    public function __construct(string $type, ?int $month = null, ?int $year = null, ?int $userId = null)
    {
        $this->type = $type;
        $this->month = $month;
        $this->year = $year;
        $this->userId = $userId;
    }

    public function collection(): Collection
    {
        // Start with schedules to include alpha status (missing attendances)
        $schedulesQuery = \App\Models\Schedules::with(['user', 'shift']);

        // Apply date filters
        if ($this->type === 'monthly' && $this->year && $this->month) {
            $start = Carbon::create($this->year, $this->month, 1)->startOfDay();
            $end = $start->copy()->endOfMonth();
            $schedulesQuery->whereBetween('schedule_date', [$start->toDateString(), $end->toDateString()]);
        } elseif ($this->type === 'yearly' && $this->year) {
            $start = Carbon::create($this->year, 1, 1)->startOfDay();
            $end = $start->copy()->endOfYear();
            $schedulesQuery->whereBetween('schedule_date', [$start->toDateString(), $end->toDateString()]);
        } elseif ($this->type === 'user') {
            // Optional month/year for user export
            if ($this->year && $this->month) {
                $start = Carbon::create($this->year, $this->month, 1)->startOfDay();
                $end = $start->copy()->endOfMonth();
                $schedulesQuery->whereBetween('schedule_date', [$start->toDateString(), $end->toDateString()]);
            } elseif ($this->year) {
                $start = Carbon::create($this->year, 1, 1)->startOfDay();
                $end = $start->copy()->endOfYear();
                $schedulesQuery->whereBetween('schedule_date', [$start->toDateString(), $end->toDateString()]);
            }
        }
        // For 'all' type, no date filtering

        if ($this->type === 'user' && $this->userId) {
            $schedulesQuery->where('user_id', $this->userId);
        }

        $schedules = $schedulesQuery->get();

        // Get all attendances for these schedules
        $scheduleIds = $schedules->pluck('id');
        $attendances = Attendance::whereIn('schedule_id', $scheduleIds)->get()->keyBy('schedule_id');

        // Create combined data with proper status (including alpha)
        $combinedData = $schedules->map(function ($schedule) use ($attendances) {
            $attendance = $attendances->get($schedule->id);
            
            // Create a pseudo-attendance object for consistent mapping
            return (object) [
                'schedule' => $schedule,
                'user' => $schedule->user,
                'check_in_time' => $attendance->check_in_time ?? null,
                'check_out_time' => $attendance->check_out_time ?? null,
                'status' => $attendance->status ?? 'alpha', // Default to alpha if no attendance
                'late_minutes' => $attendance->late_minutes ?? 0,
            ];
        });

        // Sort by schedule date
        $sorted = $combinedData->sortBy(function ($item) {
            return $item->schedule->schedule_date ?? '9999-12-31';
        })->values();

        return $sorted;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama',
            'Shift',
            'Kategori Shift',
            'Check In',
            'Check Out',
            'Status',
        ];
    }

    public function map($item): array
    {
        $schedule = $item->schedule;
        $user = $item->user;
        $shift = $schedule->shift;

        return [
            $schedule->schedule_date ? Carbon::parse($schedule->schedule_date)->format('Y-m-d') : '',
            $user->name ?? '-',
            $shift->shift_name ?? '-',
            $shift->category ?? '-',
            $item->check_in_time ? Carbon::parse($item->check_in_time)->format('H:i:s') : '',
            $item->check_out_time ? Carbon::parse($item->check_out_time)->format('H:i:s') : '',
            $item->status ?? 'alpha',
        ];
    }
}
