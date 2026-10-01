@extends('layouts.admin')

@section('title', $row->exists ? 'Edit Akun' : 'Tambah Akun')
@section('subtitle', $row->exists ? $row->name : 'Akun baru untuk masuk ke panel')

@section('content')
    <form action="{{ $row->exists ? route('admin.users.update', $row) : route('admin.users.store') }}"
          method="POST" class="max-w-3xl space-y-6">
        @csrf
        @if ($row->exists) @method('PUT') @endif

        <a href="{{ route('admin.users.index') }}" class="text-sm inline-flex hover:text-[var(--accent)] transition-colors" style="color:var(--muted)">
            ← Kembali ke daftar akun
        </a>

        @if ($errors->any())
            <div class="card p-4" style="border-color:color-mix(in srgb,#ef4444 40%,transparent)">
                <ul class="text-sm list-disc list-inside space-y-0.5" style="color:var(--muted)">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        @php
            $isMe = $row->exists && $row->id === auth()->id();
        @endphp

        {{-- Identitas --}}
        <div class="card p-6" data-reveal>
            <h3 class="font-semibold mb-4">Identitas</h3>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="field-label" for="name">Nama <span style="color:var(--accent)">*</span></label>
                    <input id="name" name="name" class="field" required
                           value="{{ old('name', $row->name) }}" placeholder="Budi Santoso">
                </div>
                <div>
                    <label class="field-label" for="email">Email <span style="color:var(--accent)">*</span></label>
                    <input id="email" name="email" type="email" class="field" required
                           value="{{ old('email', $row->email) }}" placeholder="budi@contoh.com">
                    <p class="text-[11px] mt-1.5" style="color:var(--muted)">Dipakai untuk login ke panel.</p>
                </div>
            </div>
        </div>

        {{-- Kata sandi --}}
        <div class="card p-6" data-reveal>
            <h3 class="font-semibold mb-1">Kata Sandi</h3>
            <p class="text-xs mb-4" style="color:var(--muted)">
                @if ($row->exists)
                    Biarkan kosong kalau tidak ingin mengubah kata sandi.
                @else
                    Minimal 8 karakter.
                @endif
            </p>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="field-label" for="password">
                        Kata Sandi @if (! $row->exists) <span style="color:var(--accent)">*</span> @endif
                    </label>
                    <input id="password" name="password" type="password" class="field" autocomplete="new-password"
                           {{ $row->exists ? '' : 'required' }} placeholder="{{ $row->exists ? '••••••••' : 'Minimal 8 karakter' }}">
                </div>
                <div>
                    <label class="field-label" for="password_confirmation">Ulangi Kata Sandi</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="field"
                           autocomplete="new-password" placeholder="Ulangi kata sandi">
                </div>
            </div>
        </div>

        {{-- Peran & status --}}
        <div class="card p-6" data-reveal>
            <h3 class="font-semibold mb-4">Peran &amp; Status</h3>

            @if ($isMe)
                <div class="p-3 rounded-xl text-xs mb-4" style="background:var(--surface);border:1px dashed var(--border-strong);color:var(--muted)">
                    Ini akun Anda sendiri. Peran dan statusnya dikunci sebagai <strong style="color:var(--text)">Admin aktif</strong>
                    supaya Anda tidak terkunci dari panel.
                </div>
            @endif

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="field-label" for="role">Peran <span style="color:var(--accent)">*</span></label>
                    <select id="role" name="role" class="field" {{ $isMe ? 'disabled' : '' }}>
                        @foreach (\App\Models\User::ROLES as $value => $label)
                            <option value="{{ $value }}" @selected(old('role', $row->role) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @if ($isMe)
                        <input type="hidden" name="role" value="admin">
                    @endif
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-3 cursor-pointer select-none p-3 rounded-xl w-full"
                           style="background:var(--surface);border:1px solid var(--border)">
                        <input type="checkbox" name="is_active" value="1" class="w-4 h-4"
                               @checked(old('is_active', $row->is_active ?? true)) {{ $isMe ? 'disabled' : '' }}>
                        <span class="text-sm">
                            <span class="block font-medium">Akun aktif</span>
                            <span class="block text-xs" style="color:var(--muted)">Kalau dimatikan, akun tidak bisa login.</span>
                        </span>
                    </label>
                    @if ($isMe)
                        <input type="hidden" name="is_active" value="1">
                    @endif
                </div>
            </div>
        </div>

        <div class="card p-4 flex justify-end gap-2" data-reveal>
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm">Batal</a>
            <button type="submit" class="btn btn-primary btn-sm">{{ $row->exists ? '💾 Simpan Perubahan' : '+ Buat Akun' }}</button>
        </div>
    </form>
@endsection
