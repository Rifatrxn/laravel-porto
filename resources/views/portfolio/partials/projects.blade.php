@php
    $categories = $categories ?? collect();
@endphp

<section id="projects" class="py-20 sm:py-24">
    <div class="container-x">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-6">
            @include('portfolio.partials.section-heading', [
                'label' => 'Portofolio',
                'title' => $settings['section_projects_title'] ?? 'Proyek Pilihan',
                'align' => 'left',
            ])

            <a href="{{ route('portfolio.projects') }}" class="btn btn-ghost btn-sm mb-12 shrink-0" data-reveal="right">
                Lihat Semua Proyek →
            </a>
        </div>

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
                @include('portfolio.partials.project-card', ['project' => $project, 'delay' => min($i * 90, 400)])
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
