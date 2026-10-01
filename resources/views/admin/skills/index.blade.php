@extends('layouts.admin')

@section('title', 'Keahlian')
@section('subtitle', 'Daftar keahlian yang tampil di website')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6" data-reveal>
        <span class="text-sm" style="color:var(--muted)">
            {{ $rows->count() }} keahlian · {{ $rows->where('is_active', true)->count() }} ditampilkan
        </span>
        <a href="{{ route('admin.skills.create') }}" class="btn btn-primary btn-sm">+ Tambah Keahlian</a>
    </div>

    @if ($rows->isEmpty())
        <div class="card p-12 text-center" data-reveal>
            <p class="text-4xl mb-4">⚡</p>
            <h3 class="font-semibold">Belum ada keahlian</h3>
            <p class="text-sm mt-2" style="color:var(--muted)">Tambahkan keahlian agar pengunjung tahu kemampuan Anda.</p>
            <a href="{{ route('admin.skills.create') }}" class="btn btn-primary btn-sm mt-6">+ Tambah Keahlian</a>
        </div>
    @else
        <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach ($rows as $i => $row)
                <div class="card p-5" data-reveal="zoom" style="--reveal-delay:{{ min($i * 50, 350) }}ms">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="w-11 h-11 rounded-xl grid place-items-center text-xl shrink-0"
                                  style="background:color-mix(in srgb,var(--accent) 14%,transparent)">{{ $row->icon ?: '⚙️' }}</span>
                            <div class="min-w-0">
                                <p class="font-medium truncate">{{ $row->name }}</p>
                                <p class="text-xs" style="color:var(--muted)">{{ $row->category ?: 'Tanpa kategori' }}</p>
                            </div>
                        </div>

                        <span class="badge {{ $row->is_active ? 'badge-on' : 'badge-off' }}">
                            {{ $row->is_active ? 'Aktif' : 'Off' }}
                        </span>
                    </div>

                    <div class="mt-4 flex items-center gap-3">
                        <div class="skill-bar flex-1"><span data-skill-level="{{ $row->level }}"></span></div>
                        <span class="text-xs font-semibold shrink-0" style="color:var(--accent)">{{ $row->level }}%</span>
                    </div>

                    <div class="mt-5 pt-4 flex items-center gap-2 border-t border-[var(--border)]">
                        <a href="{{ route('admin.skills.edit', $row) }}" class="btn btn-ghost btn-sm flex-1">✏️ Edit</a>

                        <form action="{{ route('admin.skills.destroy', $row) }}" method="POST"
                              data-confirm="Keahlian &quot;{{ $row->name }}&quot; akan dihapus permanen."
                              data-confirm-title="Hapus keahlian ini?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-lg grid place-items-center text-xs hover:text-red-400 transition-colors"
                                    style="background:var(--surface);border:1px solid var(--border)">🗑️</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
