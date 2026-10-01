@extends('layouts.admin')

@section('title', $row->exists ? 'Edit Testimoni' : 'Tambah Testimoni')
@section('subtitle', $row->exists ? $row->name : 'Testimoni baru')

@section('content')
    <form action="{{ $row->exists ? route('admin.testimonials.update', $row) : route('admin.testimonials.store') }}"
          method="POST" enctype="multipart/form-data" class="max-w-3xl space-y-6">
        @csrf
        @if ($row->exists) @method('PUT') @endif

        <a href="{{ route('admin.testimonials.index') }}" class="text-sm inline-flex hover:text-[var(--accent)] transition-colors" style="color:var(--muted)">
            ← Kembali ke daftar testimoni
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
                    <label class="field-label" for="name">Nama Pemberi Testimoni <span style="color:var(--accent)">*</span></label>
                    <input id="name" name="name" class="field" required value="{{ old('name', $row->name) }}" placeholder="Andi Saputra">
                </div>
                <div>
                    <label class="field-label" for="role">Jabatan</label>
                    <input id="role" name="role" class="field" value="{{ old('role', $row->role) }}" placeholder="Pemilik">
                </div>
                <div>
                    <label class="field-label" for="company">Perusahaan / Instansi</label>
                    <input id="company" name="company" class="field" value="{{ old('company', $row->company) }}" placeholder="Toko Maju Jaya">
                </div>
                <div>
                    <label class="field-label" for="rating">Penilaian</label>
                    <select id="rating" name="rating" class="field">
                        @for ($star = 5; $star >= 1; $star--)
                            <option value="{{ $star }}" @selected((int) old('rating', $row->rating) === $star)>
                                {{ str_repeat('⭐', $star) }} ({{ $star }})
                            </option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <label class="field-label" for="message">Isi Testimoni <span style="color:var(--accent)">*</span></label>
                <textarea id="message" name="message" rows="4" class="field" required>{{ old('message', $row->message) }}</textarea>
            </div>

            <div class="mt-4 flex items-start gap-4">
                <div class="w-20 h-20 rounded-xl overflow-hidden shrink-0 grid place-items-center"
                     id="avatar-preview"
                     style="background:var(--surface);border:1px dashed var(--border-strong)">
                    @if ($avatar = media_url($row->avatar))
                        <img src="{{ $avatar }}" alt="" class="w-full h-full object-cover">
                    @else
                        <span class="text-xl opacity-40">👤</span>
                    @endif
                </div>

                <div class="flex-1">
                    <label class="field-label" for="avatar">Foto (opsional)</label>
                    <input id="avatar" name="avatar" type="file" accept="image/*" class="field" data-preview="#avatar-preview">
                    <p class="text-xs mt-1.5" style="color:var(--muted)">Kalau kosong, akan tampil inisial nama.</p>
                </div>
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
            <a href="{{ route('admin.testimonials.index') }}" class="btn btn-ghost btn-sm">Batal</a>
            <button type="submit" class="btn btn-primary btn-sm">{{ $row->exists ? '💾 Simpan' : '+ Tambahkan' }}</button>
        </div>
    </form>
@endsection
