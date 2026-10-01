<?php

namespace App\Support;

use App\Kroombase\Client;
use Illuminate\Http\UploadedFile;

/**
 * Pengganti HandlesUploads versi lama: berkas tidak lagi disimpan di disk
 * lokal, melainkan diunggah ke bucket project KroomBase.
 *
 * Yang disimpan di kolom tabel adalah path di dalam bucket
 * (mis. "portfolio/avatar/foto-ab12cd34.png"). URL untuk ditampilkan
 * dibuat saat halaman dirender lewat media_url().
 */
trait StoresFiles
{
    /** Unggah satu berkas, kembalikan path di bucket. */
    protected function storeFile(?UploadedFile $file, string $folder): ?string
    {
        if (! $file) {
            return null;
        }

        return app(Client::class)->upload(
            $folder,
            $file->getClientOriginalName(),
            (string) file_get_contents($file->getRealPath()),
            $file->getMimeType() ?: 'application/octet-stream',
        );
    }

    /**
     * Ganti berkas lama dengan yang baru. Kalau tidak ada berkas baru,
     * path lama dikembalikan apa adanya.
     */
    protected function replaceFile(?UploadedFile $file, ?string $oldPath, string $folder): ?string
    {
        if (! $file) {
            return $oldPath;
        }

        $new = $this->storeFile($file, $folder);

        if ($new !== null && $oldPath) {
            $this->deleteFile($oldPath);
        }

        return $new;
    }

    /** Hapus berkas dari bucket (aman walau path kosong / gagal). */
    protected function deleteFile(?string $path): void
    {
        if (blank($path) || str_starts_with((string) $path, 'http')) {
            return;
        }

        try {
            app(Client::class)->deleteFile((string) $path);
        } catch (\Throwable) {
            // Berkas yang sudah hilang tidak boleh menggagalkan penyimpanan.
        }
    }

    /** Unggah banyak berkas (galeri proyek). */
    protected function storeFiles(array $files, string $folder): array
    {
        $paths = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $path = $this->storeFile($file, $folder);
                if ($path !== null) {
                    $paths[] = $path;
                }
            }
        }

        return $paths;
    }

    /** Ubah "a, b, c" atau array menjadi array bersih. */
    protected function toArray(mixed $value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map('trim', $value), fn ($v) => $v !== ''));
        }

        if (blank($value)) {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', (string) $value)), fn ($v) => $v !== ''));
    }
}
