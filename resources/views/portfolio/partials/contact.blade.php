@php
    $bgPattern = ['aurora', 'grid', 'dots', 'plain'];
@endphp

<section id="contact" class="py-20 sm:py-24">
    <div class="container-x">
        @include('portfolio.partials.section-heading', [
            'label' => 'Kontak',
            'title' => $settings['section_contact_title'] ?? 'Mari Bekerja Sama',
            'subtitle' => 'Punya proyek atau pertanyaan? Kirim pesan, saya akan membalas secepatnya.',
        ])

        <div class="grid lg:grid-cols-[.85fr_1.15fr] gap-6">
            {{-- Info kontak --}}
            <div class="card p-7" data-reveal="left">
                <h3 class="font-semibold text-lg">Informasi Kontak</h3>
                <p class="text-sm mt-2" style="color:var(--muted)">
                    Lebih suka lewat jalur lain? Tidak masalah.
                </p>

                <div class="mt-6 space-y-3">
                    @if ($profile->email)
                        <button type="button" data-copy="{{ $profile->email }}"
                                class="w-full flex items-center gap-3 p-3 rounded-xl text-left transition-colors
                                       hover:bg-[var(--surface)] border border-transparent hover:border-[var(--border)]">
                            <span class="w-10 h-10 rounded-xl grid place-items-center shrink-0
                                         bg-[color-mix(in_srgb,var(--accent)_16%,transparent)]">✉️</span>
                            <span class="min-w-0">
                                <span class="block text-xs" style="color:var(--muted)">Email (klik untuk salin)</span>
                                <span class="block text-sm font-medium truncate">{{ $profile->email }}</span>
                            </span>
                        </button>
                    @endif

                    @if ($profile->phone)
                        <button type="button" data-copy="{{ $profile->phone }}"
                                class="w-full flex items-center gap-3 p-3 rounded-xl text-left transition-colors
                                       hover:bg-[var(--surface)] border border-transparent hover:border-[var(--border)]">
                            <span class="w-10 h-10 rounded-xl grid place-items-center shrink-0
                                         bg-[color-mix(in_srgb,var(--accent-2)_16%,transparent)]">📞</span>
                            <span class="min-w-0">
                                <span class="block text-xs" style="color:var(--muted)">Telepon (klik untuk salin)</span>
                                <span class="block text-sm font-medium truncate">{{ $profile->phone }}</span>
                            </span>
                        </button>
                    @endif

                    @if ($profile->location)
                        <div class="flex items-center gap-3 p-3">
                            <span class="w-10 h-10 rounded-xl grid place-items-center shrink-0
                                         bg-[color-mix(in_srgb,var(--accent)_16%,transparent)]">📍</span>
                            <span class="min-w-0">
                                <span class="block text-xs" style="color:var(--muted)">Lokasi</span>
                                <span class="block text-sm font-medium truncate">{{ $profile->location }}</span>
                            </span>
                        </div>
                    @endif
                </div>

                @if (!empty($profile->social_links))
                    <div class="mt-6 pt-6 border-t border-[var(--border)]">
                        <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color:var(--muted)">Media Sosial</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($profile->social_links as $link)
                                <a href="{{ $link['url'] ?? '#' }}" target="_blank" rel="noopener" class="chip">
                                    {{ $link['label'] ?? $link['platform'] ?? 'Link' }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($profile->is_available)
                    <div class="mt-6 p-4 rounded-xl flex items-start gap-3
                                bg-[color-mix(in_srgb,#22c55e_10%,transparent)]
                                border border-[color-mix(in_srgb,#22c55e_28%,transparent)]">
                        <span class="relative flex h-2.5 w-2.5 mt-1.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#22c55e] opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#22c55e]"></span>
                        </span>
                        <p class="text-sm">{{ $profile->availability_text ?: 'Sedang terbuka untuk proyek baru' }}</p>
                    </div>
                @endif
            </div>

            {{-- Formulir --}}
            <div class="card p-7" data-reveal="right">
                <h3 class="font-semibold text-lg">Kirim Pesan</h3>
                <p class="text-sm mt-2" style="color:var(--muted)">Isi formulir di bawah, pesan langsung masuk ke kotak masuk saya.</p>

                @if (session('success'))
                    <div class="mt-5 p-4 rounded-xl text-sm flex items-center gap-3
                                bg-[color-mix(in_srgb,#22c55e_12%,transparent)]
                                border border-[color-mix(in_srgb,#22c55e_32%,transparent)]">
                        <span>✅</span> {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mt-5 p-4 rounded-xl text-sm
                                bg-[color-mix(in_srgb,#ef4444_12%,transparent)]
                                border border-[color-mix(in_srgb,#ef4444_32%,transparent)]">
                        <p class="font-medium mb-1">Periksa kembali isian Anda:</p>
                        <ul class="list-disc list-inside space-y-0.5" style="color:var(--muted)">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="contact-form" action="{{ route('portfolio.contact') }}" method="POST" class="mt-6 space-y-4">
                    @csrf

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="field-label" for="c-name">Nama</label>
                            <input id="c-name" name="name" type="text" class="field" required
                                   value="{{ old('name') }}" placeholder="Nama Anda">
                        </div>
                        <div>
                            <label class="field-label" for="c-email">Email</label>
                            <input id="c-email" name="email" type="email" class="field" required
                                   value="{{ old('email') }}" placeholder="nama@email.com">
                        </div>
                    </div>

                    <div>
                        <label class="field-label" for="c-subject">Subjek</label>
                        <input id="c-subject" name="subject" type="text" class="field"
                               value="{{ old('subject') }}" placeholder="Tentang apa?">
                    </div>

                    <div>
                        <label class="field-label" for="c-body">Pesan</label>
                        <textarea id="c-body" name="body" rows="5" class="field" required
                                  placeholder="Ceritakan kebutuhan atau pertanyaan Anda...">{{ old('body') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-full">
                        Kirim Pesan <span>→</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
