@extends('layouts.admin')

@section('title', 'Layanan')
@section('subtitle', 'Jasa atau hal yang Anda tawarkan')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6" data-reveal>
        <span class="text-sm" style="color:var(--muted)">{{ $rows->count() }} layanan</span>
        <a href="{{ route('admin.services.create') }}" class="btn btn-primary btn-sm">+ Tambah Layanan</a>
    </div>

    @if ($rows->isEmpty())
        <div class="card p-12 text-center" data-reveal>
            <p class="text-4xl mb-4">🧩</p>
            <h3 class="font-semibold">Belum ada layanan</h3>
            <p class="text-sm mt-2" style="color:var(--muted)">Ceritakan jasa apa yang Anda tawarkan.</p>
        </div>
    @else
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach ($rows as $i => $row)
                <div class="card p-5 flex flex-col" data-reveal="zoom" style="--reveal-delay:{{ min($i * 60, 300) }}ms">
                    <div class="flex items-start justify-between gap-3">
                        <span class="w-11 h-11 rounded-xl grid place-items-center text-xl"
                              style="background:color-mix(in srgb,var(--accent) 14%,transparent)">{{ $row->icon ?: '✨' }}</span>
                        <span class="badge {{ $row->is_active ? 'badge-on' : 'badge-off' }}">
                            {{ $row->is_active ? 'Aktif' : 'Off' }}
                        </span>
                    </div>

                    <h3 class="font-semibold mt-4">{{ $row->title }}</h3>

                    @if ($row->description)
                        <p class="text-sm mt-2" style="color:var(--muted)">{{ $row->description }}</p>
                    @endif

                    @if (!empty($row->points))
                        <ul class="mt-3 space-y-1">
                            @foreach ($row->points as $point)
                                <li class="text-xs flex items-start gap-2" style="color:var(--muted)">
                                    <span style="color:var(--accent)">•</span>{{ $point }}
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="mt-auto pt-5 flex items-center gap-2">
                        <a href="{{ route('admin.services.edit', $row) }}" class="btn btn-ghost btn-sm flex-1">✏️ Edit</a>
                        <form action="{{ route('admin.services.destroy', $row) }}" method="POST"
                              data-confirm="Layanan &quot;{{ $row->title }}&quot; akan dihapus permanen."
                              data-confirm-title="Hapus layanan ini?">
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
