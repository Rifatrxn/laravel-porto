<?php

namespace App\Support;

use App\Kroombase\Client;
use Illuminate\Support\Facades\Cache;

/**
 * Pembuat URL untuk berkas yang tersimpan di bucket KroomBase.
 *
 * Bucket bersifat privat: membaca berkas butuh session token. Supaya browser
 * tetap bisa menampilkan gambar, server meminta "tautan bertanda tangan"
 * (berlaku ~15 menit) ke KroomBase lalu mengirimkannya ke halaman.
 *
 * Hasilnya di-cache sedikit lebih pendek dari masa berlakunya supaya satu
 * halaman tidak meminta tautan baru untuk gambar yang sama berulang kali.
 */
class Media
{
    /** Cache tautan sedikit lebih pendek dari masa berlaku server (15 menit). */
    protected const CACHE_SECONDS = 720;

    public static function url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $path = (string) $path;

        // Tautan penuh (mis. hasil upload lama atau gambar dari luar) dipakai apa adanya.
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }

        try {
            return Cache::remember(
                'kroombase.media.'.md5($path),
                now()->addSeconds(self::CACHE_SECONDS),
                fn () => app(Client::class)->signedUrl($path)
            );
        } catch (\Throwable) {
            return null;
        }
    }

    /** Hapus tautan yang tersimpan untuk sebuah berkas (dipakai setelah upload). */
    public static function forget(?string $path): void
    {
        if (blank($path)) {
            return;
        }

        Cache::forget('kroombase.media.'.md5((string) $path));
    }
}
