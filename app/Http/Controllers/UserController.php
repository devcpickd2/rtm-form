<?php
namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Departemen;
use App\Models\Plant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    // app/Http/Controllers/UserController.php

    public function index(Request $request)
    {
        // mulai query
        $query = User::with(['plantRelasi', 'departmentRelasi']);

        // kalau ada pencarian
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });

            // cari berdasarkan nama plant
            $query->orWhereHas('plantRelasi', function ($q) use ($search) {
                $q->where('plant', 'like', "%{$search}%");
            });

            // cari berdasarkan nama departemen
            $query->orWhereHas('departmentRelasi', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }

        // paginate
        $users = $query->orderBy('name')->paginate(10);

        // supaya query search tetap di pagination
        $users->appends($request->all());

        // kirim ke view
        return view('user.index', compact('users'));
    }

    public function create()
    {
        // Ambil data plant dan departemen dari tabel masing-masing
        $plants = Plant::select('id', 'plant')->get();
        $departments = Departemen::select('id', 'nama')->get();

        return view('user.create', compact('plants', 'departments'));
    }

    // simpan user baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6',
            'email' => 'nullable|email|unique:users,email',
            'plant' => 'nullable|string',
            'department' => 'nullable|string',
            'type_user' => 'required|integer',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'email' => $request->email,
            'plant' => $request->plant,
            'department' => $request->department,
            'type_user' => $request->type_user,
            'updater' => auth()->user()->name,
        ]);

        return redirect()->route('user.index')->with('success', 'User berhasil dibuat');
    }

    public function edit(User $user)
    {
        $plants = Plant::all();
        $departments = Departemen::all();
        return view('user.edit', compact('user', 'plants', 'departments'));
    }
    // update user
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $user->uuid . ',uuid',
            'password' => 'nullable|string|min:6',
            'email' => 'nullable|email|unique:users,email,' . $user->uuid . ',uuid',
            'plant' => 'nullable|string',
            'department' => 'nullable|string',
            'type_user' => 'required|integer',
        ]);

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
            'email' => $request->email,
            'plant' => $request->plant,
            'department' => $request->department,
            'type_user' => $request->type_user,
            'updater' => auth()->user()->name,
        ]);

        return redirect()->route('user.index')->with('success', 'User berhasil diperbarui');
    }

    // hapus user
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('user.index')->with('success', 'User berhasil dihapus');
    }

}
