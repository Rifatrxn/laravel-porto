@php
    use Illuminate\Support\Facades\Storage;

    /** @var array $settings */
    $s = fn (string $key, $default = null) => $settings[$key] ?? $default;

    $themeMode = $s('theme_mode', 'dark');
    $initialTheme = $themeMode === 'toggle' ? 'dark' : $themeMode;
    $animSpeed = match ($s('animation_speed', 'normal')) {
        'slow' => '0.65',
        'fast' => '1.5',
        default => '1',
    };

    $navSections = collect([
        ['section_about', '#about', $s('section_about_title', 'Tentang Saya')],
        ['section_services', '#services', 'Layanan'],
        ['section_skills', '#skills', 'Keahlian'],
        ['section_projects', '#projects', 'Proyek'],
        ['section_experience', '#experience', 'Pengalaman'],
        ['section_testimonials', '#testimonials', 'Testimoni'],
        ['section_contact', '#contact', 'Kontak'],
    ])->filter(fn ($item) => ($s($item[0], '1') == '1'))->values();

    $siteTitle = $s('site_title', 'Portofolio Saya');
    $pageTitle = trim($__env->yieldContent('title'));
@endphp
<!DOCTYPE html>
<html lang="id"
      data-theme="{{ $initialTheme }}"
      data-anim="{{ $s('animations_enabled', '1') == '1' ? 'on' : 'off' }}"
      style="--accent: {{ $s('accent_color', '#6366f1') }};
             --accent-2: {{ $s('accent_color_2', '#06b6d4') }};
             --radius: {{ $s('border_radius', '1.25rem') }};
             --anim-speed: {{ $animSpeed }};">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $pageTitle !== '' ? $pageTitle.' — '.$siteTitle : $siteTitle }}</title>
    <meta name="description" content="{{ $s('meta_description', 'Portofolio pribadi.') }}">
    <meta name="keywords" content="{{ $s('meta_keywords', 'portofolio') }}">
    <meta name="author" content="{{ $profile->name ?? '' }}">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $pageTitle !== '' ? $pageTitle : $siteTitle }}">
    <meta property="og:description" content="{{ $s('meta_description', '') }}">
    <meta property="og:image" content="{{ media_url($s('og_image')) ?? media_url($profile->avatar ?? null) }}">
    <meta name="twitter:card" content="summary_large_image">

    @if ($favicon = media_url($s('site_favicon')))
        <link rel="icon" href="{{ $favicon }}">
    @else
        <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>💼</text></svg>">
    @endif

    @if ($themeMode === 'toggle')
        {{-- Terapkan tema tersimpan sebelum render supaya tidak berkedip --}}
        <script>
            try {
                const saved = localStorage.getItem('portfolio-theme');
                if (saved) document.documentElement.dataset.theme = saved;
            } catch (e) {}
        </script>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="antialiased">
    <div id="scroll-progress"></div>

    {{-- Latar belakang bergerak --}}
    @php $bgStyle = $s('background_style', 'aurora'); @endphp
    <div class="bg-layer bg-{{ $bgStyle }}">
        @if ($bgStyle === 'aurora')
            <span class="blob blob-1"></span>
            <span class="blob blob-2"></span>
            <span class="blob blob-3"></span>
        @endif
    </div>

    @if ($s('cursor_glow', '1') == '1')
        <div id="cursor-glow"></div>
    @endif

    {{-- ================= NAVBAR ================= --}}
    <header id="main-nav" class="fixed top-0 inset-x-0 z-50 transition-all duration-500">
        <div class="container-x">
            <nav class="flex items-center justify-between h-[70px]">
                <a href="{{ route('portfolio.home') }}" class="flex items-center gap-2.5 group">
                    @if ($avatar = media_url($profile->avatar ?? null))
                        <img src="{{ $avatar }}" alt="{{ $profile->name }}"
                             class="w-9 h-9 rounded-full object-cover ring-2 ring-[color-mix(in_srgb,var(--accent)_60%,transparent)] transition-transform duration-500 group-hover:scale-110">
                    @else
                        <span class="w-9 h-9 rounded-full grid place-items-center text-xs font-bold text-white
                                     bg-[linear-gradient(120deg,var(--accent),var(--accent-2))] transition-transform duration-500 group-hover:scale-110">
                            {{ initials($profile->name ?? 'P') }}
                        </span>
                    @endif
                    <span class="font-semibold tracking-tight">{{ $profile->name ?? $siteTitle }}</span>
                </a>

                <div class="hidden lg:flex items-center gap-6">
                    @foreach ($navSections as [$key, $href, $label])
                        <a href="{{ $href }}" class="nav-link" data-spy-link>{{ $label }}</a>
                    @endforeach
                </div>

                <div class="flex items-center gap-2">
                    @if ($themeMode === 'toggle')
                        <button id="theme-toggle" type="button" aria-label="Ganti tema"
                                class="w-10 h-10 rounded-full grid place-items-center card hover:scale-105 transition-transform">
                            <span data-theme-icon>{{ $initialTheme === 'dark' ? '🌙' : '☀️' }}</span>
                        </button>
                    @endif

                    <a href="{{ route('portfolio.projects') }}"
                       class="btn btn-ghost btn-sm hidden sm:inline-flex">Semua Proyek</a>

                    <button id="menu-toggle" type="button" aria-expanded="false" aria-label="Menu"
                            class="lg:hidden w-10 h-10 rounded-full grid place-items-center card">
                        <span class="text-lg">☰</span>
                    </button>
                </div>
            </nav>

            <div id="mobile-menu" class="lg:hidden overflow-hidden transition-all duration-500" style="max-height:0">
                <div class="card card-solid p-3 mb-4 flex flex-col gap-1">
                    @foreach ($navSections as [$key, $href, $label])
                        <a href="{{ $href }}" class="px-3 py-2.5 rounded-xl text-sm hover:bg-[var(--surface)] transition-colors">{{ $label }}</a>
                    @endforeach
                    <a href="{{ route('portfolio.projects') }}" class="px-3 py-2.5 rounded-xl text-sm hover:bg-[var(--surface)] transition-colors">Semua Proyek</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    {{-- ================= FOOTER ================= --}}
    <footer class="mt-24 border-t border-[var(--border)]">
        <div class="container-x py-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="text-center md:text-left">
                    <p class="font-semibold">{{ $profile->name ?? $siteTitle }}</p>
                    <p class="text-sm mt-1" style="color:var(--muted)">{{ $s('footer_text', '') }}</p>
                </div>

                @if (!empty($profile->social_links))
                    <div class="flex items-center gap-2 flex-wrap justify-center">
                        @foreach ($profile->social_links as $link)
                            <a href="{{ $link['url'] ?? '#' }}" target="_blank" rel="noopener"
                               class="chip hover:-translate-y-0.5">{{ $link['label'] ?? $link['platform'] ?? 'Link' }}</a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="mt-8 pt-6 border-t border-[var(--border)] flex flex-col sm:flex-row items-center justify-between gap-3 text-xs" style="color:var(--muted)">
                <span>© {{ date('Y') }} {{ $profile->name ?? $siteTitle }}. Semua hak dilindungi.</span>
                {{--
                    Catatan keamanan: TIDAK ada tautan ke halaman masuk / panel admin
                    di sini. Kalau alamat login ditautkan dari area publik, "jalur
                    tikus" jadi tidak ada gunanya — siapa pun bisa menemukannya.
                    Simpan alamatnya di catatan pribadi Anda.
                --}}
            </div>
        </div>
    </footer>

    <button id="back-to-top" type="button" aria-label="Kembali ke atas"
            class="fixed bottom-6 left-6 z-50 w-11 h-11 rounded-full grid place-items-center card card-solid
                   opacity-0 pointer-events-none transition-all duration-400 [&.is-visible]:opacity-100 [&.is-visible]:pointer-events-auto">
        <span>↑</span>
    </button>

    @stack('scripts')

    <style>
        #back-to-top.is-visible { opacity: 1; pointer-events: auto; }
        #main-nav.is-scrolled > .container-x > nav {
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
        }
        #main-nav.is-scrolled {
            background: color-mix(in srgb, var(--bg) 78%, transparent);
            border-bottom: 1px solid var(--border);
            box-shadow: 0 12px 40px -30px rgba(0, 0, 0, .9);
        }
    </style>
</body>
</html>
