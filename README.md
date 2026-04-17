# SiJadwal

SiJadwal adalah aplikasi web berbasis Laravel untuk pengelolaan dan optimisasi jadwal perkuliahan Prodi Sistem Informasi. Aplikasi ini menyediakan dashboard admin/staff akademik untuk mengelola data mata kuliah, dosen, ruang, waktu, kalender jadwal, serta proses generate jadwal berbasis algoritma genetika.

## Fitur Utama

- Login staff akademik.
- Dashboard ringkasan data dan metrik jadwal.
- Manajemen data mata kuliah.
- Manajemen data dosen.
- Manajemen data ruang.
- Manajemen slot waktu perkuliahan.
- Generate jadwal otomatis melalui proses algoritma genetika.
- Tampilan kalender jadwal aktif.
- UI berbasis Blade Laravel, tanpa React.

## Teknologi

- Laravel 12
- PHP 8.2+
- Blade Template
- Alpine.js
- Tailwind CSS
- Python untuk proses optimisasi jadwal

## Struktur Folder Penting

- `app/Http/Controllers/Admin/DashboardController.php` - controller utama halaman admin.
- `app/Http/Controllers/Auth/LoginController.php` - proses login dan logout.
- `resources/views/` - seluruh tampilan Blade.
- `public/css/app-layout.css` - CSS bersama untuk layout dan utility custom.
- `python/ga_scheduler.py` - skrip Python untuk algoritma genetika.
- `database/migrations/` - struktur tabel database.
- `database/seeders/` - data awal untuk ruangan dan staff akademik.

## Instalasi

1. Clone project ini.
2. Install dependency PHP:
   ```bash
   composer install
   ```
3. Copy file environment:
   ```bash
   copy .env.example .env
   ```
4. Generate key aplikasi:
   ```bash
   php artisan key:generate
   ```
5. Jalankan migrasi database:
   ```bash
   php artisan migrate
   ```
6. Install dependency frontend:
   ```bash
   npm install
   ```
7. Build aset frontend:
   ```bash
   npm run build
   ```

## Menjalankan Aplikasi

Untuk mode development:

```bash
php artisan serve
```

Jika memakai Vite selama development:

```bash
npm run dev
```

## Data Awal

Jika ingin mengisi data awal ruangan dan staff akademik, jalankan seeder yang tersedia:

```bash
php artisan db:seed
```

## Catatan

- CSS khusus layout dipisahkan ke file `public/css/app-layout.css`, jadi tidak dicampur ke HTML Blade.
- Proses generate jadwal memanggil skrip Python dari folder `python/`.
- Pastikan database sudah terkonfigurasi dengan benar sebelum menjalankan fitur generate jadwal.

## Lisensi

Project ini dibuat untuk kebutuhan tugas akhir dan mengikuti lisensi yang berlaku pada kode sumber di dalam repository ini.
