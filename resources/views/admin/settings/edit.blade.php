@extends('layouts.admin')

@section('title', 'Pengaturan Tampilan')
@section('subtitle', 'Warna, animasi, section, dan teks global')

@php
    $groupLabels = [
        'identitas' => ['🏷️', 'Identitas Situs', 'Judul, favicon, dan teks footer'],
        'seo' => ['🔍', 'SEO & Berbagi Sosial', 'Tampilan saat link dibagikan'],
        'desain' => ['🎨', 'Desain', 'Warna, tema, dan gaya latar'],
        'animasi' => ['✨', 'Animasi', 'Efek gerak dan interaksi'],
        'section' => ['🧱', 'Bagian Halaman', 'Nyalakan/matikan satu bagian — satu klik langsung tersimpan'],
        'kontak' => ['✉️', 'Kontak', 'Tujuan pesan formulir'],
    ];
@endphp

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6" data-reveal>
        <p class="text-sm max-w-2xl" style="color:var(--muted)">
            Semua perubahan di sini berlaku langsung ke halaman publik. Tidak perlu mengedit kode apa pun.
        </p>

        <form action="{{ route('admin.settings.reset') }}" method="POST"
              data-confirm="Semua pengaturan akan dikembalikan ke nilai awal. Lanjutkan?"
              data-confirm-title="Reset pengaturan?">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-ghost btn-sm">↺ Reset ke Default</button>
        </form>
    </div>

    @if ($errors->any())
        <div class="card p-4 mb-6" style="border-color:color-mix(in srgb,#ef4444 40%,transparent)">
            <ul class="text-sm list-disc list-inside space-y-0.5" style="color:var(--muted)">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    {{--
        PENTING: halaman ini dibungkus SATU form (dengan enctype multipart)
        lewat komponen <x-settings-form>. Alasannya: HTML tidak mengizinkan form
        bersarang, jadi form terpisah di dalam sini akan membuat browser membuang
        tag penutup form induk — akibatnya field & tombol simpan "terlempar keluar"
        dari form dan upload favicon/gambar ikut gagal.

        Tombol saklar memakai atribut form="toggle-<key>" (tidak perlu bersarang),
        dan tombol simpan memakai form="settings-form".
    --}}
    <x-settings-form :action="route('admin.settings.update')">
        @csrf
        @method('PUT')

        {{-- Navigasi tab per grup --}}
        <div class="flex flex-wrap gap-2" data-reveal>
            @foreach ($groups as $group => $items)
                <a href="#group-{{ $group }}" class="chip">{{ $groupLabels[$group][0] ?? '•' }} {{ $groupLabels[$group][1] ?? ucfirst($group) }}</a>
            @endforeach
        </div>

        @foreach ($groups as $group => $items)
            @php [$icon, $label, $desc] = $groupLabels[$group] ?? ['•', ucfirst($group), '']; @endphp

            <div class="card p-6 scroll-mt-24" id="group-{{ $group }}" data-reveal>
                <div class="flex items-start gap-3 mb-6">
                    <span class="w-10 h-10 rounded-xl grid place-items-center text-lg shrink-0"
                          style="background:color-mix(in srgb,var(--accent) 14%,transparent)">{{ $icon }}</span>
                    <div>
                        <h3 class="font-semibold">{{ $label }}</h3>
                        @if ($desc)
                            <p class="text-xs mt-0.5" style="color:var(--muted)">{{ $desc }}</p>
                        @endif
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-5">
                    @foreach ($items as $key => $def)
                        @php
                            $type = $def['type'] ?? 'string';
                            $value = $current[$key] ?? $def['default'] ?? null;
                            $full = in_array($type, ['textarea', 'file'], true) || str_starts_with($key, 'meta_');
                        @endphp

                        <div class="{{ $full ? 'sm:col-span-2' : '' }}">
                            <label class="field-label" for="set-{{ $key }}">{{ $def['label'] ?? $key }}</label>

                            @if ($type === 'bool')
                                @if ($group === 'section' || $group === 'animasi')
                                    {{-- Saklar tersimpan sendiri: satu klik = satu perubahan.
                                         Aman dari masalah "kotak lain ikut termatikan". --}}
                                    <button type="submit" form="toggle-{{ $key }}"
                                            class="w-full flex items-center gap-3 p-3 rounded-xl text-left cursor-pointer transition-colors"
                                            style="background:var(--surface);border:1px solid {{ (bool) $value ? 'color-mix(in srgb,var(--accent) 45%,transparent)' : 'var(--border)' }}">
                                        <span class="w-9 h-5 rounded-full relative shrink-0 transition-colors"
                                              style="background:{{ (bool) $value ? 'var(--accent)' : 'var(--border-strong)' }}">
                                            <span class="absolute top-0.5 w-4 h-4 rounded-full bg-white transition-all"
                                                  style="left:{{ (bool) $value ? '1.15rem' : '0.15rem' }}"></span>
                                        </span>
                                        <span class="text-sm flex-1">{{ (bool) $value ? 'Tampil' : 'Disembunyikan' }}</span>
                                        <span class="text-[10px] px-2 py-0.5 rounded-md" style="background:var(--bg);color:var(--muted)">
                                            klik untuk {{ (bool) $value ? 'sembunyikan' : 'tampilkan' }}
                                        </span>
                                    </button>
                                @else
                                    <label class="flex items-center gap-3 cursor-pointer select-none p-3 rounded-xl"
                                           style="background:var(--surface);border:1px solid var(--border)">
                                        <input type="checkbox" id="set-{{ $key }}" name="{{ $key }}" value="1"
                                               @checked((bool) $value) class="w-4 h-4 rounded">
                                        <span class="text-sm">{{ (bool) $value ? 'Aktif' : 'Nonaktif' }}</span>
                                    </label>
                                @endif

                            @elseif ($type === 'textarea')
                                <textarea id="set-{{ $key }}" name="{{ $key }}" rows="3" class="field">{{ $value }}</textarea>

                            @elseif ($type === 'color')
                                <div class="flex items-center gap-3">
                                    <input type="color" id="set-{{ $key }}" name="{{ $key }}" class="field w-20 shrink-0"
                                           value="{{ $value ?: '#000000' }}">
                                    <input type="text" class="field" value="{{ $value }}" readonly
                                           style="font-family:ui-monospace,monospace;font-size:.8rem">
                                </div>

                            @elseif ($type === 'select')
                                <select id="set-{{ $key }}" name="{{ $key }}" class="field">
                                    @foreach (($def['options'] ?? []) as $optValue => $optLabel)
                                        <option value="{{ $optValue }}" @selected((string) $value === (string) $optValue)>{{ $optLabel }}</option>
                                    @endforeach
                                </select>

                            @elseif ($type === 'file')
                                <div class="flex items-center gap-4">
                                    <div class="w-20 h-20 rounded-xl overflow-hidden shrink-0 grid place-items-center"
                                         id="preview-{{ $key }}"
                                         style="background:var(--surface);border:1px dashed var(--border-strong)">
                                        @if ($url = media_url($value))
                                            <img src="{{ $url }}" alt="" class="w-full h-full object-contain p-1">
                                        @else
                                            <span class="text-xl opacity-40">🖼️</span>
                                        @endif
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <input type="file" id="set-{{ $key }}" name="{{ $key }}" class="field"
                                               data-preview="#preview-{{ $key }}">
                                        @if ($value)
                                            <label class="flex items-center gap-2 text-xs mt-2 cursor-pointer select-none" style="color:var(--muted)">
                                                <input type="checkbox" name="remove_{{ $key }}" value="1" class="w-3.5 h-3.5 rounded">
                                                Hapus file ini
                                            </label>
                                        @endif
                                    </div>
                                </div>

                            @else
                                <input id="set-{{ $key }}" name="{{ $key }}" type="text" class="field"
                                       value="{{ $value }}">
                            @endif

                            @if (!empty($def['help']))
                                <p class="text-xs mt-1.5" style="color:var(--muted)">{{ $def['help'] }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        {{-- Simpan (menempel di bawah) --}}
        <div class="sticky bottom-4 z-20">
            <div class="card card-solid p-4 flex items-center justify-between gap-4 shadow-lg">
                <p class="text-sm hidden sm:block" style="color:var(--muted)">Perubahan langsung tampil di website.</p>
                <div class="flex gap-2 ml-auto">
                    <a href="{{ route('portfolio.home') }}" target="_blank" class="btn btn-ghost btn-sm">Lihat Website</a>
                    <button type="submit" form="settings-form" class="btn btn-primary btn-sm">💾 Simpan Pengaturan</button>
                </div>
            </div>
        </div>

    </x-settings-form>

    {{--
        Form untuk tiap saklar tersimpan-sendiri, diletakkan SETELAH form utama
        (bukan di dalamnya). Tombol saklar di atas memakai form="toggle-<key>",
        jadi tidak perlu bersarang.
    --}}
    @foreach (config('portfolio.settings') as $key => $def)
        @if (($def['type'] ?? null) === 'bool' && in_array($def['group'] ?? '', ['section', 'animasi'], true))
            <form id="toggle-{{ $key }}" action="{{ route('admin.settings.toggle', $key) }}" method="POST" class="hidden">
                @csrf
            </form>
        @endif
    @endforeach
@endsection
