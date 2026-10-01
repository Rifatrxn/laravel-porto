@extends('layouts.admin')

@section('title', $row->exists ? 'Edit Layanan' : 'Tambah Layanan')
@section('subtitle', $row->exists ? $row->title : 'Layanan baru')

@section('content')
    <form action="{{ $row->exists ? route('admin.services.update', $row) : route('admin.services.store') }}"
          method="POST" class="max-w-2xl space-y-6">
        @csrf
        @if ($row->exists) @method('PUT') @endif

        <a href="{{ route('admin.services.index') }}" class="text-sm inline-flex hover:text-[var(--accent)] transition-colors" style="color:var(--muted)">
            ← Kembali ke daftar layanan
        </a>

        @if ($errors->any())
            <div class="card p-4" style="border-color:color-mix(in srgb,#ef4444 40%,transparent)">
                <ul class="text-sm list-disc list-inside space-y-0.5" style="color:var(--muted)">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="card p-6" data-reveal>
            <div class="grid sm:grid-cols-[1fr_100px] gap-4">
                <div>
                    <label class="field-label" for="title">Nama Layanan <span style="color:var(--accent)">*</span></label>
                    <input id="title" name="title" class="field" required value="{{ old('title', $row->title) }}"
                           placeholder="Pengembangan Web">
                </div>
                <div>
                    <label class="field-label" for="icon">Ikon</label>
                    <input id="icon" name="icon" class="field text-center" value="{{ old('icon', $row->icon) }}" placeholder="🌐">
                </div>
            </div>

            <div class="mt-4">
                <label class="field-label" for="description">Deskripsi</label>
                <textarea id="description" name="description" rows="3" class="field">{{ old('description', $row->description) }}</textarea>
            </div>

            <div class="mt-4">
                <label class="field-label" for="points_text">Poin Fitur <span class="text-[10px]">(satu poin per baris)</span></label>
                <textarea id="points_text" name="points_text" rows="4" class="field"
                          placeholder="Laravel / PHP&#10;Tailwind CSS&#10;REST API">{{ old('points_text', implode("\n", $row->points ?? [])) }}</textarea>
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
            <a href="{{ route('admin.services.index') }}" class="btn btn-ghost btn-sm">Batal</a>
            <button type="submit" class="btn btn-primary btn-sm">{{ $row->exists ? '💾 Simpan' : '+ Tambahkan' }}</button>
        </div>
    </form>
@endsection
