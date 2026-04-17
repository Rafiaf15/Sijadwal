@extends('layouts.app')

@section('title', 'Waktu')

@section('content')
    <div class="p-8 space-y-6" x-data="waktuPage()">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Waktu</h1>
                <p class="text-sm text-zinc-500 mt-1">Kelola slot waktu perkuliahan</p>
            </div>
            <button @click="openCreate()" class="px-4 py-2 btn-telu text-white rounded-lg hover:bg-red-800">Tambah Slot Waktu</button>
        </div>

        <div class="flex gap-4">
            <input
                x-model="search"
                type="text"
                placeholder="Cari slot waktu..."
                class="flex-1 px-3 py-2.5 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-500"
            >
            <select x-model="filterHari" class="px-4 py-2.5 bg-white border border-zinc-200 rounded-lg text-sm">
                <option value="Semua">Semua Hari</option>
                <template x-for="hari in hariOptions" :key="hari">
                    <option :value="hari" x-text="hari"></option>
                </template>
            </select>
        </div>

        <div class="bg-white border border-zinc-200 rounded-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 text-zinc-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Hari</th>
                        <th class="px-6 py-3 text-left">Jam Mulai</th>
                        <th class="px-6 py-3 text-left">Jam Selesai</th>
                        <th class="px-6 py-3 text-left">Durasi</th>
                        <th class="px-6 py-3 text-left">Sesi</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="item in filteredData()" :key="item.id">
                        <tr class="border-t border-zinc-200 hover:bg-zinc-50">
                            <td class="px-6 py-4 font-medium text-zinc-900" x-text="item.hari"></td>
                            <td class="px-6 py-4 text-zinc-700" x-text="item.jamMulai"></td>
                            <td class="px-6 py-4 text-zinc-700" x-text="item.jamSelesai"></td>
                            <td class="px-6 py-4 text-zinc-700" x-text="durationLabel(item.jamMulai, item.jamSelesai)"></td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex px-2 py-1 rounded text-xs"
                                    :class="item.sesi === 'Pagi' ? 'bg-rose-50 text-rose-700 border border-rose-200' : (item.sesi === 'Siang' ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-red-200 text-red-800 border border-red-300')"
                                    x-text="item.sesi"
                                ></span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button @click="openEdit(item.id)" class="px-3 py-1.5 text-sm rounded-md border border-zinc-300 hover:bg-zinc-100">Edit</button>
                                <button @click="remove(item.id)" class="ml-2 px-3 py-1.5 text-sm rounded-md border border-red-200 text-red-600 hover:bg-red-50">Hapus</button>
                            </td>
                        </tr>
                    </template>

                    <tr x-show="filteredData().length === 0" class="border-t border-zinc-200">
                        <td colspan="6" class="px-6 py-10 text-center text-zinc-500">Data tidak ditemukan.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            x-show="showModal"
            x-cloak
            x-transition
            class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50"
        >
            <div class="bg-white rounded-lg w-full max-w-md">
                <div class="flex items-center justify-between p-6 border-b border-zinc-200">
                    <h2 class="text-lg font-semibold" x-text="editingId ? 'Edit Slot Waktu' : 'Tambah Slot Waktu'"></h2>
                    <button @click="closeModal()" class="text-zinc-400 hover:text-zinc-600">X</button>
                </div>

                <form @submit.prevent="save()" class="p-6 space-y-4">
                    <template x-if="errorMessage">
                        <div class="px-3 py-2 rounded-lg border border-red-200 bg-red-50 text-red-700 text-sm" x-text="errorMessage"></div>
                    </template>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-1.5">Hari</label>
                        <select x-model="form.hari" class="w-full px-3 py-2 border border-zinc-300 rounded-lg">
                            <template x-for="hari in hariOptions" :key="hari">
                                <option :value="hari" x-text="hari"></option>
                            </template>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1.5">Jam Mulai</label>
                            <input x-model="form.jamMulai" type="time" required class="w-full px-3 py-2 border border-zinc-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1.5">Jam Selesai</label>
                            <input x-model="form.jamSelesai" type="time" required class="w-full px-3 py-2 border border-zinc-300 rounded-lg">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-1.5">Sesi</label>
                        <select x-model="form.sesi" class="w-full px-3 py-2 border border-zinc-300 rounded-lg">
                            <template x-for="sesi in sesiOptions" :key="sesi">
                                <option :value="sesi" x-text="sesi"></option>
                            </template>
                        </select>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="closeModal()" class="flex-1 px-4 py-2 border border-zinc-300 text-zinc-700 rounded-lg hover:bg-zinc-50">Batal</button>
                        <button type="submit" class="flex-1 px-4 py-2 btn-telu text-white rounded-lg hover:bg-red-800">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function waktuPage() {
            return {
                search: '',
                filterHari: 'Semua',
                showModal: false,
                editingId: null,
                errorMessage: '',
                hariOptions: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
                sesiOptions: ['Pagi', 'Siang', 'Sore'],
                data: [
                    { id: 1, hari: 'Senin', jamMulai: '08:00', jamSelesai: '10:00', sesi: 'Pagi' },
                    { id: 2, hari: 'Senin', jamMulai: '10:00', jamSelesai: '12:00', sesi: 'Pagi' },
                    { id: 3, hari: 'Selasa', jamMulai: '13:00', jamSelesai: '15:00', sesi: 'Siang' },
                    { id: 4, hari: 'Rabu', jamMulai: '08:00', jamSelesai: '10:00', sesi: 'Pagi' },
                    { id: 5, hari: 'Kamis', jamMulai: '15:00', jamSelesai: '17:00', sesi: 'Sore' }
                ],
                form: {
                    hari: 'Senin',
                    jamMulai: '08:00',
                    jamSelesai: '10:00',
                    sesi: 'Pagi'
                },
                filteredData() {
                    const q = this.search.toLowerCase().trim();
                    return this.data.filter(item => {
                        const matchQuery = !q || item.hari.toLowerCase().includes(q) || item.sesi.toLowerCase().includes(q);
                        const matchHari = this.filterHari === 'Semua' || item.hari === this.filterHari;
                        return matchQuery && matchHari;
                    });
                },
                toMinutes(value) {
                    const [hours, minutes] = value.split(':').map(Number);
                    return (hours * 60) + minutes;
                },
                durationLabel(start, end) {
                    const diff = this.toMinutes(end) - this.toMinutes(start);
                    if (diff <= 0) return 'Tidak valid';
                    const jam = diff / 60;
                    return Number.isInteger(jam) ? jam + ' jam' : (jam.toFixed(1) + ' jam');
                },
                resetForm() {
                    this.form = { hari: 'Senin', jamMulai: '08:00', jamSelesai: '10:00', sesi: 'Pagi' };
                    this.editingId = null;
                    this.errorMessage = '';
                },
                openCreate() {
                    this.resetForm();
                    this.showModal = true;
                },
                openEdit(id) {
                    const item = this.data.find(entry => entry.id === id);
                    if (!item) return;
                    this.form = { ...item };
                    this.editingId = id;
                    this.errorMessage = '';
                    this.showModal = true;
                },
                closeModal() {
                    this.showModal = false;
                    this.resetForm();
                },
                save() {
                    this.errorMessage = '';
                    if (this.toMinutes(this.form.jamSelesai) <= this.toMinutes(this.form.jamMulai)) {
                        this.errorMessage = 'Jam selesai harus lebih besar dari jam mulai.';
                        return;
                    }

                    if (this.editingId) {
                        this.data = this.data.map(item => item.id === this.editingId ? { ...this.form, id: this.editingId } : item);
                    } else {
                        this.data.push({ ...this.form, id: Date.now() });
                    }
                    this.closeModal();
                },
                remove(id) {
                    if (!confirm('Hapus slot waktu ini?')) return;
                    this.data = this.data.filter(item => item.id !== id);
                }
            }
        }
    </script>
@endpush
