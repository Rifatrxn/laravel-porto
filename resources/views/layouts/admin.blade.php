@php
    use App\Models\Setting;

    $navGroups = [
        'Konten' => [
            ['admin.dashboard', '📊', 'Dashboard'],
            ['admin.profile.edit', '👤', 'Profil Saya'],
        ],
        'Data' => [
            ['admin.projects.index', '📁', 'Proyek'],
            ['admin.skills.index', '⚡', 'Keahlian'],
            ['admin.experiences.index', '💼', 'Pengalaman'],
            ['admin.educations.index', '🎓', 'Pendidikan'],
            ['admin.services.index', '🧩', 'Layanan'],
            ['admin.testimonials.index', '⭐', 'Testimoni'],
        ],
        'Lainnya' => [
            ['admin.messages.index', '✉️', 'Pesan Masuk'],
            ['admin.settings.edit', '⚙️', 'Pengaturan Tampilan'],
        ],
    ];

    // Menu khusus admin (editor tidak melihatnya).
    if (auth()->user()?->isAdmin()) {
        $navGroups['Keamanan'] = [
            ['admin.users.index', '🔐', 'Kelola Akun'],
        ];
    }

    $unread = \App\Models\Message::where('is_read', false)->count();
@endphp
<!DOCTYPE html>
<html lang="id" data-theme="dark"
      style="--accent: {{ Setting::get('accent_color', '#6366f1') }};
             --accent-2: {{ Setting::get('accent_color_2', '#06b6d4') }};
             --radius: 0.9rem;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel Admin') — {{ Setting::get('site_title', 'Portofolio') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div class="admin-shell lg:flex">

        {{-- ================= SIDEBAR ================= --}}
        <aside id="admin-side" class="admin-side lg:w-[266px] lg:shrink-0 lg:min-h-screen
                                      fixed lg:sticky lg:top-0 inset-y-0 left-0 z-40 w-[266px]
                                      -translate-x-full lg:translate-x-0 transition-transform duration-400">
            <div class="p-5 flex items-center justify-between">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl grid place-items-center text-white text-sm font-bold
                                 bg-[linear-gradient(120deg,var(--accent),var(--accent-2))]">P</span>
                    <span class="font-semibold text-sm">Panel Admin</span>
                </a>
                <button type="button" id="admin-side-close" class="lg:hidden text-xl" aria-label="Tutup">×</button>
            </div>

            <nav class="px-3 pb-6 space-y-5 overflow-y-auto" style="max-height:calc(100vh - 80px)">
                @foreach ($navGroups as $group => $links)
                    <div>
                        <p class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-[0.16em]" style="color:var(--muted)">
                            {{ $group }}
                        </p>
                        <div class="space-y-1">
                            @foreach ($links as [$route, $icon, $label])
                                @php
                                    $base = str_replace('.index', '', $route);
                                    $isActive = request()->routeIs($route) || request()->routeIs($base.'.*');
                                @endphp
                                <a href="{{ route($route) }}" class="admin-link {{ $isActive ? 'is-active' : '' }}">
                                    <span>{{ $icon }}</span>
                                    <span class="flex-1">{{ $label }}</span>
                                    @if ($route === 'admin.messages.index' && $unread > 0)
                                        <span class="badge badge-warn">{{ $unread }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="px-3 pt-2 space-y-1">
                    <a href="{{ route('portfolio.home') }}" target="_blank" class="admin-link">
                        <span>🌐</span><span>Lihat Website</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="admin-link w-full text-left">
                            <span>🚪</span><span>Keluar</span>
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <div id="admin-overlay" class="fixed inset-0 z-30 bg-black/60 backdrop-blur-sm hidden lg:hidden"></div>

        {{-- ================= KONTEN ================= --}}
        <div class="flex-1 min-w-0">
            <header class="admin-topbar">
                <div class="px-4 sm:px-7 py-3.5 flex items-center gap-3">
                    <button type="button" id="admin-side-open" class="lg:hidden w-9 h-9 rounded-lg grid place-items-center card" aria-label="Menu">
                        ☰
                    </button>

                    <div class="min-w-0">
                        <h1 class="font-semibold truncate">@yield('title', 'Dashboard')</h1>
                        <p class="text-xs truncate hidden sm:block" style="color:var(--muted)">@yield('subtitle', 'Kelola isi website portofolio Anda')</p>
                    </div>

                    <div class="ml-auto flex items-center gap-2.5">
                        <span class="hidden sm:flex items-center gap-2 text-xs px-3 py-1.5 rounded-full"
                              style="background:var(--surface);border:1px solid var(--border);color:var(--muted)">
                            <span class="w-6 h-6 rounded-full grid place-items-center text-[10px] font-bold text-white"
                                  style="background:linear-gradient(120deg,var(--accent),var(--accent-2))">
                                {{ initials(auth()->user()->name ?? 'A') }}
                            </span>
                            {{ auth()->user()->email ?? '' }}
                            @if (auth()->user()?->isAdmin())
                                <span class="badge badge-on">Admin</span>
                            @endif
                        </span>
                    </div>
                </div>
            </header>

            <main class="px-4 sm:px-7 py-6 sm:py-8">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="toast" data-toast>
            <span>✅</span><span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="toast" data-toast style="border-color:color-mix(in srgb,#ef4444 40%,transparent)">
            <span>⚠️</span><span>{{ session('error') }}</span>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const side = document.getElementById('admin-side');
            const overlay = document.getElementById('admin-overlay');
            const open = () => { side.classList.remove('-translate-x-full'); overlay.classList.remove('hidden'); };
            const close = () => { side.classList.add('-translate-x-full'); overlay.classList.add('hidden'); };
            document.getElementById('admin-side-open')?.addEventListener('click', open);
            document.getElementById('admin-side-close')?.addEventListener('click', close);
            overlay?.addEventListener('click', close);
        });
    </script>
</body>
</html>
