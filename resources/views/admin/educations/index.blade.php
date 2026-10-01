@extends('layouts.admin')

@section('title', 'Pendidikan')
@section('subtitle', 'Riwayat pendidikan formal')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6" data-reveal>
        <span class="text-sm" style="color:var(--muted)">{{ $rows->count() }} riwayat pendidikan</span>
        <a href="{{ route('admin.educations.create') }}" class="btn btn-primary btn-sm">+ Tambah Pendidikan</a>
    </div>

    @if ($rows->isEmpty())
        <div class="card p-12 text-center" data-reveal>
            <p class="text-4xl mb-4">🎓</p>
            <h3 class="font-semibold">Belum ada data pendidikan</h3>
        </div>
    @else
        <div class="grid md:grid-cols-2 gap-4">
            @foreach ($rows as $i => $row)
                <div class="card p-5" data-reveal="zoom" style="--reveal-delay:{{ min($i * 60, 300) }}ms">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="font-semibold">{{ $row->institution }}</h3>
                                @if ($row->is_current) <span class="badge badge-on">Aktif</span> @endif
                            </div>
                            <p class="text-sm mt-1" style="color:var(--muted)">
                                {{ trim(($row->degree ?? '').' '.($row->field_of_study ?? '')) ?: '—' }}
                            </p>
                        </div>
                        <span class="badge {{ $row->is_active ? 'badge-on' : 'badge-off' }} shrink-0">
                            {{ $row->is_active ? 'Aktif' : 'Off' }}
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs mt-3" style="color:var(--muted)">
                        <span>{{ period_label($row->start_year, $row->end_year, $row->is_current) }}</span>
                        @if ($row->grade) <span>· {{ $row->grade }}</span> @endif
                    </div>

                    @if ($row->description)
                        <p class="text-sm mt-3" style="color:var(--muted)">{{ $row->description }}</p>
                    @endif

                    <div class="mt-5 pt-4 flex items-center gap-2 border-t border-[var(--border)]">
                        <a href="{{ route('admin.educations.edit', $row) }}" class="btn btn-ghost btn-sm flex-1">✏️ Edit</a>
                        <form action="{{ route('admin.educations.destroy', $row) }}" method="POST"
                              data-confirm="Data pendidikan di &quot;{{ $row->institution }}&quot; akan dihapus permanen."
                              data-confirm-title="Hapus data ini?">
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
