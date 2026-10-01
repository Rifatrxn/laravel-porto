@php $count = max(1, $testimonials->count()); @endphp

<section id="testimonials" class="py-20 sm:py-24">
    <div class="container-x">
        @include('portfolio.partials.section-heading', [
            'label' => 'Testimoni',
            'title' => $settings['section_testimonials_title'] ?? 'Kata Mereka',
            'subtitle' => 'Apa kata klien dan rekan kerja tentang kerja sama kami.',
        ])
    </div>

    <div class="container-x">
        <div class="relative" data-reveal="zoom">
            <div class="overflow-hidden">
                <div id="testimonial-track" class="flex transition-transform duration-700 ease-[cubic-bezier(.16,1,.3,1)]"
                     style="transform:translateX(0)">
                    @foreach ($testimonials as $t)
                        <div class="w-full shrink-0 px-2" style="flex:0 0 100%">
                            @if ($loop->index % 3 === 0)
                                <div class="grid md:grid-cols-3 gap-4">
                            @endif

                            <div class="card p-6 h-full">
                                <div class="flex gap-0.5 text-sm mb-4">
                                    @for ($star = 1; $star <= 5; $star++)
                                        <span style="opacity:{{ $star <= $t->rating ? 1 : .25 }}">⭐</span>
                                    @endfor
                                </div>

                                <p class="leading-relaxed text-sm" style="color:var(--muted)">"{{ $t->message }}"</p>

                                <div class="mt-5 pt-5 border-t border-[var(--border)] flex items-center gap-3">
                                    @if ($avatar = media_url($t->avatar))
                                        <img src="{{ $avatar }}" alt="{{ $t->name }}" class="w-10 h-10 rounded-full object-cover">
                                    @else
                                        <span class="w-10 h-10 rounded-full grid place-items-center text-xs font-bold text-white
                                                     bg-[linear-gradient(120deg,var(--accent),var(--accent-2))]">
                                            {{ initials($t->name) }}
                                        </span>
                                    @endif
                                    <div>
                                        <p class="text-sm font-medium">{{ $t->name }}</p>
                                        <p class="text-xs" style="color:var(--muted)">
                                            {{ trim(($t->role ?? '').($t->company ? ', '.$t->company : '')) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @if ($loop->index % 3 === 2 || $loop->last)
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Kontrol --}}
            <div class="flex items-center justify-center gap-4 mt-8">
                <button type="button" id="testimonial-prev" aria-label="Sebelumnya"
                        class="w-10 h-10 rounded-full grid place-items-center card hover:scale-110 transition-transform">←</button>

                <div id="testimonial-dots" class="flex items-center gap-2">
                    @for ($i = 0; $i < ceil($count / 3); $i++)
                        <button type="button" data-slide="{{ $i }}" aria-label="Slide {{ $i + 1 }}"
                                class="h-2 rounded-full transition-all duration-400"
                                style="width:{{ $i === 0 ? '26px' : '8px' }};background:{{ $i === 0 ? 'var(--accent)' : 'var(--border-strong)' }}"></button>
                    @endfor
                </div>

                <button type="button" id="testimonial-next" aria-label="Berikutnya"
                        class="w-10 h-10 rounded-full grid place-items-center card hover:scale-110 transition-transform">→</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const track = document.getElementById('testimonial-track');
            if (!track) return;

            const slides = Array.from(track.children);
            const dots = Array.from(document.querySelectorAll('[data-slide]'));
            let index = 0;

            const render = () => {
                track.style.transform = `translateX(-${index * 100}%)`;
                dots.forEach((dot, i) => {
                    dot.style.width = i === index ? '26px' : '8px';
                    dot.style.background = i === index ? 'var(--accent)' : 'var(--border-strong)';
                });
            };

            document.getElementById('testimonial-next')?.addEventListener('click', () => {
                index = (index + 1) % slides.length;
                render();
            });
            document.getElementById('testimonial-prev')?.addEventListener('click', () => {
                index = (index - 1 + slides.length) % slides.length;
                render();
            });
            dots.forEach((dot, i) => dot.addEventListener('click', () => { index = i; render(); }));

            // Geser otomatis, berhenti saat kursor di atasnya
            let timer = setInterval(() => {
                index = (index + 1) % slides.length;
                render();
            }, 7000);

            const wrap = track.closest('.relative');
            wrap?.addEventListener('mouseenter', () => clearInterval(timer));
            wrap?.addEventListener('mouseleave', () => {
                timer = setInterval(() => {
                    index = (index + 1) % slides.length;
                    render();
                }, 7000);
            });
        });
    </script>
    @endpush
</section>
