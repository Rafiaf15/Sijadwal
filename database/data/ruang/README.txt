Format file CSV untuk import ruang:

1. Letakkan 3 file di folder ini dengan nama:
   - gedung_tult.csv
   - gedung_gku.csv
   - gedung_b.csv

2. Header minimal yang didukung (huruf besar/kecil bebas):
   - kode
   - nama
   - kapasitas

Alternatif header yang juga didukung:
- kode ruang / koderuang / room code
- nama ruang / namaruang / room name
- kap / capacity

3. Contoh isi:
kode,nama,kapasitas
TULT-301,Ruang 301,40
TULT-302,Ruang 302,35

4. Jalankan import:
php artisan db:seed --class=RuangSeeder

Catatan:
- Jika nama ruang belum mengandung nama gedung, seeder otomatis menambahkan prefix (TULT/GKU/B).
- Data update berdasarkan kolom kode (kode yang sama akan di-update, bukan dobel).
