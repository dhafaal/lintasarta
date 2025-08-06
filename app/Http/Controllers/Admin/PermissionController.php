<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::with(['user', 'schedule.shift'])->get();
        return view('admin.permissions.index', compact('permissions'));
    }

    public function approve($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->status = 'Disetujui';
        $permission->save();

        return back()->with('success', 'Izin disetujui.');
    }

    public function reject($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->status = 'Ditolak';
        $permission->save();

        return back()->with('success', 'Izin ditolak.');
    }
}
