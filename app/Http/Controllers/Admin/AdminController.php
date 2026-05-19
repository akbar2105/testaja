<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Kabupaten;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminController extends Controller
{

    // ── Index ─────────────────────────────────────────────────────────────────

    public function index()
    {
        $admins = User::with(['role', 'kabupaten'])
            ->whereHas('role', fn($q) => $q->where('name', 'admin'))
            ->latest()
            ->paginate(10);

        return view('admin.admins.index', compact('admins'));
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function create()
    {
        // Kabupaten yang belum punya admin (opsional, bisa diubah ke semua)
        $kabupatens = Kabupaten::orderBy('id')->get();

        return view('admin.admins.create', compact('kabupatens'));
    }

    // ── Store ─────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'      => ['required', 'confirmed', Rules\Password::defaults()],
            'kabupaten_id'  => ['nullable', 'exists:kabupaten,id'],
            'is_active'     => ['required', 'boolean'],
        ]);

        $adminRole = Role::where('name', 'admin')->firstOrFail();

        User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'role_id'      => $adminRole->id,
            'kabupaten_id' => $request->kabupaten_id ?: null, // null = akses semua
            'is_active'    => $request->is_active,
        ]);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin berhasil ditambahkan!');
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function show(User $admin)
    {
        $admin->load(['role', 'kabupaten']);

        return view('admin.admins.show', compact('admin'));
    }

    // ── Edit ──────────────────────────────────────────────────────────────────

    public function edit(User $admin)
    {
        $kabupatens = Kabupaten::orderBy('id')->get();

        return view('admin.admins.edit', compact('admin', 'kabupatens'));
    }

    // ── Update ────────────────────────────────────────────────────────────────

    public function update(Request $request, User $admin)
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $admin->id],
            'password'     => ['nullable', 'confirmed', Rules\Password::defaults()],
            'kabupaten_id' => ['nullable', 'exists:kabupaten,id'],
            'is_active'    => ['required', 'boolean'],
        ]);

        $data = [
            'name'         => $request->name,
            'email'        => $request->email,
            'kabupaten_id' => $request->kabupaten_id ?: null,
            'is_active'    => $request->is_active,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Data admin berhasil diperbarui!');
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    public function destroy(User $admin)
    {
        // Pastikan tidak menghapus diri sendiri
        if ($admin->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $admin->delete();

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin berhasil dihapus!');
    }
}