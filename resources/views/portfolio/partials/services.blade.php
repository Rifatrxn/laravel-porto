@php
    $iconFallbacks = ['🌐', '🎨', '🔧', '🚀', '📱', '⚙️'];
@endphp

<section id="services" class="py-20 sm:py-24">
    <div class="container-x">
        @include('portfolio.partials.section-heading', [
            'label' => 'Layanan',
            'title' => $settings['section_services_title'] ?? 'Yang Saya Kerjakan',
            'subtitle' => 'Beberapa hal yang bisa saya bantu kerjakan untuk Anda.',
        ])

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($services as $i => $service)
                <div class="card shine p-6 tilt group" data-tilt="8" data-reveal="zoom"
                     style="--reveal-delay:{{ $i * 90 }}ms">
                    <div class="tilt-inner">
                        <div class="w-12 h-12 rounded-2xl grid place-items-center text-2xl mb-5
                                    bg-[color-mix(in_srgb,var(--accent)_16%,transparent)]
                                    border border-[color-mix(in_srgb,var(--accent)_30%,transparent)]
                                    transition-transform duration-500 group-hover:scale-110 group-hover:rotate-6">
                            {{ $service->icon ?: $iconFallbacks[$i % count($iconFallbacks)] }}
                        </div>

                        <h3 class="font-semibold text-lg">{{ $service->title }}</h3>

                        @if ($service->description)
                            <p class="mt-2.5 text-sm leading-relaxed" style="color:var(--muted)">{{ $service->description }}</p>
                        @endif

                        @if (!empty($service->points))
                            <ul class="mt-4 space-y-1.5">
                                @foreach ($service->points as $point)
                                    <li class="text-sm flex items-start gap-2" style="color:var(--muted)">
                                        <span class="mt-1.5 w-1.5 h-1.5 rounded-full shrink-0"
                                              style="background:var(--accent)"></span>
                                        {{ $point }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
