@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="p-8 space-y-8">
        <div class="rounded-2xl border border-red-100 bg-gradient-to-r from-red-50 to-white p-6">
            <h1 class="text-2xl font-semibold text-zinc-900">Dashboard</h1>
            <p class="text-sm text-zinc-600 mt-1">Overview sistem penjadwalan perkuliahan Prodi SI</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6">
            <div class="bg-white border border-red-100 rounded-xl p-6 shadow-sm">
                <p class="text-sm text-zinc-500">Total Mata Kuliah</p>
                <p class="text-3xl font-semibold text-red-700 mt-2">{{ $counts['mata_kuliah'] ?? 0 }}</p>
            </div>
            <div class="bg-white border border-red-100 rounded-xl p-6 shadow-sm">
                <p class="text-sm text-zinc-500">Total Ruang SI</p>
                <p class="text-3xl font-semibold text-red-700 mt-2">{{ $counts['ruang'] ?? 0 }}</p>
            </div>
            <div class="bg-white border border-red-100 rounded-xl p-6 shadow-sm">
                <p class="text-sm text-zinc-500">Total Dosen</p>
                <p class="text-3xl font-semibold text-red-700 mt-2">{{ $counts['dosen'] ?? 0 }}</p>
            </div>
            <div class="bg-white border border-red-100 rounded-xl p-6 shadow-sm">
                <p class="text-sm text-zinc-500">Total Mahasiswa SI</p>
                <p class="text-3xl font-semibold text-red-700 mt-2">{{ $counts['mahasiswa'] ?? 0 }}</p>
            </div>
            <div class="bg-white border border-red-100 rounded-xl p-6 shadow-sm">
                <p class="text-sm text-zinc-500">Jadwal Aktif</p>
                <p class="text-3xl font-semibold text-red-700 mt-2">{{ $counts['jadwal_aktif'] ?? 0 }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="bg-white border border-zinc-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-sm font-semibold text-zinc-900 mb-4">Ringkasan Kualitas Jadwal</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span>Minimasi Konflik</span><span class="text-red-700 font-semibold">{{ $metrics['konflik'] ?? 0 }}%</span></div>
                    <div class="h-2 bg-zinc-100 rounded-full overflow-hidden"><div class="h-full bg-red-700" style="width: {{ $metrics['konflik'] ?? 0 }}%"></div></div>
                    <div class="flex justify-between"><span>Utilisasi Ruang</span><span class="text-red-700 font-semibold">{{ $metrics['utilisasi_ruang'] ?? 0 }}%</span></div>
                    <div class="h-2 bg-zinc-100 rounded-full overflow-hidden"><div class="h-full bg-red-700" style="width: {{ $metrics['utilisasi_ruang'] ?? 0 }}%"></div></div>
                    <div class="flex justify-between"><span>Kesesuaian Preferensi Dosen</span><span class="text-red-700 font-semibold">{{ $metrics['preferensi_dosen'] ?? 0 }}%</span></div>
                    <div class="h-2 bg-zinc-100 rounded-full overflow-hidden"><div class="h-full bg-red-700" style="width: {{ $metrics['preferensi_dosen'] ?? 0 }}%"></div></div>
                </div>
            </div>

            <div class="bg-white border border-zinc-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-sm font-semibold text-zinc-900 mb-4">Aktivitas Terkini</h2>
                <ul class="space-y-3 text-sm text-zinc-700">
                    @foreach (($activities ?? []) as $activity)
                        <li>{{ $activity }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection
