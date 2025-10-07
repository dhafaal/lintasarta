<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Attendance;
use App\Models\Schedules;
use App\Models\UserActivityLog;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto mark missed checkouts as 'forgot_checkout' (FINAL end + 6h, multi-shift & cross-midnight)
Artisan::command('attendances:auto-forgot-checkout', function () {
    $now = Carbon::now();

    // Fetch all open attendances (checked-in, not checked-out)
    $open = Attendance::with(['schedule.shift'])
        ->whereNotNull('check_in_time')
        ->whereNull('check_out_time')
        ->get();

    // Group by user and schedule_date
    $grouped = $open->groupBy(function($att){
        return $att->user_id . '|' . optional($att->schedule)->schedule_date;
    });

    $totalAffected = 0;

    foreach ($grouped as $key => $atts) {
        $first = $atts->first();
        $userId = $first->user_id;
        $scheduleDate = optional($first->schedule)->schedule_date;
        if (!$scheduleDate) { continue; }

        // Compute FINAL end across all shifts for this user on this date
        $sameDaySchedules = Schedules::with('shift')
            ->where('user_id', $userId)
            ->whereDate('schedule_date', $scheduleDate)
            ->get();

        $finalEnd = null;
        foreach ($sameDaySchedules as $sch) {
            if (!$sch->shift) continue;
            $date = Carbon::parse($sch->schedule_date);
            $st = Carbon::parse($sch->shift->start_time);
            $et = Carbon::parse($sch->shift->end_time);
            $startDT = $date->copy()->setTimeFrom($st);
            $endDT   = $date->copy()->setTimeFrom($et);
            if ($endDT->lt($startDT)) { $endDT->addDay(); }
            if (!$finalEnd || $endDT->gt($finalEnd)) { $finalEnd = $endDT->copy(); }
        }

        if (!$finalEnd) { continue; }

        // Apply 6-hour grace after FINAL end
        $threshold = $finalEnd->copy()->addHours(1);
        if ($now->lt($threshold)) { continue; }

        // Update all open attendances for this user/date
        $affected = 0; $firstId = null;
        foreach ($atts as $att) {
            $cin = Carbon::parse($att->check_in_time);
            $checkoutAt = $cin->gt($finalEnd) ? $cin : $finalEnd;
            $att->update([
                'check_out_time' => $checkoutAt,
                'status' => 'forgot_checkout',
            ]);
            $affected++; $totalAffected++; if (!$firstId) { $firstId = $att->id; }
        }

        if ($affected > 0) {
            UserActivityLog::log(
                'auto_forgot_checkout',
                'attendances',
                $firstId,
                'Auto Forgot Checkout (final end + 6h)',
                [
                    'user_id' => $userId,
                    'schedule_date' => $scheduleDate,
                    'final_shift_end' => $finalEnd->toDateTimeString(),
                    'applied_after_hours' => 1,
                    'affected_attendances' => $affected,
                ],
                'Sistem menutup otomatis attendance (final end + 6 jam) untuk hari yang sama'
            );
        }
    }

    $this->info("Auto-forgot-checkout selesai. Attendance diperbarui: {$totalAffected}");
})->purpose('Auto close missed checkouts (final end + 6h) and mark them as forgot_checkout');
