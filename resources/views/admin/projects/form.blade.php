@extends('layouts.admin')

@section('title', $project->exists ? 'Edit Proyek' : 'Tambah Proyek')
@section('subtitle', $project->exists ? $project->title : 'Proyek baru untuk portofolio Anda')

@php
    $isEdit = $project->exists;
    $thumb = media_url($project->thumbnail);
    $gallery = collect($project->gallery ?? [])->map(fn ($p) => ['path' => $p, 'url' => media_url($p)])->filter(fn ($g) => $g['url']);
@endphp

@section('content')
    <form action="{{ $isEdit ? route('admin.projects.update', $project) : route('admin.projects.store') }}"
          method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div class="flex items-center justify-between gap-3" data-reveal>
            <a href="{{ route('admin.projects.index') }}" class="text-sm hover:text-[var(--accent)] transition-colors" style="color:var(--muted)">
                ← Kembali ke daftar proyek
            </a>

            <div class="flex gap-2">
                @if ($isEdit)
                    <a href="{{ route('portfolio.project', $project) }}" target="_blank" class="btn btn-ghost btn-sm">↗ Lihat</a>
                @endif
                <button type="submit" class="btn btn-primary btn-sm">💾 Simpan</button>
            </div>
        </div>

        @if ($errors->any())
            <div class="card p-4" style="border-color:color-mix(in srgb,#ef4444 40%,transparent)">
                <p class="text-sm font-medium mb-2">Ada isian yang perlu diperbaiki:</p>
                <ul class="text-sm list-disc list-inside space-y-0.5" style="color:var(--muted)">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="grid lg:grid-cols-[1fr_340px] gap-6 items-start">
            <div class="space-y-6">
                <div class="card p-6" data-reveal>
                    <h3 class="font-semibold mb-5">Informasi Utama</h3>

                    <div>
                        <label class="field-label" for="title">Judul Proyek <span style="color:var(--accent)">*</span></label>
                        <input id="title" name="title" class="field" required value="{{ old('title', $project->title) }}"
                               placeholder="Sistem Informasi Akademik">
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="field-label" for="category">Kategori</label>
                            <input id="category" name="category" class="field" list="category-list"
                                   value="{{ old('category', $project->category) }}" placeholder="Web App">
                            <datalist id="category-list">
                                @foreach ($categories as $cat) <option value="{{ $cat }}"></option> @endforeach
                            </datalist>
                        </div>
                        <div>
                            <label class="field-label" for="completed_at">Tanggal Selesai</label>
                            <input id="completed_at" name="completed_at" class="field" type="month"
                                   value="{{ old('completed_at', $project->completed_at) }}">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="field-label" for="short_description">Deskripsi Singkat <span class="text-[10px]">(muncul di kartu, max 300 karakter)</span></label>
                        <textarea id="short_description" name="short_description" rows="2" class="field" maxlength="300">{{ old('short_description', $project->short_description) }}</textarea>
                    </div>

                    <div class="mt-4">
                        <label class="field-label" for="description">Deskripsi Lengkap <span class="text-[10px]">(muncul di halaman detail)</span></label>
                        <textarea id="description" name="description" rows="8" class="field">{{ old('description', $project->description) }}</textarea>
                    </div>
                </div>

                <div class="card p-6" data-reveal>
                    <h3 class="font-semibold mb-5">Teknologi & Tautan</h3>

                    <div>
                        <label class="field-label" for="tech_stack">Teknologi <span class="text-[10px]">(pisahkan dengan koma)</span></label>
                        <input id="tech_stack" name="tech_stack" class="field"
                               value="{{ old('tech_stack', implode(', ', $project->tech_stack ?? [])) }}"
                               placeholder="Laravel, Tailwind CSS, MySQL">
                        <p class="text-xs mt-1.5" style="color:var(--muted)">Ditampilkan sebagai label kecil pada kartu proyek.</p>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="field-label" for="demo_url">Tautan Demo</label>
                            <input id="demo_url" name="demo_url" class="field" value="{{ old('demo_url', $project->demo_url) }}"
                                   placeholder="https://demo.example.com">
                        </div>
                        <div>
                            <label class="field-label" for="repo_url">Tautan Repositori</label>
                            <input id="repo_url" name="repo_url" class="field" value="{{ old('repo_url', $project->repo_url) }}"
                                   placeholder="https://github.com/...">
                        </div>
                    </div>
                </div>

                {{-- Galeri --}}
                <div class="card p-6" data-reveal>
                    <h3 class="font-semibold mb-2">Galeri Foto</h3>
                    <p class="text-xs mb-5" style="color:var(--muted)">Foto tambahan yang muncul di halaman detail proyek.</p>

                    @if ($gallery->isNotEmpty())
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
                            @foreach ($gallery as $image)
                                <label class="gallery-thumb cursor-pointer block relative group">
                                    <img src="{{ $image['url'] }}" alt="Galeri" loading="lazy">

                                    <input type="checkbox" name="remove_gallery[]" value="{{ $image['path'] }}"
                                           class="absolute top-2 left-2 w-4 h-4 rounded z-10">

                                    <span class="absolute inset-0 grid place-items-center text-[10px] font-medium text-white
                                                 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity">
                                        Centang untuk hapus
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    <label class="field-label" for="gallery_files">Tambah foto baru</label>
                    <input id="gallery_files" name="gallery[]" type="file" accept="image/*" multiple class="field">
                    <p class="text-xs mt-1.5" style="color:var(--muted)">Bisa pilih beberapa file sekaligus. Maksimal 5 MB per gambar.</p>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6 lg:sticky lg:top-[86px]">
                <div class="card p-6" data-reveal="right">
                    <h3 class="font-semibold mb-4">Thumbnail</h3>

                    <div class="aspect-[16/10] rounded-xl overflow-hidden mb-4 grid place-items-center"
                         id="thumb-preview"
                         style="background:var(--surface);border:1px dashed var(--border-strong)">
                        @if ($thumb)
                            <img src="{{ $thumb }}" alt="Thumbnail" class="w-full h-full object-cover">
                        @else
                            <span class="text-3xl opacity-40">🖼️</span>
                        @endif
                    </div>

                    <input name="thumbnail" type="file" accept="image/*" class="field" data-preview="#thumb-preview">
                    <p class="text-xs mt-2" style="color:var(--muted)">Rasio 16:10 paling rapi. Maksimal 5 MB.</p>
                </div>

                <div class="card p-6" data-reveal="right">
                    <h3 class="font-semibold mb-5">Status</h3>

                    <label class="flex items-center gap-3 cursor-pointer select-none mb-4">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $project->is_active)) class="w-4 h-4 rounded">
                        <span class="text-sm">Tampilkan di website</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer select-none mb-5">
                        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $project->is_featured)) class="w-4 h-4 rounded">
                        <span class="text-sm">Tandai sebagai unggulan</span>
                    </label>

                    <div>
                        <label class="field-label" for="sort_order">Urutan Tampil</label>
                        <input id="sort_order" name="sort_order" type="number" min="0" max="9999" class="field"
                               value="{{ old('sort_order', $project->sort_order ?? 0) }}">
                        <p class="text-xs mt-1.5" style="color:var(--muted)">Angka kecil tampil lebih dulu.</p>
                    </div>
                </div>

                <div class="card p-6" data-reveal="right">
                    <button type="submit" class="btn btn-primary w-full">
                        {{ $isEdit ? '💾 Simpan Perubahan' : '+ Tambahkan Proyek' }}
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
