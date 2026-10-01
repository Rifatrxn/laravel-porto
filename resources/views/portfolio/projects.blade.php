@extends('layouts.portfolio')

@section('title', 'Semua Proyek')

@section('content')
    <section class="pt-36 pb-16">
        <div class="container-x">
            <a href="{{ route('portfolio.home') }}" class="text-sm inline-flex items-center gap-2 mb-6 hover:text-[var(--accent)] transition-colors" style="color:var(--muted)">
                ← Kembali ke Beranda
            </a>

            @include('portfolio.partials.section-heading', [
                'label' => 'Portofolio',
                'title' => 'Semua Proyek',
                'subtitle' => 'Kumpulan pekerjaan yang pernah saya kerjakan.',
                'align' => 'left',
            ])

            @if ($categories->count() > 1)
                <div class="flex flex-wrap gap-2 mb-10" data-reveal>
                    <button type="button" class="chip is-active" data-filter="all"
                            style="background:color-mix(in srgb,var(--accent) 24%,transparent)">Semua</button>
                    @foreach ($categories as $category)
                        <button type="button" class="chip" data-filter="{{ Str::slug($category) }}">{{ $category }}</button>
                    @endforeach
                </div>
            @endif

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($projects as $i => $project)
                    @include('portfolio.partials.project-card', ['project' => $project, 'delay' => min($i * 80, 400)])
                @endforeach
            </div>

            <p id="filter-empty" class="text-center py-10" style="display:none;color:var(--muted)">
                Tidak ada proyek pada kategori ini.
            </p>

            <style>
                .chip.is-active { background: color-mix(in srgb, var(--accent) 24%, transparent); }
                [data-filter-item] { transition: opacity .32s ease, transform .32s ease; }
            </style>
        </div>
    </section>
@endsection
