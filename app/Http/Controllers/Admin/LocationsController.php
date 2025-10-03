<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;

class LocationsController extends Controller
{
    public function index()
    {
        $locations = Location::query()
            ->when(request('search'), function($q, $search){
                $q->where('name', 'like', "%{$search}%");
            })
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();
        return view('admin.locations.index', compact('locations'));
    }

    public function create()
    {
        return view('admin.locations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|integer|min:1',
            'type' => 'required|in:wfa,wfo',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->only(['name','latitude','longitude','radius','type']);
        $data['is_active'] = $request->boolean('is_active', true);
        $location = Location::create($data);

        // Log admin activity
        AdminActivityLog::log(
            'create',
            'Location',
            $location->id,
            $location->name,
            null,
            $location->toArray(),
            "Membuat lokasi baru: {$location->name}"
        );

        return redirect()->route('admin.locations.index')->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function edit(Location $location)
    {
        return view('admin.locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|integer|min:1',
            'type' => 'required|in:wfa,wfo',
            'is_active' => 'nullable|boolean',
        ]);

        $oldValues = $location->toArray();
        $data = $request->only(['name','latitude','longitude','radius','type']);
        $data['is_active'] = $request->boolean('is_active', $location->is_active);
        $location->update($data);

        // Log admin activity
        AdminActivityLog::log(
            'update',
            'Location',
            $location->id,
            $location->name,
            $oldValues,
            $location->fresh()->toArray(),
            "Mengupdate lokasi: {$location->name}"
        );

        return redirect()->route('admin.locations.index')->with('success', 'Lokasi berhasil diupdate.');
    }

    public function destroy(Location $location)
    {
        $locationData = $location->toArray();
        $locationName = $location->name;
        
        $location->delete();

        // Log admin activity
        AdminActivityLog::log(
            'delete',
            'Location',
            null,
            $locationName,
            $locationData,
            null,
            "Menghapus lokasi: {$locationName}"
        );

        return redirect()->route('admin.locations.index')->with('success', 'Lokasi berhasil dihapus.');
    }

    public function toggleActive(Location $location)
    {
        $oldValues = $location->toArray();
        $location->is_active = !$location->is_active;
        $location->save();

        AdminActivityLog::log(
            'update',
            'Location',
            $location->id,
            $location->name,
            $oldValues,
            $location->fresh()->toArray(),
            ($location->is_active ? 'Mengaktifkan' : 'Menonaktifkan') . " lokasi: {$location->name}"
        );

        return back()->with('success', 'Status lokasi diperbarui.');
    }

    public function bulkActivate(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return back()->with('error', 'Pilih minimal satu lokasi.');
        }
        $affected = Location::whereIn('id', $ids)->update(['is_active' => true]);

        AdminActivityLog::log(
            'update',
            'Location',
            null,
            'Bulk Activate',
            ['ids' => $ids],
            ['activated_count' => $affected, 'ids' => $ids],
            'Mengaktifkan beberapa lokasi'
        );

        return back()->with('success', "Berhasil mengaktifkan {$affected} lokasi.");
    }

    public function bulkDeactivate(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return back()->with('error', 'Pilih minimal satu lokasi.');
        }
        $affected = Location::whereIn('id', $ids)->update(['is_active' => false]);

        AdminActivityLog::log(
            'update',
            'Location',
            null,
            'Bulk Deactivate',
            ['ids' => $ids],
            ['deactivated_count' => $affected, 'ids' => $ids],
            'Menonaktifkan beberapa lokasi'
        );

        return back()->with('success', "Berhasil menonaktifkan {$affected} lokasi.");
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return back()->with('error', 'Pilih minimal satu lokasi.');
        }

        $toDelete = Location::whereIn('id', $ids)->get();
        $backup = $toDelete->map(function($l){return $l->toArray();})->all();
        $count = $toDelete->count();

        Location::whereIn('id', $ids)->delete();

        AdminActivityLog::log(
            'delete',
            'Location',
            null,
            'Bulk Delete',
            ['backup' => $backup],
            null,
            'Menghapus beberapa lokasi'
        );

        return back()->with('success', "Berhasil menghapus {$count} lokasi.");
    }
}
