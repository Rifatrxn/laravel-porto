@php
    $grouped = $skills->groupBy(fn ($s) => $s->category ?: 'Lainnya');
@endphp

<section id="skills" class="py-20 sm:py-24">
    <div class="container-x">
        @include('portfolio.partials.section-heading', [
            'label' => 'Keahlian',
            'title' => $settings['section_skills_title'] ?? 'Keahlian',
            'subtitle' => 'Teknologi dan alat yang saya pakai sehari-hari.',
        ])

        @if ($grouped->count() > 1)
            <div class="flex flex-wrap justify-center gap-2 mb-10" data-reveal="zoom">
                <button type="button" class="chip is-active" data-filter="all"
                        style="background:color-mix(in srgb,var(--accent) 24%,transparent)">Semua</button>
                @foreach ($grouped->keys() as $category)
                    <button type="button" class="chip" data-filter="{{ Str::slug($category) }}">{{ $category }}</button>
                @endforeach
            </div>
        @endif

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($skills as $i => $skill)
                <div class="card p-5" data-reveal style="--reveal-delay:{{ min($i * 60, 420) }}ms"
                     data-filter-item data-filter-value="{{ Str::slug($skill->category ?: 'Lainnya') }}">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="w-10 h-10 rounded-xl grid place-items-center text-lg shrink-0
                                         bg-[color-mix(in_srgb,var(--accent)_14%,transparent)]
                                         border border-[color-mix(in_srgb,var(--accent)_26%,transparent)]">
                                {{ $skill->icon ?: '⚙️' }}
                            </span>
                            <div class="min-w-0">
                                <p class="font-medium truncate">{{ $skill->name }}</p>
                                @if ($skill->category)
                                    <p class="text-xs" style="color:var(--muted)">{{ $skill->category }}</p>
                                @endif
                            </div>
                        </div>
                        <span class="text-sm font-semibold" style="color:var(--accent)">{{ $skill->level }}%</span>
                    </div>

                    <div class="skill-bar mt-4">
                        <span data-skill-level="{{ $skill->level }}"></span>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($grouped->count() > 1)
            <style>
                [data-filter-item] { transition: opacity .32s ease, transform .32s ease; }
                .chip.is-active { background: color-mix(in srgb, var(--accent) 24%, transparent); }
            </style>
        @endif
    </div>
</section>
