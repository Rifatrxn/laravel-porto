<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — Panel Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div class="bg-layer bg-aurora">
        <span class="blob blob-1"></span>
        <span class="blob blob-2"></span>
    </div>

    <div class="min-h-screen grid place-items-center px-5">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <div class="w-14 h-14 mx-auto rounded-2xl grid place-items-center text-2xl text-white
                            bg-[linear-gradient(120deg,var(--accent),var(--accent-2))] floaty">
                    🔐
                </div>
                <h1 class="text-2xl font-bold mt-5">Panel Admin</h1>
                <p class="text-sm mt-1.5" style="color:var(--muted)">Masuk untuk mengelola isi portofolio Anda.</p>
            </div>

            <div class="card p-7" data-reveal="zoom">
                @if ($errors->any())
                    <div class="mb-5 p-3.5 rounded-xl text-sm
                                bg-[color-mix(in_srgb,#ef4444_12%,transparent)]
                                border border-[color-mix(in_srgb,#ef4444_32%,transparent)]">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login.attempt') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="field-label" for="email">Email</label>
                        <input id="email" name="email" type="email" class="field" required
                               value="{{ old('email') }}" placeholder="admin@portfolio.test">
                    </div>

                    <div>
                        <label class="field-label" for="password">Password</label>
                        <input id="password" name="password" type="password" class="field" required
                               placeholder="••••••••">
                    </div>

                    <label class="flex items-center gap-2.5 text-sm cursor-pointer select-none" style="color:var(--muted)">
                        <input type="checkbox" name="remember" value="1" class="rounded">
                        Ingat saya
                    </label>

                    <button type="submit" class="btn btn-primary w-full">Masuk</button>
                </form>

                <p class="text-center text-xs mt-6" style="color:var(--muted)">
                    <a href="{{ route('portfolio.home') }}" class="hover:text-[var(--accent)] transition-colors">← Kembali ke website</a>
                </p>
            </div>

            <div class="mt-6 p-4 rounded-xl text-xs text-center"
                 style="background:var(--surface);border:1px dashed var(--border-strong);color:var(--muted)">
                Akun contoh (hasil seeder):<br>
                <span class="font-medium" style="color:var(--text)">admin@portfolio.test</span> /
                <span class="font-medium" style="color:var(--text)">password</span>
            </div>
        </div>
    </div>
</body>
</html>
