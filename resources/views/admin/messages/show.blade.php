@extends('layouts.admin')

@section('title', 'Detail Pesan')
@section('subtitle', $row->name.' · '.$row->email)

@section('content')
    <a href="{{ route('admin.messages.index') }}" class="text-sm inline-flex hover:text-[var(--accent)] transition-colors" style="color:var(--muted)">
        ← Kembali ke pesan masuk
    </a>

    <div class="card p-6 sm:p-7 mt-6 max-w-3xl" data-reveal>
        <div class="flex items-start gap-4">
            <span class="w-12 h-12 rounded-full grid place-items-center text-sm font-bold text-white shrink-0"
                  style="background:linear-gradient(120deg,var(--accent),var(--accent-2))">
                {{ initials($row->name) }}
            </span>

            <div class="min-w-0 flex-1">
                <h2 class="font-semibold text-lg">{{ $row->name }}</h2>
                <p class="text-sm" style="color:var(--muted)">
                    <a href="mailto:{{ $row->email }}" class="hover:text-[var(--accent)] transition-colors">{{ $row->email }}</a>
                </p>
                <p class="text-xs mt-1" style="color:var(--muted)">
                    Dikirim {{ $row->created_at->translatedFormat('l, d F Y — H:i') }}
                </p>
            </div>

            <span class="badge {{ $row->is_read ? 'badge-on' : 'badge-warn' }} shrink-0">
                {{ $row->is_read ? 'Sudah dibaca' : 'Baru' }}
            </span>
        </div>

        <div class="mt-6 pt-6 border-t border-[var(--border)]">
            @if ($row->subject)
                <p class="font-medium mb-3">{{ $row->subject }}</p>
            @endif

            <p class="leading-relaxed whitespace-pre-line" style="color:var(--muted)">{{ $row->body }}</p>
        </div>

        <div class="mt-7 pt-5 border-t border-[var(--border)] flex flex-wrap items-center gap-2">
            <a href="mailto:{{ $row->email }}?subject={{ urlencode('Re: '.($row->subject ?: 'Pesan dari website')) }}"
               class="btn btn-primary btn-sm">✉️ Balas lewat Email</a>

            <form action="{{ route('admin.messages.update', $row) }}" method="POST" class="ml-auto">
                @csrf
                @method('PUT')
                <input type="hidden" name="is_read" value="{{ $row->is_read ? 0 : 1 }}">
                <button type="submit" class="btn btn-ghost btn-sm">
                    {{ $row->is_read ? '↺ Tandai belum dibaca' : '✓ Tandai sudah dibaca' }}
                </button>
            </form>

            <form action="{{ route('admin.messages.destroy', $row) }}" method="POST"
                  data-confirm="Pesan dari &quot;{{ $row->name }}&quot; akan dihapus permanen."
                  data-confirm-title="Hapus pesan ini?">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-ghost btn-sm">🗑️ Hapus</button>
            </form>
        </div>
    </div>
@endsection
