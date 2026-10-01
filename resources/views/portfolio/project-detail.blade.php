@extends('layouts.portfolio')

@section('title', $project->title)

@php
    $gallery = collect($project->gallery ?? [])->map(fn ($p) => media_url($p))->filter()->values();
    $cover = media_url($project->thumbnail) ?? $gallery->first();
    $techs = $project->tech_stack ?? [];
@endphp

@section('content')
    <article class="pt-36 pb-16">
        <div class="container-x">
            <a href="{{ route('portfolio.projects') }}"
               class="text-sm inline-flex items-center gap-2 mb-6 hover:text-[var(--accent)] transition-colors" style="color:var(--muted)">
                ← Semua Proyek
            </a>

            <div class="max-w-3xl" data-reveal>
                @if ($project->category)
                    <span class="chip">{{ $project->category }}</span>
                @endif

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight mt-4">{{ $project->title }}</h1>

                @if ($project->short_description)
                    <p class="mt-4 text-lg leading-relaxed" style="color:var(--muted)">{{ $project->short_description }}</p>
                @endif

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    @if ($project->demo_url)
                        <a href="{{ $project->demo_url }}" target="_blank" rel="noopener" class="btn btn-primary btn-sm">🔗 Lihat Demo</a>
                    @endif
                    @if ($project->repo_url)
                        <a href="{{ $project->repo_url }}" target="_blank" rel="noopener" class="btn btn-ghost btn-sm">💻 Kode Sumber</a>
                    @endif
                    @if ($project->completed_at)
                        <span class="text-sm" style="color:var(--muted)">📅 {{ indonesian_month($project->completed_at) }}</span>
                    @endif
                </div>
            </div>

            {{-- Gambar utama --}}
            @if ($cover)
                <div class="mt-10 card overflow-hidden p-0" data-reveal="zoom">
                    <img src="{{ $cover }}" alt="{{ $project->title }}" class="w-full max-h-[560px] object-cover">
                </div>
            @endif

            <div class="grid lg:grid-cols-[1.6fr_.9fr] gap-10 mt-12">
                {{-- Deskripsi --}}
                <div data-reveal="left">
                    <h2 class="text-xl font-semibold mb-4">Tentang Proyek</h2>

                    @if ($project->description)
                        <div class="space-y-4 leading-relaxed whitespace-pre-line" style="color:var(--muted)">
                            {{ $project->description }}
                        </div>
                    @else
                        <p style="color:var(--muted)">Belum ada deskripsi untuk proyek ini.</p>
                    @endif

                    @if ($gallery->isNotEmpty())
                        <h3 class="text-lg font-semibold mt-10 mb-4">Galeri</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach ($gallery as $image)
                                <button type="button" class="gallery-thumb cursor-zoom-in" data-lightbox="{{ $image }}">
                                    <img src="{{ $image }}" alt="Galeri {{ $project->title }}" loading="lazy">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <aside class="space-y-5" data-reveal="right">
                    @if (!empty($techs))
                        <div class="card p-5">
                            <h3 class="font-semibold text-sm mb-3">Teknologi</h3>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($techs as $tech)
                                    <span class="chip">{{ $tech }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="card p-5">
                        <h3 class="font-semibold text-sm mb-3">Informasi</h3>
                        <dl class="space-y-2.5 text-sm">
                            @if ($project->category)
                                <div class="flex justify-between gap-3">
                                    <dt style="color:var(--muted)">Kategori</dt>
                                    <dd class="text-right">{{ $project->category }}</dd>
                                </div>
                            @endif
                            @if ($project->completed_at)
                                <div class="flex justify-between gap-3">
                                    <dt style="color:var(--muted)">Selesai</dt>
                                    <dd class="text-right">{{ indonesian_month($project->completed_at) }}</dd>
                                </div>
                            @endif
                            <div class="flex justify-between gap-3">
                                <dt style="color:var(--muted)">Status</dt>
                                <dd class="text-right">
                                    <span class="badge {{ $project->is_active ? 'badge-on' : 'badge-off' }}">
                                        {{ $project->is_active ? 'Publik' : 'Draf' }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="card p-5">
                        <h3 class="font-semibold text-sm mb-2">Tertarik kerja sama?</h3>
                        <p class="text-sm mb-4" style="color:var(--muted)">Saya terbuka untuk proyek serupa.</p>
                        <a href="{{ route('portfolio.home') }}#contact" class="btn btn-primary btn-sm w-full">Hubungi Saya</a>
                    </div>
                </aside>
            </div>

            {{-- Proyek terkait --}}
            @if ($related->isNotEmpty())
                <div class="mt-20">
                    @include('portfolio.partials.section-heading', [
                        'label' => 'Lainnya',
                        'title' => 'Proyek Terkait',
                        'align' => 'left',
                    ])

                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach ($related as $i => $item)
                            @include('portfolio.partials.project-card', ['project' => $item, 'delay' => $i * 90])
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </article>
@endsection
