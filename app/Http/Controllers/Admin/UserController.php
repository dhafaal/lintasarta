<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('shifts');

        // Filter role
        if ($request->filled('role') && strtolower($request->role) !== 'all') {
            $query->where('role', $request->role);
        }

        // Filter shift
        if ($request->filled('shift') && strtolower($request->shift) !== 'all') {
            $query->whereHas('shifts', function ($q) use ($request) {
                $q->where('name', $request->shift);
            });
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name', 'asc')->paginate(10);

        // Kirim juga data shift supaya dropdown filter bisa diisi
        $shifts = Shift::orderBy('name', 'asc')->pluck('name');

        if ($request->ajax()) {
            return view('admin.users.table', compact('users'))->render();
        }

        return view('admin.users.index', compact('users', 'shifts'));
    }

    public function exportPdf(Request $request)
    {
        $query = User::with('shifts');

        // Filter role
        if ($request->filled('role') && strtolower($request->role) !== 'all') {
            $query->where('role', $request->role);
        }

        // Filter shift
        if ($request->filled('shift') && strtolower($request->shift) !== 'all') {
            $query->whereHas('shifts', function ($q) use ($request) {
                $q->where('name', $request->shift);
            });
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Ambil semua data sesuai filter
        $users = $query->orderBy('name', 'asc')->get();

        // Render PDF
        $pdf = Pdf::loadView('admin.users.pdf', compact('users'))
            ->setPaper('a4', 'landscape');

        // Download
        return $pdf->download('users-' . now()->format('YmdHis') . '.pdf');
    }



    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,operator,user'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,operator,user'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
