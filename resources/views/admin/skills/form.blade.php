@extends('layouts.admin')

@section('title', $row->exists ? 'Edit Keahlian' : 'Tambah Keahlian')
@section('subtitle', $row->exists ? $row->name : 'Keahlian baru')

@section('content')
    <form action="{{ $row->exists ? route('admin.skills.update', $row) : route('admin.skills.store') }}"
          method="POST" class="max-w-2xl space-y-6">
        @csrf
        @if ($row->exists) @method('PUT') @endif

        <a href="{{ route('admin.skills.index') }}" class="text-sm inline-flex hover:text-[var(--accent)] transition-colors" style="color:var(--muted)">
            ← Kembali ke daftar keahlian
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
                    <label class="field-label" for="name">Nama Keahlian <span style="color:var(--accent)">*</span></label>
                    <input id="name" name="name" class="field" required value="{{ old('name', $row->name) }}" placeholder="Laravel">
                </div>
                <div>
                    <label class="field-label" for="icon">Ikon</label>
                    <input id="icon" name="icon" class="field text-center" value="{{ old('icon', $row->icon) }}" placeholder="🐘">
                </div>
            </div>

            <div class="mt-4">
                <label class="field-label" for="category">Kategori <span class="text-[10px]">(dipakai untuk pengelompokan)</span></label>
                <input id="category" name="category" class="field" list="skill-categories"
                       value="{{ old('category', $row->category) }}" placeholder="Backend">
                <datalist id="skill-categories">
                    @foreach ($categories as $cat) <option value="{{ $cat }}"></option> @endforeach
                </datalist>
            </div>

            <div class="mt-4">
                <label class="field-label" for="level">Tingkat Penguasaan: <span id="level-value" style="color:var(--accent)">{{ old('level', $row->level ?? 80) }}%</span></label>
                <input id="level" name="level" type="range" min="0" max="100" class="field" style="height:auto"
                       value="{{ old('level', $row->level ?? 80) }}"
                       oninput="document.getElementById('level-value').textContent = this.value + '%'">
                <p class="text-xs mt-1.5" style="color:var(--muted)">Menentukan panjang bilah progres di website.</p>
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
            <a href="{{ route('admin.skills.index') }}" class="btn btn-ghost btn-sm">Batal</a>
            <button type="submit" class="btn btn-primary btn-sm">{{ $row->exists ? '💾 Simpan' : '+ Tambahkan' }}</button>
        </div>
    </form>
@endsection
