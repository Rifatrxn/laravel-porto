<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\StoresFiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    use StoresFiles;

    public function edit()
    {
        $definitions = config('portfolio.settings');
        $groups = [];
        foreach ($definitions as $key => $def) {
            $groups[$def['group'] ?? 'general'][$key] = $def;
        }

        $current = Setting::all_cached();

        return view('admin.settings.edit', compact('definitions', 'groups', 'current'));
    }

    public function update(Request $request)
    {
        foreach (config('portfolio.settings') as $key => $def) {
            $type = $def['type'] ?? 'string';

            switch ($type) {
                case 'file':
                    $file = $request->file($key);
                    if ($file) {
                        $old = Setting::get($key);
                        Setting::put($key, $this->replaceFile($file, $old, 'portfolio/settings'), 'file', $def['group'] ?? 'general');
                    } elseif ($request->boolean("remove_$key")) {
                        $this->deleteFile(Setting::get($key));
                        Setting::put($key, null, 'file', $def['group'] ?? 'general');
                    }
                    break;

                case 'bool':
                    /*
                     * Saklar grup "section" & "animasi" TIDAK ikut form besar:
                     * masing-masing punya tombol tersimpan-sendiri (route toggle).
                     * Kalau ditulis di sini, checkbox yang tidak tercentang akan
                     * mematikan semua bagian halaman — itu bug yang pernah
                     * menyembunyikan keahlian/pendidikan/proyek dari pengunjung.
                     */
                    if (in_array($def['group'] ?? '', ['section', 'animasi'], true)) {
                        break;
                    }

                    // Hanya tulis kalau field-nya memang ada di form. Jangan
                    // mengubah setting yang tidak dikirim halaman ini.
                    if (! $request->has($key)) {
                        break;
                    }

                    Setting::put($key, $request->boolean($key) ? '1' : '0', 'bool', $def['group'] ?? 'general');
                    break;

                default:
                    // Lewati field yang tidak dikirim form ini, supaya tidak
                    // menimpa nilainya dengan string kosong.
                    if (! $request->has($key)) {
                        break;
                    }

                    Setting::put(
                        $key,
                        (string) $request->input($key, $def['default'] ?? ''),
                        $type,
                        $def['group'] ?? 'general'
                    );
            }
        }

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }

    public function reset()
    {
        // Hapus semua baris setting, lalu bersihkan cache supaya nilai default
        // dari config langsung terpakai lagi.
        Setting::resetAll();

        return back()->with('success', 'Pengaturan dikembalikan ke nilai default.');
    }

    /**
     * Nyalakan/matikan SATU setting boolean saja, langsung tersimpan.
     *
     * Dipakai tombol di grup "Bagian Halaman" & "Animasi". Dengan begitu satu
     * klik tidak mungkin mengubah saklar lain — beda dengan checkbox di dalam
     * form besar, yang akan mematikan semua kotak yang tidak ikut tercentang.
     */
    public function toggle(string $key)
    {
        $def = config("portfolio.settings.{$key}");

        if (! $def || ($def['type'] ?? null) !== 'bool') {
            return back()->with('error', 'Saklar itu tidak dikenal.');
        }

        $baru = ! setting_on($key);

        Setting::put($key, $baru ? '1' : '0', 'bool', $def['group'] ?? 'general');

        return back()->with('success', sprintf(
            '"%s" sekarang %s.',
            trim(str_replace('Tampilkan', '', $def['label'] ?? $key), ' "'),
            $baru ? 'TAMPIL' : 'disembunyikan'
        ));
    }
}
