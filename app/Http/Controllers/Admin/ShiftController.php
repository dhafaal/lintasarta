<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\AdminShiftsLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $query = Shift::query()->orderBy('start_time');

        // Filter shift berdasarkan kategori
        if ($request->filled('filter') && in_array($request->filter, ['Pagi', 'Siang', 'Malam'])) {
            $query->where('category', $request->filter);
        }

        // Search bebas (cari di nama shift)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('shift_name', 'like', "%{$search}%");
        }

        $shifts = $query->get();
        $Pagi = $shifts->where('category', 'Pagi')->count();
        $Siang = $shifts->where('category', 'Siang')->count();
        $Malam = $shifts->where('category', 'Malam')->count();

        return view('admin.shifts.index', compact('shifts', 'Pagi', 'Siang', 'Malam'));
    }

    public function create()
    {
        return view('admin.shifts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'shift_name' => 'required|string|max:255',
            'category'   => 'required|in:Pagi,Siang,Malam',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i',
        ]);

        // Custom validasi shift malam
        $this->validateShiftTime($request->start_time, $request->end_time);

        // Normalisasi ke format H:i:s untuk DB
        $start = Carbon::createFromFormat('H:i', $request->start_time)->format('H:i:s');
        $end   = Carbon::createFromFormat('H:i', $request->end_time)->format('H:i:s');

        $shift = Shift::create([
            'shift_name' => $request->shift_name,
            'category'   => $request->category,
            'start_time' => $start,
            'end_time'   => $end,
        ]);

        // Log admin shift activity
        AdminShiftsLog::log(
            'create',
            $shift->id,
            $shift->shift_name,
            $shift->category,
            null,
            $shift->toArray(),
            "Membuat shift baru: {$shift->shift_name} ({$shift->category})"
        );

        return redirect()->route('admin.shifts.index')->with('success', 'Shift berhasil ditambahkan.');
    }

    public function edit(Shift $shift)
    {
        return view('admin.shifts.edit', compact('shift'));
    }

    public function update(Request $request, Shift $shift)
    {
        $request->validate([
            'shift_name' => 'required|string|max:255',
            'category'   => 'required|in:Pagi,Siang,Malam',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i',
        ]);

        // Custom validasi shift malam
        $this->validateShiftTime($request->start_time, $request->end_time);

        // Normalisasi ke format H:i:s untuk DB
        $start = Carbon::createFromFormat('H:i', $request->start_time)->format('H:i:s');
        $end   = Carbon::createFromFormat('H:i', $request->end_time)->format('H:i:s');

        $oldValues = $shift->toArray();
        
        $shift->update([
            'shift_name' => $request->shift_name,
            'category'   => $request->category,
            'start_time' => $start,
            'end_time'   => $end,
        ]);

        // Log admin shift activity
        AdminShiftsLog::log(
            'update',
            $shift->id,
            $shift->shift_name,
            $shift->category,
            $oldValues,
            $shift->fresh()->toArray(),
            "Mengubah shift: {$shift->shift_name}"
        );

        return redirect()->route('admin.shifts.index')->with('success', 'Shift berhasil diupdate.');
    }

    public function destroy(Shift $shift)
    {
        $shiftName = $shift->shift_name;
        $shiftData = $shift->toArray();
        
        $shift->delete();

        // Log admin shift activity
        AdminShiftsLog::log(
            'delete',
            null,
            $shiftName,
            $shiftData['category'] ?? null,
            $shiftData,
            null,
            "Menghapus shift: {$shiftName}"
        );

        return redirect()->route('admin.shifts.index')
            ->with('success', 'Shift berhasil dihapus.');
    }

    /**
     * Validasi khusus untuk jam shift
     */
    private function validateShiftTime($start, $end)
    {
        $startTime = Carbon::createFromFormat('H:i', $start);
        $endTime   = Carbon::createFromFormat('H:i', $end);

        if ($startTime->eq($endTime)) {
            abort(422, 'Jam mulai dan selesai tidak boleh sama.');
        }

        // Jika bukan shift malam, end_time harus > start_time
        if ($endTime->lt($startTime) && ! $this->isNightShift($startTime, $endTime)) {
            abort(422, 'Jam selesai harus setelah jam mulai (kecuali shift malam).');
        }

        return true;
    }

    /**
     * Deteksi shift malam (contoh 22:00 - 06:00)
     */
    private function isNightShift(Carbon $start, Carbon $end)
    {
        return $end->lt($start);
    }
}
