<?php

use Illuminate\Support\Facades\Storage;

if (! function_exists('media_url')) {
    /**
     * URL publik untuk berkas yang tersimpan di bucket KroomBase.
     *
     * Bucket-nya privat, jadi server membuat "tautan bertanda tangan" yang
     * berlaku sekitar 15 menit (lihat App\Support\Media).
     */
    function media_url(?string $path): ?string
    {
        return \App\Support\Media::url($path);
    }
}

if (! function_exists('indonesian_month')) {
    /**
     * "2024-08" => "Agustus 2024"
     */
    function indonesian_month(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $months = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];

        $value = trim($value);

        // Sudah berupa tahun saja
        if (preg_match('/^\d{4}$/', $value)) {
            return $value;
        }

        if (preg_match('/^(\d{4})-(\d{1,2})/', $value, $m)) {
            $month = (int) $m[2];

            return ($months[$month] ?? $m[2]).' '.$m[1];
        }

        return $value;
    }
}

if (! function_exists('period_label')) {
    /**
     * Rentang waktu untuk pengalaman/pendidikan.
     */
    function period_label(?string $start, ?string $end, bool $isCurrent, string $currentLabel = 'Sekarang'): string
    {
        $from = indonesian_month($start);
        $to = $isCurrent ? $currentLabel : indonesian_month($end);

        if ($from && $to) {
            return $from.' — '.$to;
        }

        return $from ?: ($to ?: '');
    }
}

if (! function_exists('initials')) {
    /**
     * Inisial nama untuk placeholder avatar.
     */
    function initials(?string $name, int $limit = 2): string
    {
        $parts = preg_split('/\s+/', trim((string) $name)) ?: [];
        $letters = '';

        foreach ($parts as $part) {
            if ($part !== '') {
                $letters .= mb_substr($part, 0, 1);
            }
            if (mb_strlen($letters) >= $limit) {
                break;
            }
        }

        return mb_strtoupper($letters ?: '?');
    }
}

if (! function_exists('setting_on')) {
    /**
     * Cek setting boolean dengan default aman (true kalau belum diset).
     */
    function setting_on(string $key, bool $default = true): bool
    {
        $value = \App\Models\Setting::get($key);

        return is_null($value) ? $default : (bool) $value;
    }
}
