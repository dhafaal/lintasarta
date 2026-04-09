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

        $now = Carbon::now();
        $year = $now->year;

        // 2. Generate Leave Quotas
        foreach ($users as $user) {
            DB::table('leave_quotas')->updateOrInsert(
                ['user_id' => $user->id, 'year' => $year],
                ['remaining_quota' => rand(5, 12), 'created_at' => $now, 'updated_at' => $now]
            );
        }

        // 3. Generate Schedules untuk 5 hari kebelakang dan 5 hari ke depan
        $usersArray = $users->pluck('id')->toArray();
        $shiftsArray = $shifts->pluck('id')->toArray();
        
        $schedulesCreated = [];

        for ($i = -5; $i <= 5; $i++) {
            $date = $now->copy()->addDays($i)->format('Y-m-d');
            
            foreach ($usersArray as $userId) {
                // Pilih shift acak untuk user hari ini
                $shiftId = $shiftsArray[array_rand($shiftsArray)];
                
                $schedule = Schedules::create([
                    'user_id' => $userId,
                    'shift_id' => $shiftId,
                    'schedule_date' => $date,
                ]);

                // Simpan untuk keperluan testing selanjutnya (array)
                $schedulesCreated[] = $schedule;

                // 4. Generate Attendances untuk jadwal di masa lalu
                if ($i < 0 && rand(1, 100) > 20) { // 80% kemungkinan hadir
                    $attendanceStatus = ['hadir', 'telat', 'early_checkout'][rand(0, 2)];
                    
                    $checkInTime = Carbon::createFromFormat('Y-m-d', $date)->setHour(rand(6, 8))->setMinute(rand(0, 59));
                    $checkOutTime = $checkInTime->copy()->addHours(rand(8, 10));

                    Attendance::create([
                        'user_id' => $userId,
                        'schedule_id' => $schedule->id,
                        'location_id' => $locationId,
                        'status' => $attendanceStatus,
                        'is_late' => $attendanceStatus === 'telat',
                        'late_minutes' => $attendanceStatus === 'telat' ? rand(10, 60) : null,
                        'check_in_time' => $checkInTime,
                        'check_out_time' => $attendanceStatus !== 'forgot_checkout' ? $checkOutTime : null,
                        'latitude' => -6.2088,
                        'longitude' => 106.8456,
                        'latitude_checkout' => -6.2088,
                        'longitude_checkout' => 106.8456,
                    ]);
                } else if ($i < 0) {
                    Attendance::create([
                        'user_id' => $userId,
                        'schedule_id' => $schedule->id,
                        'status' => 'alpha',
                    ]);
                }
            }
        }

        // 5. Generate Permissions (Cuti, Izin, Sakit)
        $adminUserId = User::where('role', 'Admin')->first()->id ?? $users->first()->id;

        for ($j = 0; $j < 10; $j++) {
            $targetSchedule = $schedulesCreated[array_rand($schedulesCreated)];
            $types = ['izin', 'sakit', 'cuti'];
            $statuses = ['pending', 'approved', 'rejected'];
            $status = $statuses[array_rand($statuses)];

            Permissions::create([
                'user_id' => $targetSchedule->user_id,
                'schedule_id' => $targetSchedule->id,
                'type' => $types[array_rand($types)],
                'reason' => 'Alasan dummy permission ' . $j,
                'status' => $status,
                'approved_by' => $status !== 'pending' ? $adminUserId : null,
                'approved_at' => $status !== 'pending' ? now() : null,
                'admin_note' => $status === 'rejected' ? 'Ditolak karena alasan kurang kuat' : null,
            ]);
        }

        // 6. Generate Schedule Swap Requests
        for ($k = 0; $k < 5; $k++) {
            // Pilih requester acak
            $requesterId = $usersArray[array_rand($usersArray)];
            // Pilih target acak yang berbeda
            $targetUserId = null;
            while ($targetUserId === null || $targetUserId === $requesterId) {
                $targetUserId = $usersArray[array_rand($usersArray)];
            }

            // Cari jadwal masa depan untuk requester
            $requesterSchedule = Schedules::where('user_id', $requesterId)
                ->whereDate('schedule_date', '>', $now->format('Y-m-d'))
                ->inRandomOrder()->first();

            // Cari jadwal masa depan untuk target
            $targetSchedule = Schedules::where('user_id', $targetUserId)
                ->whereDate('schedule_date', '>', $now->format('Y-m-d'))
                ->inRandomOrder()->first();

            if ($requesterSchedule && $targetSchedule) {
                $swapStatuses = ['pending_target', 'rejected_by_target', 'pending_admin', 'rejected_by_admin', 'approved', 'canceled'];
                $swapStatus = $swapStatuses[array_rand($swapStatuses)];

                ScheduleSwapRequest::create([
                    'requester_id' => $requesterId,
                    'requested_schedule_id' => $requesterSchedule->id,
                    'target_user_id' => $targetUserId,
                    'target_schedule_id' => $targetSchedule->id,
                    'reason' => 'Ingin tukar shift ' . $k,
                    'status' => $swapStatus,
                    'target_rejection_reason' => $swapStatus === 'rejected_by_target' ? 'Maaf tidak bisa' : null,
                    'admin_id' => in_array($swapStatus, ['approved', 'rejected_by_admin']) ? $adminUserId : null,
                    'admin_note' => $swapStatus === 'rejected_by_admin' ? 'Jadwal bertabrakan' : null,
                ]);
            }
        }
    }
}
