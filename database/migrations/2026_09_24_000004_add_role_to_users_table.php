<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan peran (role) dan kolom pendukung ke tabel users.
     *
     * - role       : 'admin' (akses penuh) atau 'editor' (hanya konten)
     * - is_active  : akun bisa dimatikan tanpa dihapus
     * - avatar     : path foto akun di bucket KroomBase
     * - last_login_at : kapan terakhir masuk (ditampilkan di daftar pengguna)
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('editor')->after('email');
            $table->boolean('is_active')->default(true)->after('role');
            $table->string('avatar')->nullable()->after('is_active');
            $table->timestamp('last_login_at')->nullable()->after('avatar');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'is_active', 'avatar', 'last_login_at']);
        });
    }
};
