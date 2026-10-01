@extends('layouts.admin')

@section('title', 'Profil Saya')
@section('subtitle', 'Identitas, foto, bio, dan sosial media')

@section('content')
    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div class="card p-4" style="border-color:color-mix(in srgb,#ef4444 40%,transparent)">
                <p class="text-sm font-medium mb-2">Ada isian yang perlu diperbaiki:</p>
                <ul class="text-sm list-disc list-inside space-y-0.5" style="color:var(--muted)">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="grid lg:grid-cols-[1fr_340px] gap-6 items-start">
            {{-- ===== Kolom utama ===== --}}
            <div class="space-y-6">
                <div class="card p-6" data-reveal>
                    <h3 class="font-semibold mb-5">Identitas</h3>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="field-label" for="name">Nama Lengkap <span style="color:var(--accent)">*</span></label>
                            <input id="name" name="name" class="field" required value="{{ old('name', $profile->name) }}">
                        </div>
                        <div>
                            <label class="field-label" for="headline">Jabatan / Headline</label>
                            <input id="headline" name="headline" class="field" value="{{ old('headline', $profile->headline) }}"
                                   placeholder="Full-Stack Web Developer">
                        </div>
                        <div>
                            <label class="field-label" for="role">Peran Tambahan</label>
                            <input id="role" name="role" class="field" value="{{ old('role', $profile->role) }}"
                                   placeholder="Mahasiswa / Freelancer">
                        </div>
                        <div>
                            <label class="field-label" for="location">Lokasi</label>
                            <input id="location" name="location" class="field" value="{{ old('location', $profile->location) }}"
                                   placeholder="Yogyakarta, Indonesia">
                        </div>
                        <div>
                            <label class="field-label" for="email">Email</label>
                            <input id="email" name="email" type="email" class="field" value="{{ old('email', $profile->email) }}">
                        </div>
                        <div>
                            <label class="field-label" for="phone">Telepon</label>
                            <input id="phone" name="phone" class="field" value="{{ old('phone', $profile->phone) }}">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="field-label" for="short_bio">Bio Singkat <span class="text-[10px]">(tampil di bagian hero, max 400 karakter)</span></label>
                        <textarea id="short_bio" name="short_bio" rows="2" class="field" maxlength="400">{{ old('short_bio', $profile->short_bio) }}</textarea>
                    </div>

                    <div class="mt-4">
                        <label class="field-label" for="about">Tentang Saya <span class="text-[10px]">(pisahkan paragraf dengan baris kosong)</span></label>
                        <textarea id="about" name="about" rows="9" class="field">{{ old('about', $profile->about) }}</textarea>
                    </div>
                </div>

                {{-- Hero --}}
                <div class="card p-6" data-reveal>
                    <h3 class="font-semibold mb-5">Bagian Hero (Halaman Depan)</h3>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="field-label" for="hero_greeting">Sapaan Pembuka</label>
                            <input id="hero_greeting" name="hero_greeting" class="field"
                                   value="{{ old('hero_greeting', $profile->hero_greeting) }}" placeholder="Halo, saya">
                        </div>
                        <div>
                            <label class="field-label" for="availability_text">Teks Status Ketersediaan</label>
                            <input id="availability_text" name="availability_text" class="field"
                                   value="{{ old('availability_text', $profile->availability_text) }}"
                                   placeholder="Terbuka untuk proyek freelance">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="field-label" for="hero_tagline">Tagline <span class="text-[10px]">(kalimat pertama yang muncul dengan efek mengetik)</span></label>
                        <input id="hero_tagline" name="hero_tagline" class="field" value="{{ old('hero_tagline', $profile->hero_tagline) }}">
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="field-label" for="hero_cta_label">Teks Tombol Utama</label>
                            <input id="hero_cta_label" name="hero_cta_label" class="field"
                                   value="{{ old('hero_cta_label', $profile->hero_cta_label) }}" placeholder="Lihat Proyek Saya">
                        </div>
                        <div>
                            <label class="field-label" for="hero_cta_url">Tautan Tombol Utama</label>
                            <input id="hero_cta_url" name="hero_cta_url" class="field"
                                   value="{{ old('hero_cta_url', $profile->hero_cta_url) }}" placeholder="#projects">
                        </div>
                    </div>

                    <label class="mt-5 flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="is_available" value="1" @checked(old('is_available', $profile->is_available))
                               class="w-4 h-4 rounded">
                        <span class="text-sm">Tampilkan badge "sedang tersedia" di hero</span>
                    </label>
                </div>

                {{-- Sosial media (baris dinamis) --}}
                <div class="card p-6" data-reveal data-repeater>
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="font-semibold">Media Sosial</h3>
                        <button type="button" data-repeater-add class="btn btn-ghost btn-sm">+ Tambah</button>
                    </div>

                    @php
                        $links = old('social_url') ? collect(old('social_platform'))->map(fn ($p, $i) => [
                            'platform' => $p,
                            'label' => old('social_label')[$i] ?? '',
                            'url' => old('social_url')[$i] ?? '',
                        ]) : collect($profile->social_links ?? []);
                        if ($links->isEmpty()) {
                            $links = collect([['platform' => '', 'label' => '', 'url' => '']]);
                        }
                    @endphp

                    <div data-repeater-rows class="space-y-3">
                        @foreach ($links as $i => $link)
                            <div data-repeater-row class="grid sm:grid-cols-[140px_1fr_1fr_auto] gap-3 items-center">
                                <input name="social_platform[{{ $i }}]" class="field" value="{{ $link['platform'] ?? '' }}"
                                       placeholder="github">
                                <input name="social_label[{{ $i }}]" class="field" value="{{ $link['label'] ?? '' }}"
                                       placeholder="Label (GitHub)">
                                <input name="social_url[{{ $i }}]" class="field" value="{{ $link['url'] ?? '' }}"
                                       placeholder="https://...">
                                <button type="button" data-repeater-remove
                                        class="w-9 h-9 rounded-lg grid place-items-center hover:text-red-400 transition-colors"
                                        style="background:var(--surface);border:1px solid var(--border)" title="Hapus baris">×</button>
                            </div>
                        @endforeach
                    </div>

                    <template data-repeater-template>
                        <div data-repeater-row class="grid sm:grid-cols-[140px_1fr_1fr_auto] gap-3 items-center">
                            <input name="social_platform[__INDEX__]" class="field" placeholder="github">
                            <input name="social_label[__INDEX__]" class="field" placeholder="Label (GitHub)">
                            <input name="social_url[__INDEX__]" class="field" placeholder="https://...">
                            <button type="button" data-repeater-remove
                                    class="w-9 h-9 rounded-lg grid place-items-center hover:text-red-400 transition-colors"
                                    style="background:var(--surface);border:1px solid var(--border)" title="Hapus baris">×</button>
                        </div>
                    </template>

                    <p class="text-xs mt-4" style="color:var(--muted)">
                        Isi <span class="font-medium" style="color:var(--text)">platform</span> dengan nama singkat (github, linkedin, instagram, email).
                        Baris yang tautannya kosong akan otomatis diabaikan.
                    </p>
                </div>
            </div>

            {{-- ===== Sidebar ===== --}}
            <div class="space-y-6 lg:sticky lg:top-[86px]">
                <div class="card p-6" data-reveal="right">
                    <h3 class="font-semibold mb-4">Foto Profil</h3>

                    <div class="aspect-square rounded-2xl overflow-hidden mb-4 grid place-items-center"
                         id="avatar-preview"
                         style="background:var(--surface);border:1px dashed var(--border-strong)">
                        @if ($avatar = media_url($profile->avatar))
                            <img src="{{ $avatar }}" alt="Foto profil" class="w-full h-full object-cover">
                        @else
                            <span class="text-4xl opacity-50">🖼️</span>
                        @endif
                    </div>

                    <label class="field-label" for="avatar">Pilih gambar</label>
                    <input id="avatar" name="avatar" type="file" accept="image/*" class="field"
                           data-preview="#avatar-preview">

                    <p class="text-xs mt-2" style="color:var(--muted)">
                        Format JPG, PNG, atau WebP. Maksimal 4 MB. Rasio persegi paling bagus.
                    </p>
                </div>

                <div class="card p-6" data-reveal="right">
                    <h3 class="font-semibold mb-4">Berkas CV</h3>

                    @if ($cv = media_url($profile->cv_file))
                        <a href="{{ $cv }}" target="_blank" rel="noopener"
                           class="flex items-center gap-3 p-3 rounded-xl mb-3 text-sm transition-colors hover:bg-[var(--surface)]"
                           style="border:1px solid var(--border)">
                            <span>📄</span>
                            <span class="truncate">CV saat ini</span>
                            <span class="ml-auto text-xs" style="color:var(--muted)">buka ↗</span>
                        </a>
                    @endif

                    <label class="field-label" for="cv_file">Unggah CV baru</label>
                    <input id="cv_file" name="cv_file" type="file" accept=".pdf,.doc,.docx" class="field">

                    <p class="text-xs mt-2" style="color:var(--muted)">PDF atau Word, maksimal 8 MB.</p>
                </div>

                <div class="card p-6" data-reveal="right">
                    <button type="submit" class="btn btn-primary w-full">💾 Simpan Perubahan</button>
                    <a href="{{ route('portfolio.home') }}" target="_blank" class="btn btn-ghost w-full mt-3">Lihat Hasil →</a>
                </div>
            </div>
        </div>
    </form>
@endsection
