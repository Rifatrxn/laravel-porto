@extends('layouts.admin')

@section('title', $row->exists ? 'Edit Pendidikan' : 'Tambah Pendidikan')
@section('subtitle', $row->exists ? $row->institution : 'Riwayat pendidikan baru')

@section('content')
    <form action="{{ $row->exists ? route('admin.educations.update', $row) : route('admin.educations.store') }}"
          method="POST" class="max-w-3xl space-y-6">
        @csrf
        @if ($row->exists) @method('PUT') @endif

        <a href="{{ route('admin.educations.index') }}" class="text-sm inline-flex hover:text-[var(--accent)] transition-colors" style="color:var(--muted)">
            ← Kembali ke daftar pendidikan
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
                <div class="sm:col-span-2">
                    <label class="field-label" for="institution">Nama Institusi <span style="color:var(--accent)">*</span></label>
                    <input id="institution" name="institution" class="field" required value="{{ old('institution', $row->institution) }}"
                           placeholder="Universitas Teknologi">
                </div>
                <div>
                    <label class="field-label" for="degree">Jenjang</label>
                    <input id="degree" name="degree" class="field" list="degrees"
                           value="{{ old('degree', $row->degree) }}" placeholder="S1">
                    <datalist id="degrees">
                        @foreach (['SMA/SMK', 'D3', 'D4', 'S1', 'S2', 'S3'] as $degree)
                            <option value="{{ $degree }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label class="field-label" for="field_of_study">Jurusan / Program Studi</label>
                    <input id="field_of_study" name="field_of_study" class="field"
                           value="{{ old('field_of_study', $row->field_of_study) }}" placeholder="Teknik Informatika">
                </div>
                <div>
                    <label class="field-label" for="start_year">Tahun Mulai</label>
                    <input id="start_year" name="start_year" class="field" value="{{ old('start_year', $row->start_year) }}"
                           placeholder="2022" maxlength="10">
                </div>
                <div>
                    <label class="field-label" for="end_year">Tahun Selesai</label>
                    <input id="end_year" name="end_year" class="field" value="{{ old('end_year', $row->end_year) }}"
                           placeholder="2026" maxlength="10">
                </div>
                <div>
                    <label class="field-label" for="grade">Nilai / IPK / Predikat</label>
                    <input id="grade" name="grade" class="field" value="{{ old('grade', $row->grade) }}"
                           placeholder="IPK 3.78">
                </div>
            </div>

            <label class="mt-4 flex items-center gap-3 cursor-pointer select-none p-3 rounded-xl"
                   style="background:var(--surface);border:1px solid var(--border)">
                <input type="checkbox" name="is_current" value="1" @checked(old('is_current', $row->is_current)) class="w-4 h-4 rounded">
                <span class="text-sm">Masih menempuh pendidikan ini</span>
            </label>

            <div class="mt-4">
                <label class="field-label" for="description">Keterangan Tambahan</label>
                <textarea id="description" name="description" rows="3" class="field">{{ old('description', $row->description) }}</textarea>
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
            <a href="{{ route('admin.educations.index') }}" class="btn btn-ghost btn-sm">Batal</a>
            <button type="submit" class="btn btn-primary btn-sm">{{ $row->exists ? '💾 Simpan' : '+ Tambahkan' }}</button>
        </div>
    </form>
@endsection
