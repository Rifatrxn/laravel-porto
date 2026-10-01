@extends('layouts.admin')

@section('title', 'Proyek')
@section('subtitle', 'Kelola daftar proyek portofolio')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6" data-reveal>
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-sm" style="color:var(--muted)">
                {{ $projects->count() }} proyek · {{ $projects->where('is_active', true)->count() }} ditampilkan
            </span>
        </div>

        <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn-sm">+ Tambah Proyek</a>
    </div>

    @if ($projects->isEmpty())
        <div class="card p-12 text-center" data-reveal>
            <p class="text-4xl mb-4">📁</p>
            <h3 class="font-semibold">Belum ada proyek</h3>
            <p class="text-sm mt-2" style="color:var(--muted)">Tambahkan proyek pertama Anda untuk ditampilkan di website.</p>
            <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn-sm mt-6">+ Tambah Proyek</a>
        </div>
    @else
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach ($projects as $i => $project)
                @php $thumb = media_url($project->thumbnail); @endphp

                <div class="card overflow-hidden flex flex-col" data-reveal="zoom" style="--reveal-delay:{{ min($i * 60, 360) }}ms">
                    <div class="aspect-[16/10] overflow-hidden relative">
                        @if ($thumb)
                            <img src="{{ $thumb }}" alt="{{ $project->title }}" class="w-full h-full object-cover" loading="lazy">
                        @else
                            <div class="w-full h-full grid place-items-center"
                                 style="background:linear-gradient(140deg,color-mix(in srgb,var(--accent) 24%,transparent),color-mix(in srgb,var(--accent-2) 24%,transparent))">
                                <span class="text-3xl opacity-70">📁</span>
                            </div>
                        @endif

                        <div class="absolute top-2.5 left-2.5 flex gap-1.5 flex-wrap">
                            <span class="badge {{ $project->is_active ? 'badge-on' : 'badge-off' }}">
                                {{ $project->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                            @if ($project->is_featured)
                                <span class="badge badge-warn">⭐ Unggulan</span>
                            @endif
                        </div>
                    </div>

                    <div class="p-4 flex-1 flex flex-col">
                        <p class="font-medium leading-snug">{{ $project->title }}</p>

                        <div class="flex flex-wrap items-center gap-2 mt-1.5 text-xs" style="color:var(--muted)">
                            @if ($project->category) <span>{{ $project->category }}</span> @endif
                            @if ($project->completed_at) <span>· {{ indonesian_month($project->completed_at) }}</span> @endif
                        </div>

                        @if ($project->short_description)
                            <p class="text-xs mt-2.5 line-clamp-2" style="color:var(--muted)">{{ $project->short_description }}</p>
                        @endif

                        @if (!empty($project->tech_stack))
                            <div class="flex flex-wrap gap-1 mt-3">
                                @foreach (array_slice($project->tech_stack, 0, 3) as $tech)
                                    <span class="text-[10px] px-2 py-0.5 rounded"
                                          style="background:var(--surface);border:1px solid var(--border);color:var(--muted)">{{ $tech }}</span>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-auto pt-4 flex items-center gap-2">
                            <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-ghost btn-sm flex-1">✏️ Edit</a>
                            <a href="{{ route('portfolio.project', $project) }}" target="_blank"
                               class="w-8 h-8 rounded-lg grid place-items-center text-xs"
                               style="background:var(--surface);border:1px solid var(--border)" title="Lihat di website">↗</a>

                            <form action="{{ route('admin.projects.destroy', $project) }}" method="POST"
                                  data-confirm="Proyek &quot;{{ $project->title }}&quot; beserta semua fotonya akan dihapus permanen."
                                  data-confirm-title="Hapus proyek ini?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg grid place-items-center text-xs hover:text-red-400 transition-colors"
                                        style="background:var(--surface);border:1px solid var(--border)" title="Hapus">🗑️</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
