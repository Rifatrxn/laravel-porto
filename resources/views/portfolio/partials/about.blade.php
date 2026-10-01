@php
    $aboutParagraphs = preg_split('/\r\n\r\n|\n\n|\r\r/', trim((string) $profile->about)) ?: [];
@endphp

<section id="about" class="py-20 sm:py-24">
    <div class="container-x">
        @include('portfolio.partials.section-heading', [
            'label' => 'Profil',
            'title' => $settings['section_about_title'] ?? 'Tentang Saya',
        ])

        <div class="grid lg:grid-cols-[.95fr_1.05fr] gap-10 items-start">
            {{-- Kartu identitas --}}
            <div class="card p-7" data-reveal="left">
                <div class="flex items-center gap-4">
                    @if ($avatar = media_url($profile->avatar))
                        <img src="{{ $avatar }}" alt="{{ $profile->name }}"
                             class="w-16 h-16 rounded-2xl object-cover">
                    @else
                        <div class="w-16 h-16 rounded-2xl grid place-items-center text-xl font-bold text-white
                                    bg-[linear-gradient(120deg,var(--accent),var(--accent-2))]">
                            {{ initials($profile->name) }}
                        </div>
                    @endif

                    <div>
                        <p class="font-semibold text-lg">{{ $profile->name }}</p>
                        <p class="text-sm" style="color:var(--muted)">{{ $profile->headline }}</p>
                    </div>
                </div>

                <div class="mt-6 space-y-3 text-sm">
                    @if ($profile->role)
                        <div class="flex gap-3"><span>🎯</span><span style="color:var(--muted)">{{ $profile->role }}</span></div>
                    @endif
                    @if ($profile->location)
                        <div class="flex gap-3"><span>📍</span><span style="color:var(--muted)">{{ $profile->location }}</span></div>
                    @endif
                    @if ($profile->email)
                        <div class="flex gap-3"><span>✉️</span>
                            <a href="mailto:{{ $profile->email }}" class="hover:text-[var(--accent)] transition-colors" style="color:var(--muted)">{{ $profile->email }}</a>
                        </div>
                    @endif
                    @if ($profile->phone)
                        <div class="flex gap-3"><span>📞</span><span style="color:var(--muted)">{{ $profile->phone }}</span></div>
                    @endif
                </div>

                <div class="mt-6 pt-6 border-t border-[var(--border)] flex flex-wrap gap-2">
                    @if ($cv = media_url($profile->cv_file))
                        <a href="{{ $cv }}" target="_blank" rel="noopener" class="btn btn-primary btn-sm">📄 Unduh CV</a>
                    @endif
                    @if (setting_on('section_contact', true))
                        <a href="#contact" class="btn btn-ghost btn-sm">Hubungi</a>
                    @endif
                </div>
            </div>

            {{-- Narasi --}}
            <div data-reveal="right">
                <div class="space-y-5 leading-relaxed" style="color:var(--muted)">
                    @foreach ($aboutParagraphs as $paragraph)
                        @if (trim($paragraph) !== '')
                            <p>{{ trim($paragraph) }}</p>
                        @endif
                    @endforeach
                </div>

                @if ($educations->isNotEmpty())
                    <div class="mt-8 grid sm:grid-cols-2 gap-4">
                        @foreach ($educations->take(2) as $edu)
                            <div class="card p-4" data-reveal="zoom">
                                <p class="text-xs font-semibold tracking-wider uppercase" style="color:var(--accent)">
                                    {{ $edu->degree }}{{ $edu->field_of_study ? ' — '.$edu->field_of_study : '' }}
                                </p>
                                <p class="font-medium mt-1.5 text-sm">{{ $edu->institution }}</p>
                                <p class="text-xs mt-1" style="color:var(--muted)">
                                    {{ period_label($edu->start_year, $edu->end_year, $edu->is_current) }}
                                    @if ($edu->grade) · {{ $edu->grade }} @endif
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
