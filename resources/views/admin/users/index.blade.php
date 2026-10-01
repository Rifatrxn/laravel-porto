@extends('layouts.admin')

@section('title', 'Kelola Akun')
@section('subtitle', 'Siapa saja yang boleh masuk dan mengubah isi website')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6" data-reveal>
        <span class="text-sm" style="color:var(--muted)">
            {{ $rows->count() }} akun · {{ $admins }} admin
        </span>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">+ Tambah Akun</a>
    </div>

    {{-- Penjelasan singkat peran --}}
    <div class="card p-5 mb-6" data-reveal>
        <h3 class="font-semibold text-sm mb-3">Peran akun</h3>
        <div class="grid sm:grid-cols-2 gap-3 text-xs" style="color:var(--muted)">
            <div class="p-3 rounded-xl" style="background:var(--surface);border:1px solid var(--border)">
                <p class="font-medium mb-1" style="color:var(--text)">Admin — akses penuh</p>
                <p>Bisa mengubah semua konten <em>dan</em> mengelola akun (tambah, ubah peran, nonaktifkan, hapus).</p>
            </div>
            <div class="p-3 rounded-xl" style="background:var(--surface);border:1px solid var(--border)">
                <p class="font-medium mb-1" style="color:var(--text)">Editor — hanya konten</p>
                <p>Bisa mengubah profil, proyek, keahlian, pengalaman, pendidikan, layanan, testimoni, dan pengaturan tampilan.</p>
            </div>
        </div>
    </div>

    @if ($rows->isEmpty())
        <div class="card p-12 text-center" data-reveal>
            <p class="text-4xl mb-4">🔐</p>
            <h3 class="font-semibold">Belum ada akun</h3>
            <p class="text-sm mt-2" style="color:var(--muted)">Tambahkan akun untuk bisa masuk ke panel.</p>
        </div>
    @else
        <div class="card overflow-hidden" data-reveal>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="background:var(--surface)">
                            <th class="text-left font-medium px-5 py-3.5 text-xs uppercase tracking-wider" style="color:var(--muted)">Akun</th>
                            <th class="text-left font-medium px-5 py-3.5 text-xs uppercase tracking-wider hidden sm:table-cell" style="color:var(--muted)">Peran</th>
                            <th class="text-left font-medium px-5 py-3.5 text-xs uppercase tracking-wider hidden md:table-cell" style="color:var(--muted)">Status</th>
                            <th class="text-left font-medium px-5 py-3.5 text-xs uppercase tracking-wider hidden lg:table-cell" style="color:var(--muted)">Login Terakhir</th>
                            <th class="px-5 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            @php
                                $isMe = $row->id === auth()->id();
                                $avatar = media_url($row->avatar);
                            @endphp
                            <tr class="border-t border-[var(--border)]">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        @if ($avatar)
                                            <img src="{{ $avatar }}" alt="{{ $row->name }}" class="w-9 h-9 rounded-full object-cover shrink-0">
                                        @else
                                            <span class="w-9 h-9 rounded-full grid place-items-center text-[11px] font-bold text-white shrink-0"
                                                  style="background:linear-gradient(120deg,var(--accent),var(--accent-2))">{{ initials($row->name) }}</span>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="font-medium truncate">
                                                {{ $row->name }}
                                                @if ($isMe)
                                                    <span class="badge badge-on ml-1">Anda</span>
                                                @endif
                                            </p>
                                            <p class="text-xs truncate" style="color:var(--muted)">{{ $row->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 hidden sm:table-cell">
                                    <span class="badge {{ $row->isAdmin() ? 'badge-on' : '' }}"
                                          style="{{ $row->isAdmin() ? '' : 'background:var(--surface);border:1px solid var(--border)' }}">
                                        {{ $row->isAdmin() ? '👑 Admin' : '✏️ Editor' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 hidden md:table-cell">
                                    <span class="badge {{ $row->is_active ? 'badge-on' : 'badge-off' }}">
                                        {{ $row->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 hidden lg:table-cell text-xs" style="color:var(--muted)">
                                    {{ $row->last_login_at ? $row->last_login_at->diffForHumans() : 'Belum pernah' }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.users.edit', $row) }}" class="btn btn-ghost btn-sm">✏️ Edit</a>
                                        @if ($isMe)
                                            <span class="text-[10px] px-2 py-1 rounded-lg" style="background:var(--surface);border:1px solid var(--border);color:var(--muted)">
                                                akun aktif
                                            </span>
                                        @else
                                            <form action="{{ route('admin.users.destroy', $row) }}" method="POST"
                                                  data-confirm="Akun &quot;{{ $row->name }}&quot; ({{ $row->email }}) akan dihapus dan tidak bisa masuk lagi."
                                                  data-confirm-title="Hapus akun ini?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-8 h-8 rounded-lg grid place-items-center text-xs hover:text-red-400 transition-colors"
                                                        style="background:var(--surface);border:1px solid var(--border)">🗑️</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <p class="text-xs mt-5" data-reveal style="color:var(--muted)">
        Catatan: admin tidak bisa menghapus atau menurunkan akunnya sendiri, dan admin aktif terakhir selalu dipertahankan
        supaya panel tidak pernah terkunci.
    </p>
@endsection
