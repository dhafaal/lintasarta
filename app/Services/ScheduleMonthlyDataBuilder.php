<?php

namespace App\Services;

use App\Models\Schedules;
use App\Models\User;
use Carbon\Carbon;

class ScheduleMonthlyDataBuilder
{
    /**
     * Bangun data tabel kalender jadwal bulanan.
     *
     * @return array{0: array<int, array<string, mixed>>, 1: int}
     */
    public static function build(int $month, int $year): array
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
                'total_jam' => '0j',
            ];

            $totalMinutes = 0;

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = Carbon::createFromDate($year, $month, $day)->format('Y-m-d');

                $schedules = Schedules::with(['shift', 'attendances', 'permissions'])
                    ->where('user_id', $user->id)
                    ->whereDate('schedule_date', $date)
                    ->get();

                if ($schedules->isNotEmpty()) {
                    $shiftLetters = [];
                    $shiftNames = [];
                    $attendanceStatuses = [];
                    $dayMinutesAcc = 0;

                    foreach ($schedules as $schedule) {
                        if (!$schedule->shift) {
                            continue;
                        }

                        $attendance = null;
                        if (isset($schedule->attendances) && $schedule->attendances) {
                            $attendance = $schedule->attendances->firstWhere('user_id', $user->id) ?? $schedule->attendances->first();
                        } elseif (isset($schedule->attendance)) {
                            $attendance = $schedule->attendance;
                        }

                        $permissionApproved = null;
                        $permissionPending = null;
                        if (isset($schedule->permissions) && $schedule->permissions) {
                            $permissionApproved = $schedule->permissions->firstWhere('status', 'approved');
                            $permissionPending = $schedule->permissions->firstWhere('status', 'pending');
                        }

                        $start = Carbon::parse($schedule->shift->start_time);
                        $end = Carbon::parse($schedule->shift->end_time);
                        if ($end->lt($start)) {
                            $end->addDay();
                        }
                        $shiftMinutes = $start->diffInMinutes($end);

                        if ($attendance && $attendance->status === 'alpha') {
                            $minutes = 0;
                            $forcedAlpha = true;
                        } elseif (!$attendance && !$permissionApproved && !$permissionPending) {
                            $minutes = 0;
                            $forcedAlpha = true;
                        } else {
                            $minutes = $shiftMinutes;
                            $forcedAlpha = false;
                        }

                        $dayMinutesAcc += $minutes;

                        $shiftLetters[] = strtoupper(substr($schedule->shift->shift_name, 0, 1));
                        $shiftNames[] = $schedule->shift->shift_name;

                        $attendanceStatus = $attendance->status ?? (($permissionApproved || $permissionPending) ? 'izin' : null);
                        if ($forcedAlpha && !$attendanceStatus) {
                            $attendanceStatus = 'alpha';
                        }
                        $attendanceStatuses[] = $attendanceStatus;
                    }

                    $dayMinutesAfterBreak = $dayMinutesAcc > 0 ? max(0, $dayMinutesAcc - 60) : 0;
                    $totalMinutes += $dayMinutesAfterBreak;

                    $row['shifts'][$day] = [
                        'shift' => implode(',', $shiftLetters),
                        'shift_name' => implode(' + ', $shiftNames),
                        'hours' => self::formatHours($dayMinutesAfterBreak),
                        'attendance_statuses' => $attendanceStatuses,
                        'primary_attendance' => $attendanceStatuses[0] ?? null,
                    ];
                } else {
                    $row['shifts'][$day] = [
                        'shift' => '',
                        'shift_name' => '',
                        'hours' => '',
                        'attendance_statuses' => [],
                        'primary_attendance' => null,
                    ];
                }
            }

            $row['total_jam'] = self::formatHours($totalMinutes);
            $data[] = $row;
        }

        return [$data, $daysInMonth];
    }

    private static function formatHours(int $minutes): string
    {
        $hours = $minutes / 60;
        if ($hours == floor($hours)) {
            return floor($hours) . 'j';
        }

        return number_format($hours, 1) . 'j';
    }
}

