@extends('layouts.app')

@section('title', 'Ruang')

@section('content')
    <div class="p-8 space-y-6" x-data="ruangPage(@js($ruangs))">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Ruang</h1>
                <p class="text-sm text-zinc-500 mt-1">Data ruang SI dengan atribut lengkap</p>
            </div>
            <div class="text-sm text-zinc-500">
                Total: <span class="font-semibold text-zinc-800" x-text="filteredData().length"></span> ruang
            </div>
        </div>

        <input
            x-model="search"
            type="text"
            placeholder="Cari kode, nama, fasilitas, lokasi, atau status..."
            class="w-full px-3 py-2.5 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-500"
        >

        <div class="bg-white border border-zinc-200 rounded-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 text-zinc-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Kode</th>
                        <th class="px-6 py-3 text-left">Nama Ruang</th>
                        <th class="px-6 py-3 text-left">Kapasitas</th>
                        <th class="px-6 py-3 text-left">Fasilitas</th>
                        <th class="px-6 py-3 text-left">Lokasi</th>
                        <th class="px-6 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="item in filteredData()" :key="item.id">
                        <tr class="border-t border-zinc-200 hover:bg-zinc-50 align-top">
                            <td class="px-6 py-4 font-medium text-zinc-900" x-text="item.kode"></td>
                            <td class="px-6 py-4 text-zinc-700" x-text="item.nama"></td>
                            <td class="px-6 py-4 text-zinc-700" x-text="item.kapasitas + ' orang'"></td>
                            <td class="px-6 py-4 text-zinc-700" x-text="item.fasilitas"></td>
                            <td class="px-6 py-4 text-zinc-700" x-text="item.lokasi"></td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex px-2 py-1 rounded text-xs capitalize"
                                    :class="item.status === 'tersedia' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-red-100 text-red-700 border border-red-200'"
                                    x-text="item.statusLabel"
                                ></span>
                            </td>
                        </tr>
                    </template>

                    <tr x-show="filteredData().length === 0" class="border-t border-zinc-200">
                        <td colspan="6" class="px-6 py-10 text-center text-zinc-500">Data tidak ditemukan.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function ruangPage(initialRows) {
            const normalized = (initialRows || []).map((row) => {
                const status = (row.status || 'tersedia').toLowerCase();

                return {
                    id: row.id,
                    kode: row.kode,
                    nama: row.nama,
                    kapasitas: Number(row.kapasitas ?? 30),
                    fasilitas: row.fasilitas || '-',
                    lokasi: row.lokasi || '-',
                    status: status,
                    statusLabel: status === 'tersedia' ? 'Tersedia' : 'Tidak Tersedia'
                };
            });

            return {
                search: '',
                data: normalized,
                filteredData() {
                    const q = this.search.toLowerCase().trim();
                    if (!q) return this.data;

                    return this.data.filter((item) =>
                        item.kode.toLowerCase().includes(q)
                        || item.nama.toLowerCase().includes(q)
                        || item.fasilitas.toLowerCase().includes(q)
                        || item.lokasi.toLowerCase().includes(q)
                        || item.statusLabel.toLowerCase().includes(q)
                    );
                }
            }
        }
    </script>
@endpush
