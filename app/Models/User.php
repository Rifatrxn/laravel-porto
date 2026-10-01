<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLES = [
        'admin' => 'Admin — akses penuh',
        'editor' => 'Editor — hanya mengubah konten',
    ];

    protected $fillable = [
        'name', 'email', 'password', 'role', 'is_active', 'avatar', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Peran                                                             */
    /* ------------------------------------------------------------------ */

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** Label peran untuk ditampilkan. */
    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }

    /** Benar kalau akun ini boleh dihapus/diturunkan (admin aktif terakhir). */
    public function isLastActiveAdmin(): bool
    {
        if (! $this->isAdmin()) {
            return false;
        }

        return static::query()
            ->where('role', 'admin')
            ->where('is_active', true)
            ->where('id', '!=', $this->id)
            ->count() === 0;
    }
}
