<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Staff Akademik | SiJadwal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --telu-red: #b51234;
            --telu-red-dark: #8f0d2a;
            --telu-cream: #fff6f7;
            --telu-charcoal: #1f1f23;
            --telu-stroke: #f1ccd4;
        }

        .bg-accent {
            background:
                radial-gradient(circle at 15% 20%, rgba(181, 18, 52, 0.22), transparent 40%),
                radial-gradient(circle at 85% 80%, rgba(143, 13, 42, 0.20), transparent 38%),
                linear-gradient(130deg, #fff 0%, var(--telu-cream) 55%, #ffeef1 100%);
        }
    </style>
</head>
<body class="min-h-screen bg-accent text-[var(--telu-charcoal)]">
    <div class="mx-auto flex min-h-screen w-full max-w-6xl items-center px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid w-full overflow-hidden rounded-3xl border border-[var(--telu-stroke)] bg-white shadow-2xl lg:grid-cols-[1.05fr_0.95fr]">
            <section class="relative hidden overflow-hidden bg-[var(--telu-red)] p-10 text-white lg:block">
                <div class="absolute inset-0 opacity-20" style="background: linear-gradient(130deg, rgba(255,255,255,0.18) 0%, rgba(255,255,255,0) 60%);"></div>
                <div class="relative z-10 flex h-full flex-col justify-between">
                    <div>
                        <p class="text-sm font-semibold tracking-[0.24em] uppercase">Telkom University</p>
                        <h1 class="mt-4 text-3xl font-bold leading-tight">Sistem Optimasi Jadwal<br>Prodi Sistem Informasi</h1>
                        <p class="mt-4 max-w-md text-sm text-red-100">
                            Halaman ini hanya diperuntukkan bagi staff akademik untuk proses optimisasi dan monitoring jadwal perkuliahan.
                        </p>
                    </div>
                    <div class="rounded-2xl border border-white/25 bg-white/10 p-4 backdrop-blur-sm">
                        <p class="text-xs uppercase tracking-wider text-red-100">Akses</p>
                        <p class="mt-1 text-sm font-semibold">Staff Akademik Prodi SI</p>
                    </div>
                </div>
            </section>

            <section class="p-6 sm:p-10">
                <div class="mx-auto w-full max-w-md">
                    <div class="mb-8 lg:hidden">
                        <p class="text-xs font-semibold tracking-[0.24em] text-[var(--telu-red)] uppercase">Telkom University</p>
                        <h1 class="mt-2 text-2xl font-bold">Login Staff Akademik</h1>
                        <p class="mt-1 text-sm text-zinc-600">Masuk untuk mengakses dashboard optimasi jadwal Prodi SI.</p>
                    </div>

                    <div class="mb-8 hidden lg:block">
                        <p class="text-xs font-semibold tracking-[0.24em] text-[var(--telu-red)] uppercase">Akses Sistem</p>
                        <h2 class="mt-2 text-3xl font-bold">Selamat Datang</h2>
                        <p class="mt-1 text-sm text-zinc-600">Silakan login terlebih dahulu sebelum masuk ke dashboard.</p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('login.process') }}" method="POST" class="space-y-5">
                        @csrf

                        <div>
                            <label for="username" class="mb-2 block text-sm font-semibold text-[var(--telu-red)]">Username</label>
                            <input
                                type="text"
                                id="username"
                                name="username"
                                value="{{ old('username') }}"
                                placeholder="Masukkan username"
                                required
                                autofocus
                                class="w-full rounded-xl border border-zinc-300 px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-[var(--telu-red)] focus:ring-4 focus:ring-red-100"
                            >
                        </div>

                        <div>
                            <label for="password" class="mb-2 block text-sm font-semibold text-[var(--telu-red)]">Password</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Masukkan password"
                                required
                                class="w-full rounded-xl border border-zinc-300 px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-[var(--telu-red)] focus:ring-4 focus:ring-red-100"
                            >
                        </div>

                        <label class="flex items-center gap-2 text-sm text-zinc-600">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-zinc-300 text-[var(--telu-red)] focus:ring-[var(--telu-red)]">
                            Ingat saya
                        </label>

                        <button
                            type="submit"
                            class="w-full rounded-xl bg-[var(--telu-red)] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[var(--telu-red-dark)] focus:outline-none focus:ring-4 focus:ring-red-200"
                        >
                            Login ke Dashboard
                        </button>
                    </form>

                    <p class="mt-6 text-center text-xs text-zinc-500">
                        SiJadwal Prodi SI - Telkom University
                    </p>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
