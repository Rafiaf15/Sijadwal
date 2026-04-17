@extends('layouts.app')

@section('title', 'Mata Kuliah')

@section('content')
    <div class="p-8 space-y-6" x-data="mataKuliahPage()">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Mata Kuliah</h1>
                <p class="text-sm text-zinc-500 mt-1">Kelola data mata kuliah</p>
            </div>
            <button @click="openCreate()" class="px-4 py-2 btn-telu text-white rounded-lg hover:bg-red-800">
                Tambah Mata Kuliah
            </button>
        </div>

        <input
            x-model="search"
            type="text"
            placeholder="Cari mata kuliah..."
            class="w-full px-3 py-2.5 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-500"
        >

        <div class="bg-white border border-zinc-200 rounded-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 text-zinc-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Kode</th>
                        <th class="px-6 py-3 text-left">Nama</th>
                        <th class="px-6 py-3 text-left">SKS</th>
                        <th class="px-6 py-3 text-left">Semester</th>
                        <th class="px-6 py-3 text-left">Jumlah Mahasiswa</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="item in filteredData()" :key="item.id">
                        <tr class="border-t border-zinc-200 hover:bg-zinc-50">
                            <td class="px-6 py-4 font-medium text-zinc-900" x-text="item.kode"></td>
                            <td class="px-6 py-4 text-zinc-700" x-text="item.nama"></td>
                            <td class="px-6 py-4 text-zinc-700" x-text="item.sks"></td>
                            <td class="px-6 py-4 text-zinc-700" x-text="item.semester"></td>
                            <td class="px-6 py-4 text-zinc-700" x-text="item.jumlahMahasiswa"></td>
                            <td class="px-6 py-4 text-right">
                                <button @click="openEdit(item.id)" class="px-3 py-1.5 text-sm rounded-md border border-zinc-300 hover:bg-zinc-100">
                                    Edit
                                </button>
                                <button @click="remove(item.id)" class="ml-2 px-3 py-1.5 text-sm rounded-md border border-red-200 text-red-600 hover:bg-red-50">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    </template>

                    <tr x-show="filteredData().length === 0" class="border-t border-zinc-200">
                        <td colspan="6" class="px-6 py-10 text-center text-zinc-500">
                            Data tidak ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            x-show="showModal"
            x-transition
            class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50"
            style="display: none;"
        >
            <div class="bg-white rounded-lg w-full max-w-md">
                <div class="flex items-center justify-between p-6 border-b border-zinc-200">
                    <h2 class="text-lg font-semibold" x-text="editingId ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah'"></h2>
                    <button @click="closeModal()" class="text-zinc-400 hover:text-zinc-600">X</button>
                </div>

                <form @submit.prevent="save()" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-1.5">Kode Mata Kuliah</label>
                        <input x-model="form.kode" type="text" required class="w-full px-3 py-2 border border-zinc-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-1.5">Nama Mata Kuliah</label>
                        <input x-model="form.nama" type="text" required class="w-full px-3 py-2 border border-zinc-300 rounded-lg">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1.5">SKS</label>
                            <input x-model.number="form.sks" type="number" min="1" max="6" required class="w-full px-3 py-2 border border-zinc-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1.5">Semester</label>
                            <input x-model.number="form.semester" type="number" min="1" max="8" required class="w-full px-3 py-2 border border-zinc-300 rounded-lg">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-1.5">Jumlah Mahasiswa</label>
                        <input x-model.number="form.jumlahMahasiswa" type="number" min="1" required class="w-full px-3 py-2 border border-zinc-300 rounded-lg">
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="closeModal()" class="flex-1 px-4 py-2 border border-zinc-300 text-zinc-700 rounded-lg hover:bg-zinc-50">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 btn-telu text-white rounded-lg hover:bg-red-800">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function mataKuliahPage() {
            return {
                search: '',
                showModal: false,
                editingId: null,
                data: [
                    { id: 1, kode: 'IF101', nama: 'Pemrograman Dasar', sks: 3, semester: 1, jumlahMahasiswa: 45 },
                    { id: 2, kode: 'IF102', nama: 'Matematika Diskrit', sks: 3, semester: 1, jumlahMahasiswa: 42 },
                    { id: 3, kode: 'IF201', nama: 'Struktur Data', sks: 4, semester: 3, jumlahMahasiswa: 38 },
                    { id: 4, kode: 'IF202', nama: 'Basis Data', sks: 3, semester: 3, jumlahMahasiswa: 40 },
                    { id: 5, kode: 'IF301', nama: 'Algoritma Genetika', sks: 3, semester: 5, jumlahMahasiswa: 35 }
                ],
                form: {
                    kode: '',
                    nama: '',
                    sks: 3,
                    semester: 1,
                    jumlahMahasiswa: 0
                },
                filteredData() {
                    const q = this.search.toLowerCase().trim();
                    if (!q) return this.data;
                    return this.data.filter(item =>
                        item.nama.toLowerCase().includes(q) || item.kode.toLowerCase().includes(q)
                    );
                },
                resetForm() {
                    this.form = { kode: '', nama: '', sks: 3, semester: 1, jumlahMahasiswa: 0 };
                    this.editingId = null;
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
                    this.showModal = true;
                },
                closeModal() {
                    this.showModal = false;
                    this.resetForm();
                },
                save() {
                    if (this.editingId) {
                        this.data = this.data.map(item => item.id === this.editingId ? { ...this.form, id: this.editingId } : item);
                    } else {
                        this.data.push({ ...this.form, id: Date.now() });
                    }
                    this.closeModal();
                },
                remove(id) {
                    if (!confirm('Hapus mata kuliah ini?')) return;
                    this.data = this.data.filter(item => item.id !== id);
                }
            }
        }
    </script>
@endpush

