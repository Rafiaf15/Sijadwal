<aside class="w-72 bg-white border-r border-zinc-200 flex flex-col">
    <div class="p-6 border-b border-zinc-200">
        <h1 class="text-xl font-semibold text-zinc-900">Sistem Penjadwalan</h1>
        <p class="text-sm text-zinc-500 mt-1">Optimisasi dengan Algoritma Genetika</p>
    </div>

    <nav class="flex-1 p-4 space-y-1 text-sm">
        @php
            $menus = [
                ['label' => 'Dashboard', 'route' => 'dashboard'],
                ['label' => 'Mata Kuliah', 'route' => 'mata-kuliah.index'],
                ['label' => 'Ruang', 'route' => 'ruang.index'],
                ['label' => 'Dosen', 'route' => 'dosen.index'],
                ['label' => 'Waktu', 'route' => 'waktu.index'],
                ['label' => 'Generate Jadwal', 'route' => 'generate-jadwal.index'],
                ['label' => 'Kalender', 'route' => 'kalender.index'],
            ];
        @endphp

        @foreach ($menus as $menu)
            @php
                $isActive = request()->routeIs($menu['route']);
            @endphp
            <a
                href="{{ route($menu['route']) }}"
                class="flex items-center gap-2 px-3 py-2.5 rounded-lg transition-colors {{ $isActive ? 'bg-red-50 text-red-700 font-medium' : 'text-zinc-700 hover:bg-zinc-100' }}"
            >
                <span>{{ $menu['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="p-4 border-t border-zinc-200 space-y-3">
        <div>
            <p class="text-xs text-zinc-500">Role</p>
            <p class="text-sm font-medium text-zinc-900 mt-1">Staff Akademik</p>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button
                type="submit"
                class="w-full rounded-lg bg-red-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-red-700"
            >
                Logout
            </button>
        </form>
    </div>
</aside>
