@extends('layouts.admin')

@section('title', 'Pengalaman Kerja')
@section('subtitle', 'Riwayat pekerjaan dan magang')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6" data-reveal>
        <span class="text-sm" style="color:var(--muted)">{{ $rows->count() }} pengalaman</span>
        <a href="{{ route('admin.experiences.create') }}" class="btn btn-primary btn-sm">+ Tambah Pengalaman</a>
    </div>

    @if ($rows->isEmpty())
        <div class="card p-12 text-center" data-reveal>
            <p class="text-4xl mb-4">💼</p>
            <h3 class="font-semibold">Belum ada pengalaman</h3>
            <p class="text-sm mt-2" style="color:var(--muted)">Tambahkan riwayat kerja Anda.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($rows as $i => $row)
                <div class="card p-5" data-reveal="left" style="--reveal-delay:{{ min($i * 60, 300) }}ms">
                    <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="font-semibold">{{ $row->position }}</h3>
                                @if ($row->is_current)
                                    <span class="badge badge-on">Sekarang</span>
                                @endif
                                <span class="badge {{ $row->is_active ? 'badge-on' : 'badge-off' }}">
                                    {{ $row->is_active ? 'Aktif' : 'Off' }}
                                </span>
                            </div>

                            <p class="text-sm mt-1" style="color:var(--muted)">
                                {{ $row->company }}
                                @if ($row->employment_type) · {{ $row->employment_type }} @endif
                                @if ($row->location) · {{ $row->location }} @endif
                            </p>

                            <p class="text-xs mt-1.5" style="color:var(--accent)">
                                {{ period_label($row->start_date, $row->end_date, $row->is_current) }}
                            </p>

                            @if ($row->description)
                                <p class="text-sm mt-2.5" style="color:var(--muted)">{{ $row->description }}</p>
                            @endif

                            @if (!empty($row->highlights))
                                <ul class="mt-2.5 space-y-1">
                                    @foreach ($row->highlights as $point)
                                        <li class="text-xs flex items-start gap-2" style="color:var(--muted)">
                                            <span style="color:var(--accent)">✓</span>{{ $point }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <div class="flex sm:flex-col gap-2 shrink-0">
                            <a href="{{ route('admin.experiences.edit', $row) }}" class="btn btn-ghost btn-sm flex-1 sm:flex-none">✏️ Edit</a>
                            <form action="{{ route('admin.experiences.destroy', $row) }}" method="POST"
                                  data-confirm="Pengalaman di &quot;{{ $row->company }}&quot; akan dihapus permanen."
                                  data-confirm-title="Hapus pengalaman ini?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full sm:w-9 h-9 rounded-lg grid place-items-center text-xs hover:text-red-400 transition-colors"
                                        style="background:var(--surface);border:1px solid var(--border)">🗑️</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
