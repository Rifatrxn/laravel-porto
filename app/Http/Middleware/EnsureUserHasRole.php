<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Membatasi akses berdasarkan peran pengguna.
 *
 * Dipakai di rute dengan pola:
 *   ->middleware('role:admin')            hanya admin
 *   ->middleware('role:admin,editor')     admin atau editor
 *
 * Kalau pengguna belum login, biarkan middleware 'auth' yang menanganinya.
 * Kalau sudah login tapi perannya tidak cocok, kembalikan 403 supaya tidak
 * membingungkan (bukan menendang ke halaman login).
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Anda sedang dinonaktifkan.']);
        }

        if ($roles !== [] && ! in_array($user->role, $roles, true)) {
            abort(403, 'Peran akun Anda tidak punya akses ke halaman ini.');
        }

        return $next($request);
    }
}
