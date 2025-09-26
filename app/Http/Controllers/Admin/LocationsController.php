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
        $locations = Location::all();
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
        ]);

        $location = Location::create($request->all());

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
        ]);

        $oldValues = $location->toArray();
        $location->update($request->all());

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
}
