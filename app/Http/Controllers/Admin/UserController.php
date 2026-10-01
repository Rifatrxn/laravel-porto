<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Kelola akun yang bisa masuk ke panel admin.
 *
 * Hanya peran 'admin' yang boleh membuka halaman ini (lihat routes/web.php).
 * Beberapa pengaman penting:
 *  - admin tidak bisa menghapus atau menurunkan dirinya sendiri;
 *  - admin terakhir yang masih aktif tidak bisa dihapus, dinonaktifkan,
 *    atau diturunkan perannya — supaya panel tidak pernah terkunci total.
 */
class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', [
            'rows' => User::query()->orderBy('role')->orderBy('name')->get(),
            'total' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
        ]);
    }

    public function create()
    {
        return view('admin.users.form', ['row' => new User(['role' => 'editor', 'is_active' => true])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, null);

        unset($data['password_confirmation']);

        User::create($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun berhasil dibuat.');
    }

    public function edit(User $user)
    {
        return view('admin.users.form', ['row' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, $user);

        unset($data['password_confirmation']);

        // Jangan biarkan admin mengunci dirinya sendiri keluar dari panel.
        if ($user->id === auth()->id()) {
            $data['role'] = 'admin';
            $data['is_active'] = true;
        }

        // Jangan biarkan admin aktif terakhir diturunkan atau dimatikan.
        if ($user->isLastActiveAdmin()) {
            $data['role'] = 'admin';
            $data['is_active'] = true;
        }

        // Kosongkan password kalau tidak diisi (artinya tidak diubah).
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun yang sedang dipakai.');
        }

        if ($user->isLastActiveAdmin()) {
            return back()->with('error', 'Ini admin aktif terakhir — hapus atau turunkan akun lain dulu.');
        }

        $user->delete();

        return back()->with('success', 'Akun berhasil dihapus.');
    }

    /* ------------------------------------------------------------------ */

    /** @return array<string,mixed> */
    protected function validated(Request $request, ?User $user): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required', 'email', 'max:190',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if (filled($data['password'] ?? null)) {
            $data['password'] = Hash::make($data['password']);
        }

        return $data;
    }
}
