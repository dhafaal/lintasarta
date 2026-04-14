<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Schedules;
use App\Models\Shift;
use App\Models\Location;
use App\Models\Attendance;
use App\Models\Permissions;
use App\Models\ScheduleSwapRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan data dasar sudah ada
        $users = User::all();
        if ($users->count() < 2) {
            $this->command->warn('Harap jalankan AccountSeeder terlebih dahulu. (Kurang dari 2 user ditemukan)');
            return;
        }

        $shifts = Shift::all();
        if ($shifts->count() == 0) {
            $this->command->warn('Harap jalankan ShiftSeeder terlebih dahulu.');
            return;
        }

        $locations = Location::all();
        $locationId = $locations->count() > 0 ? $locations->first()->id : null;

        // Cleanup existing data for a clean timeline
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Attendance::truncate();
        Permissions::truncate();
        Schedules::truncate();
        ScheduleSwapRequest::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = Carbon::now();
        $startOfMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // 2. Generate Leave Quotas
        foreach ($users as $user) {
            DB::table('leave_quotas')->updateOrInsert(
                ['user_id' => $user->id, 'year' => $now->year],
                ['remaining_quota' => rand(5, 12), 'created_at' => $now, 'updated_at' => $now]
            );
        }

        // 3. Main Loop: Dari Awal Bulan Lalu sampai Akhir Bulan Ini
        $usersArray = $users->pluck('id')->toArray();
        $shiftsArray = $shifts->pluck('id', 'id')->all(); // We need shift details later
        $allShifts = $shifts->keyBy('id');
        
        $currentDate = $startOfMonth->copy();
        $adminUserId = User::where('role', 'Admin')->first()->id ?? $users->first()->id;

        while ($currentDate->lte($endOfMonth)) {
            $dateString = $currentDate->format('Y-m-d');
            $isPast = $currentDate->lt($now->copy()->startOfDay());
            $isToday = $currentDate->isToday();
            $isFuture = $currentDate->gt($now->copy()->endOfDay());

            foreach ($users as $user) {
                // Skip Admin: tidak perlu jadwal, absensi, dan shift
                if ($user->role === 'Admin') continue;

                // Skip weekend if you want, but for dummy data let's just generate daily or vary it
                // Logic: 90% chance of having a schedule on any given day
                if (rand(1, 100) > 90) continue;

                $shiftId = array_rand($shiftsArray);
                $shift = $allShifts[$shiftId];

                $schedule = Schedules::create([
                    'user_id' => $user->id,
                    'shift_id' => $shiftId,
                    'schedule_date' => $dateString,
                ]);

                if ($isPast) {
                    $rand = rand(1, 100);
                    if ($rand <= 75) {
                        // HADIR / TELAT / EARLY CHECKOUT
                        $status = 'hadir';
                        $isLate = false;
                        $lateMinutes = 0;

                        // 20% chance of being late
                        if (rand(1, 100) <= 20) {
                            $status = 'telat';
                            $isLate = true;
                            $lateMinutes = rand(5, 45);
                        }

                        // 10% chance of early checkout
                        if (rand(1, 100) <= 10) {
                            $status = 'early_checkout';
                        }

                        // Calculate times based on shift
                        $startTime = Carbon::createFromFormat('H:i:s', $shift->start_time);
                        $endTime = Carbon::createFromFormat('H:i:s', $shift->end_time);
                        
                        $checkIn = $currentDate->copy()->setTime($startTime->hour, $startTime->minute);
                        if ($isLate) {
                            $checkIn->addMinutes($lateMinutes);
                        } else {
                            $checkIn->subMinutes(rand(5, 25));
                        }

                        $checkOut = $currentDate->copy()->setTime($endTime->hour, $endTime->minute);
                        if ($status === 'early_checkout') {
                            $checkOut->subMinutes(rand(10, 60));
                        } else {
                            $checkOut->addMinutes(rand(5, 30));
                        }

                        Attendance::create([
                            'user_id' => $user->id,
                            'schedule_id' => $schedule->id,
                            'location_id' => $locationId,
                            'status' => $status,
                            'is_late' => $isLate,
                            'late_minutes' => $lateMinutes ?: null,
                            'check_in_time' => $checkIn,
                            'check_out_time' => $checkOut,
                            'latitude' => -6.2088,
                            'longitude' => 106.8456,
                            'latitude_checkout' => -6.2088,
                            'longitude_checkout' => 106.8456,
                        ]);
                    } elseif ($rand <= 90) {
                        // PERMISSION (Izin/Sakit/Cuti)
                        $types = ['izin', 'sakit', 'cuti'];
                        Permissions::create([
                            'user_id' => $user->id,
                            'schedule_id' => $schedule->id,
                            'type' => $types[array_rand($types)],
                            'reason' => 'Keperluan mendadak',
                            'status' => 'approved',
                            'approved_by' => $adminUserId,
                            'approved_at' => $currentDate->copy()->subDay(),
                        ]);
                    } else {
                        // ALPHA
                        Attendance::create([
                            'user_id' => $user->id,
                            'schedule_id' => $schedule->id,
                            'status' => 'alpha',
                        ]);
                    }
                } elseif ($isToday) {
                    // Siang hari ini: 50% sudah absen masuk
                    if (rand(1, 100) <= 50) {
                        $startTime = Carbon::createFromFormat('H:i:s', $shift->start_time);
                        $checkIn = $currentDate->copy()->setTime($startTime->hour, $startTime->minute)->subMinutes(rand(0, 15));
                        
                        Attendance::create([
                            'user_id' => $user->id,
                            'schedule_id' => $schedule->id,
                            'location_id' => $locationId,
                            'status' => 'hadir',
                            'is_late' => false,
                            'check_in_time' => $checkIn,
                            'latitude' => -6.2088,
                            'longitude' => 106.8456,
                        ]);
                    }
                } elseif ($isFuture) {
                    // MASA DEPAN: 5% chance of approved leave, the rest is just schedule (alpha)
                    if (rand(1, 100) <= 5) {
                        $types = ['izin', 'cuti'];
                        Permissions::create([
                            'user_id' => $user->id,
                            'schedule_id' => $schedule->id,
                            'type' => $types[array_rand($types)],
                            'reason' => 'Rencana cuti luar kota',
                            'status' => 'approved',
                            'approved_by' => $adminUserId,
                            'approved_at' => now(),
                        ]);
                    }
                }
            }
            $currentDate->addDay();
        }

        // 4. Generate Swap Requests History (Past & Future)
        $allSchedules = Schedules::all();
        $adminUserId = User::where('role', 'Admin')->first()->id ?? $users->first()->id;

        // Filter hanya non-Admin untuk swap requests
        $nonAdminUsers = $users->filter(fn($u) => $u->role !== 'Admin')->values();

        for ($k = 0; $k < 20; $k++) {
            // Get two random different non-Admin users
            if ($nonAdminUsers->count() < 2) break;
            $user1 = $nonAdminUsers->random();
            $user2 = $nonAdminUsers->where('id', '!=', $user1->id)->random();

            // Find schedules for both users
            $sched1 = $allSchedules->where('user_id', $user1->id)->random();
            $sched2 = $allSchedules->where('user_id', $user2->id)->where('schedule_date', $sched1->schedule_date)->first();

            if ($sched1 && $sched2) {
                $isPast = Carbon::parse($sched1->schedule_date)->lt(now()->startOfDay());
                
                if ($isPast) {
                    $statuses = ['approved', 'rejected_by_target', 'rejected_by_admin', 'canceled'];
                    $status = $statuses[array_rand($statuses)];
                } else {
                    $statuses = ['pending_target', 'pending_admin', 'approved', 'rejected_by_target'];
                    $status = $statuses[array_rand($statuses)];
                }

                ScheduleSwapRequest::create([
                    'requester_id' => $user1->id,
                    'requested_schedule_id' => $sched1->id,
                    'target_user_id' => $user2->id,
                    'target_schedule_id' => $sched2->id,
                    'reason' => 'Ingin tukar shift karena urusan pribadi',
                    'status' => $status,
                    'target_rejection_reason' => $status === 'rejected_by_target' ? 'Maaf, saya juga ada keperluan' : null,
                    'admin_id' => in_array($status, ['approved', 'rejected_by_admin']) ? $adminUserId : null,
                    'admin_note' => $status === 'rejected_by_admin' ? 'Jadwal operasional tidak memungkinkan' : null,
                    'created_at' => Carbon::parse($sched1->schedule_date)->subDays(rand(1, 3)),
                ]);
            }
        }
    }
}
