<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Schedules;
use Carbon\Carbon;


class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('shifts'); // Filter keyword (nama/email)
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        $countAdmin = User::where('role', 'admin')->count();
        $countOperator = User::where('role', 'operator')->count();
        $countUser = User::where('role', 'user')->count();

        return view('admin.users.index', compact('users', 'countAdmin', 'countOperator', 'countUser'));

        $users = $query->orderBy('name')
            ->paginate(10)
            ->appends($request->query());
        
        $shifts = Shift::orderBy('name')->pluck('name');

        if ($request->ajax()) {
            return view('admin.users.table', compact('users'))->render();
        }

        return view('admin.users.index', compact('users', 'shifts'));
    }

    public function exportPdf(Request $request)
    {
        $query = User::with('shifts');

        $role = strtolower($request->get('role', 'all'));
        if ($role !== 'all' && in_array($role, ['admin', 'operator', 'user'])) {
            $query->where('role', $role);
        }

        $shift = strtolower($request->get('shift', 'all'));
        if ($shift !== 'all') {
            $query->whereHas('shifts', function ($q) use ($shift) {
                $q->whereRaw('LOWER(name) = ?', [$shift]);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->get();

        $pdf = Pdf::loadView('admin.users.pdf', compact('users'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('users-' . now()->format('YmdHis') . '.pdf');
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role'     => 'required|in:admin,operator,user'
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:admin,operator,user',
        ]);

        // Update data user
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui!');
    }


    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }

}
