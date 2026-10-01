@extends('layouts.admin')

@section('title', 'Testimoni')
@section('subtitle', 'Ulasan dari klien atau rekan kerja')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6" data-reveal>
        <span class="text-sm" style="color:var(--muted)">{{ $rows->count() }} testimoni</span>
        <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary btn-sm">+ Tambah Testimoni</a>
    </div>

    @if ($rows->isEmpty())
        <div class="card p-12 text-center" data-reveal>
            <p class="text-4xl mb-4">⭐</p>
            <h3 class="font-semibold">Belum ada testimoni</h3>
            <p class="text-sm mt-2" style="color:var(--muted)">Tambahkan ulasan dari klien Anda.</p>
        </div>
    @else
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach ($rows as $i => $row)
                @php $avatar = media_url($row->avatar); @endphp
                <div class="card p-5 flex flex-col" data-reveal="zoom" style="--reveal-delay:{{ min($i * 60, 300) }}ms">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex gap-0.5 text-sm">
                            @for ($star = 1; $star <= 5; $star++)
                                <span style="opacity:{{ $star <= $row->rating ? 1 : .25 }}">⭐</span>
                            @endfor
                        </div>
                        <span class="badge {{ $row->is_active ? 'badge-on' : 'badge-off' }}">
                            {{ $row->is_active ? 'Aktif' : 'Off' }}
                        </span>
                    </div>

                    <p class="text-sm mt-4 flex-1 leading-relaxed" style="color:var(--muted)">"{{ $row->message }}"</p>

                    <div class="mt-5 pt-4 border-t border-[var(--border)] flex items-center gap-3">
                        @if ($avatar)
                            <img src="{{ $avatar }}" alt="{{ $row->name }}" class="w-10 h-10 rounded-full object-cover">
                        @else
                            <span class="w-10 h-10 rounded-full grid place-items-center text-[11px] font-bold text-white"
                                  style="background:linear-gradient(120deg,var(--accent),var(--accent-2))">{{ initials($row->name) }}</span>
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium truncate">{{ $row->name }}</p>
                            <p class="text-xs truncate" style="color:var(--muted)">
                                {{ trim(($row->role ?? '').($row->company ? ', '.$row->company : '')) }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center gap-2">
                        <a href="{{ route('admin.testimonials.edit', $row) }}" class="btn btn-ghost btn-sm flex-1">✏️ Edit</a>
                        <form action="{{ route('admin.testimonials.destroy', $row) }}" method="POST"
                              data-confirm="Testimoni dari &quot;{{ $row->name }}&quot; akan dihapus permanen."
                              data-confirm-title="Hapus testimoni ini?">
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
