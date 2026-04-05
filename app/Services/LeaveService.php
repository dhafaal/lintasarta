<?php

namespace App\Services;

use App\Models\Permissions;
use App\Models\Schedules;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\DB;
use Exception;

class LeaveService
{
    /**
     * Mengajukan cuti (Submit multiple leave requests)
     */
    public function submitLeaveRequest(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = $data['user'];
            $scheduleIds = $data['schedule_ids'];
            $reason = $data['reason'];
            $filePath = $data['file_path'] ?? null;
            
            // Validate that all schedules belong to the user and are in the future
            $schedules = Schedules::whereIn('id', $scheduleIds)
                ->where('user_id', $user->id)
                ->whereDate('schedule_date', '>=', now()->toDateString())
                ->get();
                
            if ($schedules->count() !== count($scheduleIds)) {
                throw new Exception('Beberapa jadwal tidak valid atau sudah lewat.');
            }

            // Group by year to process quotas correctly based on actual schedule date
            $schedulesByYear = $schedules->groupBy(function ($schedule) {
                return date('Y', strtotime($schedule->schedule_date));
            });

            // Check and update quota for each year
            foreach ($schedulesByYear as $year => $yearSchedules) {
                // Lock quota row immediately to prevent race conditions during submission
                $leaveQuota = DB::table('leave_quotas')
                                ->where('user_id', $user->id)
                                ->where('year', $year)
                                ->lockForUpdate()
                                ->first();

                // Because this is logic that could happen on newly started years without quotas yet:
                if (!$leaveQuota) {
                    throw new Exception("Data kuota cuti tahun {$year} tidak ditemukan. Silakan hubungi admin.");
                }

                $requestedDays = $yearSchedules->count();
                
                // Cek kuota apakah cukup
                if ($leaveQuota->remaining_quota < $requestedDays) {
                    throw new Exception("Sisa kuota cuti Anda di tahun {$year} ({$leaveQuota->remaining_quota} hari) tidak mencukupi untuk pengajuan ini.");
                }
            }

            // If all validations pass, check existing permissions in loop
            $createdPermissions = [];
            foreach ($schedules as $schedule) {
                $existingPermission = Permissions::where('user_id', $user->id)
                    ->whereHas('schedule', function($q) use ($schedule) {
                        $q->whereDate('schedule_date', $schedule->schedule_date);
                    })
                    ->first();

                if ($existingPermission) {
                    throw new Exception("Anda sudah memiliki pengajuan untuk tanggal {$schedule->schedule_date}.");
                }

                // Create the permission
                $permission = Permissions::create([
                    'user_id' => $user->id,
                    'schedule_id' => $schedule->id,
                    'type' => 'cuti',
                    'reason' => $reason,
                    'file' => $filePath,
                    'status' => 'pending'
                ]);

                $createdPermissions[] = $permission;

                // Log user activity
                UserActivityLog::log(
                    'request_leave',
                    'permissions',
                    $permission->id,
                    "Cuti - {$schedule->schedule_date}",
                    [
                        'schedule_id' => $schedule->id,
                        'type' => 'cuti',
                        'reason' => $reason,
                        'schedule_date' => $schedule->schedule_date
                    ],
                    "Mengajukan cuti untuk tanggal {$schedule->schedule_date}"
                );
            }
            
            return $createdPermissions;
        });
    }

    /**
     * Menyetujui cuti dan mengurangi kuota
     */
    public function approveLeaveRequest(int $permissionId, int $approverId)
    {
        return DB::transaction(function () use ($permissionId, $approverId) {
            $permission = Permissions::with('schedule')->where('id', $permissionId)->lockForUpdate()->firstOrFail();
            
            if ($permission->type !== 'cuti') {
                throw new Exception('Pengajuan yang dipilih bukan tipe cuti.');
            }

            if ($permission->status === 'approved') {
                throw new Exception('Pengajuan cuti ini sudah disetujui sebelumnya.');
            }

            if ($permission->status === 'rejected') {
                throw new Exception('Pengajuan cuti ini sudah ditolak sebelumnya.');
            }
            
            $year = date('Y', strtotime($permission->schedule->schedule_date));
            $leaveQuota = DB::table('leave_quotas')
                            ->where('user_id', $permission->user_id)
                            ->where('year', $year)
                            ->lockForUpdate()
                            ->first();

            $requestedDays = 1; 

            if (!$leaveQuota || $leaveQuota->remaining_quota < $requestedDays) {
                // If the user's quota ran out for some reason since submission
                throw new Exception("Gagal menyetujui. Kuota cuti tahun {$year} tidak mencukupi.");
            }
            
            // Kurangi sisa kuota cuti
            DB::table('leave_quotas')
                ->where('id', $leaveQuota->id)
                ->decrement('remaining_quota', $requestedDays);
                
            // Update status permission menjadi 'approved'
            $permission->update([
                'status'      => 'approved',
                'approved_by' => $approverId,
                'approved_at' => now(),
            ]);
            
            return $permission;
        });
    }
}
