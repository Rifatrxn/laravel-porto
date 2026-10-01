@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan isi website portofolio Anda')

@section('content')
    {{-- Sambutan --}}
    <div class="card p-6 sm:p-7 mb-6 relative overflow-hidden" data-reveal>
        <div class="absolute -right-16 -top-16 w-56 h-56 rounded-full opacity-20 pointer-events-none"
             style="background:radial-gradient(circle,var(--accent),transparent 70%)"></div>

        <div class="flex flex-col sm:flex-row sm:items-center gap-5">
            <div class="flex-1">
                <p class="text-xs font-semibold uppercase tracking-[0.16em]" style="color:var(--accent)">Selamat datang kembali</p>
                <h2 class="text-2xl font-bold mt-2">{{ auth()->user()->name ?? 'Admin' }} 👋</h2>
                <p class="text-sm mt-2" style="color:var(--muted)">
                    Semua isi website — teks, foto, warna, animasi — bisa diubah dari sini tanpa menyentuh kode.
                </p>

                <div class="mt-5 flex flex-wrap gap-2">
                    <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary btn-sm">👤 Edit Profil</a>
                    <a href="{{ route('admin.settings.edit') }}" class="btn btn-ghost btn-sm">⚙️ Pengaturan Tampilan</a>
                    <a href="{{ route('portfolio.home') }}" target="_blank" class="btn btn-ghost btn-sm">🌐 Lihat Website</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        @foreach ($stats as $i => $stat)
            <a href="{{ $stat['route'] }}" class="card p-5 hover:-translate-y-1 block" data-reveal="zoom"
               style="--reveal-delay:{{ $i * 60 }}ms">
                <div class="flex items-start justify-between">
                    <span class="w-10 h-10 rounded-xl grid place-items-center text-lg
                                 bg-[color-mix(in_srgb,var(--accent)_14%,transparent)]">{{ $stat['icon'] }}</span>
                    <span class="text-2xl font-bold text-gradient">{{ $stat['value'] }}</span>
                </div>
                <p class="mt-4 font-medium text-sm">{{ $stat['label'] }}</p>
                <p class="text-xs mt-0.5" style="color:var(--muted)">{{ $stat['active'] }} aktif ditampilkan</p>
            </a>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-[1.3fr_.7fr] gap-6">
        {{-- Pesan terbaru --}}
        <div class="card p-6" data-reveal="left">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-semibold">Pesan Terbaru</h3>
                <a href="{{ route('admin.messages.index') }}" class="text-xs hover:text-[var(--accent)] transition-colors" style="color:var(--muted)">
                    Lihat semua →
                </a>
            </div>

            @forelse ($latestMessages as $message)
                <a href="{{ route('admin.messages.show', $message) }}"
                   class="flex items-start gap-3 p-3 rounded-xl transition-colors hover:bg-[var(--surface)] border border-transparent hover:border-[var(--border)]">
                    <span class="w-9 h-9 rounded-full grid place-items-center text-[11px] font-bold text-white shrink-0"
                          style="background:linear-gradient(120deg,var(--accent),var(--accent-2))">
                        {{ initials($message->name) }}
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="flex items-center gap-2">
                            <span class="text-sm font-medium truncate">{{ $message->name }}</span>
                            @if (! $message->is_read)
                                <span class="badge badge-warn">Baru</span>
                            @endif
                        </span>
                        <span class="block text-xs truncate mt-0.5" style="color:var(--muted)">
                            {{ $message->subject ?: Str::limit($message->body, 60) }}
                        </span>
                    </span>
                    <span class="text-[10px] shrink-0" style="color:var(--muted)">
                        {{ $message->created_at->diffForHumans(short: true) }}
                    </span>
                </a>
            @empty
                <p class="text-sm py-6 text-center" style="color:var(--muted)">Belum ada pesan masuk.</p>
            @endforelse
        </div>

        {{-- Panduan cepat --}}
        <div class="card p-6" data-reveal="right">
            <h3 class="font-semibold mb-4">Panduan Cepat</h3>

            <ol class="space-y-4 text-sm">
                @foreach ([
                    ['👤', 'Edit Profil', 'Ubah nama, foto, bio, dan tautan sosial media Anda.'],
                    ['📁', 'Kelola Proyek', 'Tambah proyek beserta foto thumbnail dan galeri.'],
                    ['🎨', 'Pengaturan Tampilan', 'Ganti warna, latar, dan jenis animasi website.'],
                    ['✉️', 'Pesan Masuk', 'Baca pesan yang dikirim pengunjung lewat formulir kontak.'],
                ] as [$icon, $title, $desc])
                    <li class="flex gap-3">
                        <span class="w-8 h-8 rounded-lg grid place-items-center shrink-0 text-sm"
                              style="background:var(--surface);border:1px solid var(--border)">{{ $icon }}</span>
                        <span>
                            <span class="block font-medium">{{ $title }}</span>
                            <span class="block text-xs mt-0.5" style="color:var(--muted)">{{ $desc }}</span>
                        </span>
                    </li>
                @endforeach
            </ol>

            <div class="mt-6 p-4 rounded-xl text-xs" style="background:var(--surface);border:1px dashed var(--border-strong);color:var(--muted)">
                <p class="font-medium mb-1.5" style="color:var(--text)">Akses cepat website</p>
                <p>Beranda: <a href="{{ route('portfolio.home') }}" target="_blank" class="hover:text-[var(--accent)]" style="color:var(--accent)">/</a></p>
                <p class="mt-1">Daftar proyek: <a href="{{ route('portfolio.projects') }}" target="_blank" class="hover:text-[var(--accent)]" style="color:var(--accent)">/projects</a></p>
            </div>
        </div>
    </div>
@endsection
