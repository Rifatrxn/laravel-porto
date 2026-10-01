@extends('layouts.admin')

@section('title', $row->exists ? 'Edit Pengalaman' : 'Tambah Pengalaman')
@section('subtitle', $row->exists ? $row->position.' — '.$row->company : 'Riwayat kerja baru')

@section('content')
    <form action="{{ $row->exists ? route('admin.experiences.update', $row) : route('admin.experiences.store') }}"
          method="POST" class="max-w-3xl space-y-6">
        @csrf
        @if ($row->exists) @method('PUT') @endif

        <a href="{{ route('admin.experiences.index') }}" class="text-sm inline-flex hover:text-[var(--accent)] transition-colors" style="color:var(--muted)">
            ← Kembali ke daftar pengalaman
        </a>

        @if ($errors->any())
            <div class="card p-4" style="border-color:color-mix(in srgb,#ef4444 40%,transparent)">
                <ul class="text-sm list-disc list-inside space-y-0.5" style="color:var(--muted)">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="card p-6" data-reveal>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="field-label" for="position">Posisi / Jabatan <span style="color:var(--accent)">*</span></label>
                    <input id="position" name="position" class="field" required value="{{ old('position', $row->position) }}"
                           placeholder="Web Developer">
                </div>
                <div>
                    <label class="field-label" for="company">Perusahaan / Instansi <span style="color:var(--accent)">*</span></label>
                    <input id="company" name="company" class="field" required value="{{ old('company', $row->company) }}"
                           placeholder="PT Contoh Sejahtera">
                </div>
                <div>
                    <label class="field-label" for="employment_type">Jenis Pekerjaan</label>
                    <input id="employment_type" name="employment_type" class="field" list="employment-types"
                           value="{{ old('employment_type', $row->employment_type) }}" placeholder="Full-time">
                    <datalist id="employment-types">
                        @foreach (['Full-time', 'Part-time', 'Freelance', 'Internship', 'Kontrak', 'Volunteer'] as $type)
                            <option value="{{ $type }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label class="field-label" for="location">Lokasi</label>
                    <input id="location" name="location" class="field" value="{{ old('location', $row->location) }}"
                           placeholder="Jakarta / Remote">
                </div>
                <div>
                    <label class="field-label" for="start_date">Mulai <span class="text-[10px]">(bulan)</span></label>
                    <input id="start_date" name="start_date" type="month" class="field" value="{{ old('start_date', $row->start_date) }}">
                </div>
                <div>
                    <label class="field-label" for="end_date">Selesai <span class="text-[10px]">(kosongkan jika masih berjalan)</span></label>
                    <input id="end_date" name="end_date" type="month" class="field" value="{{ old('end_date', $row->end_date) }}"
                           @disabled(old('is_current', $row->is_current))>
                </div>
            </div>

            <label class="mt-4 flex items-center gap-3 cursor-pointer select-none p-3 rounded-xl"
                   style="background:var(--surface);border:1px solid var(--border)">
                <input type="checkbox" name="is_current" value="1" id="is_current"
                       @checked(old('is_current', $row->is_current)) class="w-4 h-4 rounded"
                       onchange="document.getElementById('end_date').disabled = this.checked">
                <span class="text-sm">Masih bekerja di sini (tampilkan sebagai "Sekarang")</span>
            </label>

            <div class="mt-4">
                <label class="field-label" for="description">Deskripsi Pekerjaan</label>
                <textarea id="description" name="description" rows="3" class="field">{{ old('description', $row->description) }}</textarea>
            </div>

            <div class="mt-4">
                <label class="field-label" for="highlights_text">Poin Pencapaian <span class="text-[10px]">(satu poin per baris)</span></label>
                <textarea id="highlights_text" name="highlights_text" rows="4" class="field"
                          placeholder="Menyelesaikan 15+ proyek website&#10;Memangkas waktu laporan 60%">{{ old('highlights_text', implode("\n", $row->highlights ?? [])) }}</textarea>
            </div>

            <div class="mt-4 grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="field-label" for="sort_order">Urutan Tampil</label>
                    <input id="sort_order" name="sort_order" type="number" min="0" class="field"
                           value="{{ old('sort_order', $row->sort_order ?? 0) }}">
                </div>
                <div>
                    <label class="field-label">Status</label>
                    <label class="flex items-center gap-3 cursor-pointer select-none p-3 rounded-xl"
                           style="background:var(--surface);border:1px solid var(--border)">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $row->is_active)) class="w-4 h-4 rounded">
                        <span class="text-sm">Tampilkan di website</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="card p-4 flex justify-end gap-2" data-reveal>
            <a href="{{ route('admin.experiences.index') }}" class="btn btn-ghost btn-sm">Batal</a>
            <button type="submit" class="btn btn-primary btn-sm">{{ $row->exists ? '💾 Simpan' : '+ Tambahkan' }}</button>
        </div>
    </form>
@endsection
