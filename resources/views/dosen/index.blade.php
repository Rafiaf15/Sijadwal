@extends('layouts.app')

@section('title', 'Dosen')

@section('content')
    <div class="p-8 space-y-6" x-data="dosenPage(@js($dosens))">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Data Jadwal Dosen</h1>
                <p class="text-sm text-zinc-500 mt-1">Atribut: kode dosen, mata kuliah, ketersediaan waktu, dan beban mengajar</p>
            </div>
            <div class="text-sm text-zinc-500">
                Total: <span class="font-semibold text-zinc-800" x-text="filteredData().length"></span> data
            </div>
        </div>

        <input
            x-model="search"
            type="text"
            placeholder="Cari kode dosen atau nama mata kuliah..."
            class="w-full px-3 py-2.5 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-500"
        >

        <div class="bg-white border border-zinc-200 rounded-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 text-zinc-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Kode Dosen</th>
                        <th class="px-6 py-3 text-left">Nama Mata Kuliah</th>
                        <th class="px-6 py-3 text-left">Ketersediaan Waktu</th>
                        <th class="px-6 py-3 text-left">Beban Mengajar (SKS)</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="item in filteredData()" :key="item.id">
                        <tr class="border-t border-zinc-200 hover:bg-zinc-50 align-top">
                            <td class="px-6 py-4 font-medium text-zinc-900" x-text="item.kode_dosen"></td>
                            <td class="px-6 py-4 text-zinc-700" x-text="item.nama_mata_kuliah"></td>
                            <td class="px-6 py-4 text-zinc-700 whitespace-pre-line" x-text="item.ketersediaan_waktu"></td>
                            <td class="px-6 py-4 text-zinc-700" x-text="item.beban_mengajar"></td>
                        </tr>
                    </template>

                    <tr x-show="filteredData().length === 0" class="border-t border-zinc-200">
                        <td colspan="4" class="px-6 py-10 text-center text-zinc-500">Belum ada data dosen. Data masih dalam proses pengumpulan.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function dosenPage(initialRows) {
            const normalized = (initialRows || []).map((row) => ({
                id: row.id,
                kode_dosen: row.kode_dosen || row.nip || '-',
                nama_mata_kuliah: row.nama_mata_kuliah || '-',
                ketersediaan_waktu: row.ketersediaan_waktu || '-',
                beban_mengajar: Number(row.beban_mengajar ?? 0)
            }));

            return {
                search: '',
                data: normalized,
                filteredData() {
                    const q = this.search.toLowerCase().trim();
                    if (!q) return this.data;

                    return this.data.filter((item) =>
                        item.kode_dosen.toLowerCase().includes(q)
                        || item.nama_mata_kuliah.toLowerCase().includes(q)
                    );
                }
            }
        }
    </script>
@endpush
