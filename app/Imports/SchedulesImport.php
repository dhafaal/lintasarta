<?php

namespace App\Imports;

use App\Models\Schedules;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Carbon\Carbon;

class SchedulesImport implements ToCollection, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    protected $month;
    protected $year;
    protected $errors = [];
    protected $successCount = 0;
    protected $skipCount = 0;
    protected $previewMode = false;
    protected $previewData = [];

    public function __construct($month, $year, $previewMode = false)
    {
        $this->month = $month;
        $this->year = $year;
        $this->previewMode = $previewMode;
    }
    
    public function getPreviewData()
    {
        return $this->previewData;
    }

    public function collection(Collection $rows)
    {
        // Group rows by user_id and tanggal
        $groupedRows = [];
        
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            
            // Normalisasi nilai (trim string)
            $userIdRaw = isset($row['user_id']) ? trim((string)$row['user_id']) : null;
            $tanggalRaw = isset($row['tanggal']) ? trim((string)$row['tanggal']) : null;
            $shiftIdRaw = isset($row['shift_id']) ? trim((string)$row['shift_id']) : null;

            // Abaikan baris kosong total (mencegah ratusan error dari baris kosong di Excel)
            $allEmpty = ($userIdRaw === null || $userIdRaw === '')
                && ($tanggalRaw === null || $tanggalRaw === '')
                && ($shiftIdRaw === null || $shiftIdRaw === '');
            if ($allEmpty) {
                continue;
            }

            // Validasi basic (hanya error jika ada field terisi tapi sebagian kosong)
            if ($userIdRaw === '' || $tanggalRaw === '' || $shiftIdRaw === '') {
                $this->errors[] = "Baris {$rowNumber}: Data tidak lengkap (user_id, tanggal, atau shift_id kosong)";
                $this->skipCount++;
                continue;
            }
            
            // Cast ke integer aman
            $userId = (int)$userIdRaw;
            $tanggal = (int)$tanggalRaw;
            $shiftId = (int)$shiftIdRaw;

            $key = $userId . '_' . $tanggal;
            
            if (!isset($groupedRows[$key])) {
                $groupedRows[$key] = [];
            }
            
            $groupedRows[$key][] = [
                'row' => [
                    'user_id' => $userId,
                    'tanggal' => $tanggal,
                    'shift_id' => $shiftId,
                ],
                'rowNumber' => $rowNumber
            ];
        }
        
        // Process grouped rows
        foreach ($groupedRows as $key => $group) {
            $firstRow = $group[0]['row'];
            $firstRowNumber = $group[0]['rowNumber'];
            
            try {
                // Validasi user_id
                $user = User::find($firstRow['user_id']);
                if (!$user) {
                    $this->errors[] = "Baris {$firstRowNumber}: User ID {$firstRow['user_id']} tidak ditemukan";
                    $this->skipCount++;
                    continue;
                }

                // Validasi tanggal
                $date = $firstRow['tanggal'];
                if (!is_numeric($date) || $date < 1 || $date > 31) {
                    $this->errors[] = "Baris {$firstRowNumber}: Tanggal tidak valid ({$date})";
                    $this->skipCount++;
                    continue;
                }

                // Buat schedule_date
                try {
                    $scheduleDate = Carbon::createFromDate($this->year, $this->month, $date);
                } catch (\Exception $e) {
                    $this->errors[] = "Baris {$firstRowNumber}: Tanggal tidak valid untuk bulan {$this->month}/{$this->year}";
                    $this->skipCount++;
                    continue;
                }

                // Process shift pertama (dari baris pertama)
                $shift1Id = $firstRow['shift_id'];
                
                // Validasi shift exists
                $shift1 = Shift::find($shift1Id);
                if (!$shift1) {
                    $this->errors[] = "Baris {$firstRowNumber}: Shift ID '{$shift1Id}' tidak ditemukan";
                    $this->skipCount++;
                    continue;
                }

                // Cek apakah schedule sudah ada
                $existingSchedule1 = Schedules::where('user_id', $user->id)
                    ->where('shift_id', $shift1Id)
                    ->whereDate('schedule_date', $scheduleDate)
                    ->first();

                $shift1Status = 'new';
                if ($existingSchedule1) {
                    $shift1Status = 'exists';
                }

                // Jika preview mode, simpan ke preview data
                if ($this->previewMode) {
                    $this->previewData[] = [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'date' => $date,
                        'schedule_date' => $scheduleDate->format('Y-m-d'),
                        'shift_1_id' => $shift1Id,
                        'shift_1_name' => $shift1->shift_name,
                        'shift_1_category' => $shift1->category,
                        'shift_1_status' => $shift1Status,
                        'shift_2_id' => null,
                        'shift_2_name' => null,
                        'shift_2_category' => null,
                        'shift_2_status' => null,
                    ];
                    
                    if ($shift1Status == 'new') {
                        $this->successCount++;
                    } else {
                        $this->skipCount++;
                    }
                } else {
                    // Mode normal: simpan ke database
                    if (!$existingSchedule1) {
                        Schedules::create([
                            'user_id' => $user->id,
                            'shift_id' => $shift1Id,
                            'schedule_date' => $scheduleDate,
                        ]);
                        $this->successCount++;
                    } else {
                        $this->skipCount++;
                    }
                }

                // Process shift kedua (jika ada baris kedua dengan tanggal yang sama)
                if (count($group) > 1) {
                    $secondRow = $group[1]['row'];
                    $secondRowNumber = $group[1]['rowNumber'];
                    
                    $shift2Id = $secondRow['shift_id'];
                    
                    // Validasi shift exists
                    $shift2 = Shift::find($shift2Id);
                    if (!$shift2) {
                        $this->errors[] = "Baris {$secondRowNumber}: Shift ID '{$shift2Id}' tidak ditemukan";
                        continue;
                    }

                    // Cek apakah schedule sudah ada
                    $existingSchedule2 = Schedules::where('user_id', $user->id)
                        ->where('shift_id', $shift2Id)
                        ->whereDate('schedule_date', $scheduleDate)
                        ->first();

                    $shift2Status = 'new';
                    if ($existingSchedule2) {
                        $shift2Status = 'exists';
                    }

                    // Jika preview mode, update preview data terakhir dengan shift 2
                    if ($this->previewMode) {
                        $lastIndex = count($this->previewData) - 1;
                        $this->previewData[$lastIndex]['shift_2_id'] = $shift2Id;
                        $this->previewData[$lastIndex]['shift_2_name'] = $shift2->shift_name;
                        $this->previewData[$lastIndex]['shift_2_category'] = $shift2->category;
                        $this->previewData[$lastIndex]['shift_2_status'] = $shift2Status;
                        
                        if ($shift2Status == 'new') {
                            $this->successCount++;
                        } else {
                            $this->skipCount++;
                        }
                    } else {
                        // Mode normal: simpan ke database
                        if (!$existingSchedule2) {
                            Schedules::create([
                                'user_id' => $user->id,
                                'shift_id' => $shift2Id,
                                'schedule_date' => $scheduleDate,
                            ]);
                            $this->successCount++;
                        } else {
                            $this->skipCount++;
                        }
                    }
                }
                
                // Warning jika ada lebih dari 2 baris untuk tanggal yang sama
                if (count($group) > 2) {
                    $this->errors[] = "User ID {$user->id}, Tanggal {$date}: Lebih dari 2 shift ditemukan, hanya 2 shift pertama yang diproses";
                }

            } catch (\Exception $e) {
                $this->errors[] = "Baris {$firstRowNumber}: " . $e->getMessage();
                $this->skipCount++;
            }
        }
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|integer',
            'tanggal' => 'required|integer|min:1|max:31',
            'shift_id' => 'required|integer',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'user_id.required' => 'User ID wajib diisi',
            'user_id.integer' => 'User ID harus berupa angka',
            'tanggal.required' => 'Tanggal wajib diisi',
            'tanggal.integer' => 'Tanggal harus berupa angka',
            'tanggal.min' => 'Tanggal minimal 1',
            'tanggal.max' => 'Tanggal maksimal 31',
            'shift_id.required' => 'Shift ID wajib diisi',
            'shift_id.integer' => 'Shift ID harus berupa angka',
        ];
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getSkipCount()
    {
        return $this->skipCount;
    }
}
