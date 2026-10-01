@php
    $expTitle = $settings['section_experience_title'] ?? 'Pengalaman & Pendidikan';
@endphp

<section id="experience" class="py-20 sm:py-24">
    <div class="container-x">
        @include('portfolio.partials.section-heading', [
            'label' => 'Perjalanan',
            'title' => $expTitle,
        ])

        <div class="grid lg:grid-cols-2 gap-12">
            {{-- Pengalaman --}}
            <div>
                <h3 class="flex items-center gap-2 font-semibold text-lg mb-8" data-reveal="left">
                    <span class="w-9 h-9 rounded-xl grid place-items-center text-base
                                 bg-[color-mix(in_srgb,var(--accent)_16%,transparent)]">💼</span>
                    Pengalaman Kerja
                </h3>

                <div class="timeline pl-9 space-y-8">
                    @forelse ($experiences as $i => $exp)
                        <div class="relative" data-reveal="left" style="--reveal-delay:{{ $i * 90 }}ms">
                            <span class="timeline-dot"></span>

                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-xs font-medium" style="color:var(--accent)">
                                    {{ period_label($exp->start_date, $exp->end_date, $exp->is_current) }}
                                </p>
                                @if ($exp->is_current)
                                    <span class="badge badge-on">Sekarang</span>
                                @endif
                            </div>

                            <h4 class="font-semibold mt-1.5">{{ $exp->position }}</h4>

                            <p class="text-sm" style="color:var(--muted)">
                                {{ $exp->company }}
                                @if ($exp->employment_type) · {{ $exp->employment_type }} @endif
                                @if ($exp->location) · {{ $exp->location }} @endif
                            </p>

                            @if ($exp->description)
                                <p class="mt-2.5 text-sm leading-relaxed" style="color:var(--muted)">{{ $exp->description }}</p>
                            @endif

                            @if (!empty($exp->highlights))
                                <ul class="mt-3 space-y-1.5">
                                    @foreach ($exp->highlights as $point)
                                        <li class="text-sm flex items-start gap-2" style="color:var(--muted)">
                                            <span class="mt-1 shrink-0" style="color:var(--accent)">✓</span>{{ $point }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @empty
                        <p style="color:var(--muted)">Belum ada data pengalaman.</p>
                    @endforelse
                </div>
            </div>

            {{-- Pendidikan --}}
            <div>
                <h3 class="flex items-center gap-2 font-semibold text-lg mb-8" data-reveal="right">
                    <span class="w-9 h-9 rounded-xl grid place-items-center text-base
                                 bg-[color-mix(in_srgb,var(--accent-2)_16%,transparent)]">🎓</span>
                    Pendidikan
                </h3>

                <div class="space-y-4">
                    @forelse ($educations as $i => $edu)
                        <div class="card p-5" data-reveal="right" style="--reveal-delay:{{ $i * 90 }}ms">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h4 class="font-semibold">{{ $edu->institution }}</h4>
                                    <p class="text-sm mt-0.5" style="color:var(--muted)">
                                        {{ trim(($edu->degree ?? '').' '.($edu->field_of_study ?? '')) ?: '—' }}
                                    </p>
                                </div>
                                @if ($edu->is_current)
                                    <span class="badge badge-on shrink-0">Aktif</span>
                                @endif
                            </div>

                            <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs" style="color:var(--muted)">
                                <span>{{ period_label($edu->start_year, $edu->end_year, $edu->is_current) }}</span>
                                @if ($edu->grade) <span>· {{ $edu->grade }}</span> @endif
                            </div>

                            @if ($edu->description)
                                <p class="mt-3 text-sm leading-relaxed" style="color:var(--muted)">{{ $edu->description }}</p>
                            @endif
                        </div>
                    @empty
                        <p style="color:var(--muted)">Belum ada data pendidikan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
