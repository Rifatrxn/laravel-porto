@extends('layouts.admin')

@section('title', 'Pesan Masuk')
@section('subtitle', 'Pesan dari formulir kontak website')

@section('content')
    @if ($rows->isEmpty())
        <div class="card p-12 text-center" data-reveal>
            <p class="text-4xl mb-4">✉️</p>
            <h3 class="font-semibold">Belum ada pesan</h3>
            <p class="text-sm mt-2" style="color:var(--muted)">Pesan dari pengunjung lewat formulir kontak akan muncul di sini.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($rows as $i => $row)
                <a href="{{ route('admin.messages.show', $row) }}"
                   class="card p-5 flex items-start gap-4 hover:-translate-y-0.5 block"
                   data-reveal="left" style="--reveal-delay:{{ min($i * 40, 300) }}ms">
                    <span class="w-11 h-11 rounded-full grid place-items-center text-xs font-bold text-white shrink-0"
                          style="background:linear-gradient(120deg,var(--accent),var(--accent-2))">
                        {{ initials($row->name) }}
                    </span>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-medium">{{ $row->name }}</p>
                            @if (! $row->is_read)
                                <span class="badge badge-warn">Belum dibaca</span>
                            @endif
                        </div>

                        <p class="text-xs mt-0.5" style="color:var(--muted)">
                            {{ $row->email }} · {{ $row->created_at->translatedFormat('d M Y, H:i') }}
                        </p>

                        @if ($row->subject)
                            <p class="text-sm font-medium mt-2">{{ $row->subject }}</p>
                        @endif

                        <p class="text-sm mt-1.5 line-clamp-2" style="color:var(--muted)">{{ $row->body }}</p>
                    </div>

                    <span class="text-xs shrink-0" style="color:var(--muted)">→</span>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $rows->links() }}
        </div>
    @endif
@endsection
