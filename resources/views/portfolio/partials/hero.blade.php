@php
    /** Baris teks untuk efek mengetik di hero. */
    $phrases = array_values(array_filter([
        $profile->headline,
        $profile->role,
        ...array_map(fn ($s) => $s->name, $skills->take(3)->all()),
    ]));
@endphp

<section id="hero" class="relative min-h-screen flex items-center pt-28 pb-16">
    <div class="container-x">
        <div class="grid lg:grid-cols-[1.15fr_.85fr] gap-14 items-center">

            {{-- ===== Kiri: teks ===== --}}
            <div>
                @if ($profile->is_available)
                    <div data-reveal="zoom"
                         class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-medium mb-6
                                bg-[color-mix(in_srgb,#22c55e_14%,transparent)] border border-[color-mix(in_srgb,#22c55e_35%,transparent)]
                                text-[color-mix(in_srgb,#22c55e_75%,var(--text))]">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#22c55e] opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-[#22c55e]"></span>
                        </span>
                        {{ $profile->availability_text ?: 'Tersedia untuk pekerjaan baru' }}
                    </div>
                @endif

                <p data-reveal class="text-sm tracking-[0.22em] uppercase mb-3" style="--reveal-delay:80ms;color:var(--muted)">
                    {{ $profile->hero_greeting ?: 'Halo, saya' }}
                </p>

                <h1 data-reveal style="--reveal-delay:160ms"
                    class="text-5xl sm:text-6xl lg:text-7xl font-bold leading-[1.03] tracking-tight">
                    <span class="text-gradient">{{ $profile->name }}</span>
                </h1>

                <div data-reveal style="--reveal-delay:260ms" class="mt-5 text-xl sm:text-2xl font-medium min-h-[2.4em]">
                    <span id="typing-target"
                          data-phrases='@json($phrases)'
                          data-speed="{{ setting_on('typing_effect', true) ? 58 : 0 }}"></span><span class="typing-cursor"></span>
                </div>

                <p data-reveal style="--reveal-delay:340ms" class="mt-6 text-base sm:text-lg leading-relaxed max-w-xl" style="color:var(--muted)">
                    {{ $profile->short_bio }}
                </p>

                <div data-reveal style="--reveal-delay:420ms" class="mt-9 flex flex-wrap items-center gap-3">
                    <a href="{{ $profile->hero_cta_url ?: '#projects' }}" class="btn btn-primary">
                        {{ $profile->hero_cta_label ?: 'Lihat Proyek' }}
                        <span>→</span>
                    </a>

                    @if ($cv = media_url($profile->cv_file))
                        <a href="{{ $cv }}" target="_blank" rel="noopener" class="btn btn-ghost">
                            <span>📄</span> Unduh CV
                        </a>
                    @endif

                    @if (setting_on('section_contact', true))
                        <a href="#contact" class="btn btn-ghost">Hubungi Saya</a>
                    @endif
                </div>

                {{-- Info cepat --}}
                <div data-reveal style="--reveal-delay:500ms" class="mt-9 flex flex-wrap gap-x-8 gap-y-3 text-sm" style="color:var(--muted)">
                    @if ($profile->location)
                        <span class="flex items-center gap-2">📍 {{ $profile->location }}</span>
                    @endif
                    @if ($profile->email)
                        <button type="button" data-copy="{{ $profile->email }}"
                                class="flex items-center gap-2 hover:text-[var(--text)] transition-colors cursor-pointer"
                                title="Klik untuk menyalin">
                            ✉️ {{ $profile->email }}
                        </button>
                    @endif
                    @if ($profile->phone)
                        <span class="flex items-center gap-2">📞 {{ $profile->phone }}</span>
                    @endif
                </div>

                {{-- Sosial media --}}
                @if (!empty($profile->social_links))
                    <div data-reveal style="--reveal-delay:560ms" class="mt-8 flex flex-wrap gap-2">
                        @foreach ($profile->social_links as $i => $link)
                            <a href="{{ $link['url'] ?? '#' }}" target="_blank" rel="noopener"
                               class="chip" style="--reveal-delay:{{ 600 + $i * 60 }}ms">
                                {{ $link['label'] ?? $link['platform'] ?? 'Link' }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ===== Kanan: foto ===== --}}
            <div data-reveal="zoom" style="--reveal-delay:300ms" class="flex justify-center lg:justify-end">
                <div class="relative">
                    <div class="avatar-ring w-64 h-64 sm:w-80 sm:h-80 rounded-full p-[3px]">
                        @if ($avatar = media_url($profile->avatar))
                            <img src="{{ $avatar }}" alt="{{ $profile->name }}"
                                 class="w-full h-full rounded-full object-cover border-4 border-[var(--bg)]">
                        @else
                            <div class="w-full h-full rounded-full grid place-items-center border-4 border-[var(--bg)]
                                        bg-[linear-gradient(140deg,color-mix(in_srgb,var(--accent)_35%,transparent),color-mix(in_srgb,var(--accent-2)_35%,transparent))]">
                                <span class="text-7xl font-bold text-gradient">{{ initials($profile->name) }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Kartu kecil mengapung --}}
                    <div class="floaty absolute -left-6 sm:-left-12 top-10 card card-solid px-4 py-3 shadow-lg">
                        <p class="text-2xl font-bold text-gradient"><span data-count-to="{{ $skills->count() }}">0</span></p>
                        <p class="text-[11px]" style="color:var(--muted)">Keahlian</p>
                    </div>

                    <div class="floaty-slow absolute -right-4 sm:-right-10 bottom-14 card card-solid px-4 py-3 shadow-lg">
                        <p class="text-2xl font-bold text-gradient"><span data-count-to="{{ $projects->count() }}">0</span></p>
                        <p class="text-[11px]" style="color:var(--muted)">Proyek</p>
                    </div>

                    <div class="floaty absolute -bottom-2 left-1/2 -translate-x-1/2 card card-solid px-4 py-2.5 shadow-lg" style="animation-delay:1.2s">
                        <p class="text-xs font-medium">{{ $profile->role ?: 'Web Developer' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
