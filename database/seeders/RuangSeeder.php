<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RuangSeeder extends Seeder
{
    /**
     * Import data ruang SI dari file CSV di folder database/data/ruang.
     */
    public function run(): void
    {
        $basePath = database_path('data/ruang');
        $files = [
            'gedung_tult.csv' => 'TULT',
            'gedung_gku.csv' => 'GKU',
            'gedung_b.csv' => 'B',
        ];

        // Reset agar tabel ruangs hanya berisi ruang SI hasil filter terbaru.
        DB::table('ruangs')->delete();

        foreach ($files as $fileName => $gedung) {
            $path = $basePath.DIRECTORY_SEPARATOR.$fileName;

            if (! is_file($path)) {
                $this->command?->warn("File tidak ditemukan: {$path}");
                continue;
            }

            $rows = $this->readCsvRows($path);
            $imported = 0;

            foreach ($rows as $row) {
                $parsed = $this->extractRoomData($row, $gedung);

                if (! $parsed) {
                    continue;
                }

                DB::table('ruangs')->updateOrInsert(
                    ['kode' => $parsed['kode']],
                    [
                        'nama' => $parsed['nama'],
                        'kapasitas' => $parsed['kapasitas'],
                        'fasilitas' => $parsed['fasilitas'],
                        'lokasi' => $parsed['lokasi'],
                        'status' => $parsed['status'],
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );

                $imported++;
            }

            $this->command?->info("Selesai import SI: {$fileName} ({$imported} baris)");
        }
    }

    /**
     * @return array<int, array<int, string>>
     */
    private function readCsvRows(string $path): array
    {
        $handle = fopen($path, 'r');

        if (! $handle) {
            return [];
        }

        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            return [];
        }

        $delimiter = str_contains($firstLine, ';') ? ';' : ',';
        rewind($handle);

        $rows = [];

        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            if ($this->isEmptyRow($data)) {
                continue;
            }

            $rows[] = array_map(static fn ($value) => trim((string) $value), $data);
        }

        fclose($handle);

        return $rows;
    }

    /**
     * @param array<int, string> $row
     * @return array{kode:string,nama:string,kapasitas:int,fasilitas:string,lokasi:string,status:string}|null
     */
    private function extractRoomData(array $row, string $gedung): ?array
    {
        $upperRow = array_map(static fn ($value) => strtoupper($value), $row);
        $joined = implode(' ', $upperRow);

        if ($this->isHeaderOrTitleRow($upperRow, $joined)) {
            return null;
        }

        if (! $this->isSistemInformasiRow($joined)) {
            return null;
        }

        $roomText = $this->findRoomText($row);

        if ($roomText === '') {
            return null;
        }

        $baseCode = $this->extractCode($roomText);

        if ($baseCode === '') {
            return null;
        }

        $nama = trim(preg_replace('/\s+/', ' ', $roomText) ?? $roomText);
        if (! str_contains(strtoupper($nama), $gedung)) {
            $nama = $gedung.' - '.$nama;
        }

        $kapasitas = $this->extractCapacity($row);

        return [
            'kode' => strtoupper($gedung.'-'.$baseCode),
            'nama' => $nama,
            'kapasitas' => $kapasitas,
            'fasilitas' => $this->extractFasilitas($row, $roomText),
            'lokasi' => $this->extractLokasi($gedung, $baseCode, $roomText),
            'status' => $this->extractStatus($row),
        ];
    }

    private function isSistemInformasiRow(string $joined): bool
    {
        $normalized = preg_replace('/\s+/', ' ', $joined) ?? $joined;

        return str_contains($normalized, 'S1 SI')
            || str_contains($normalized, 'S1SI')
            || str_contains($normalized, 'SISTEM INFORMASI')
            || str_contains($normalized, 'SI (INT)')
            || str_contains($normalized, 'SI(INT)');
    }

    /**
     * @param array<int, string> $upperRow
     */
    private function isHeaderOrTitleRow(array $upperRow, string $joined): bool
    {
        $first = trim($upperRow[0] ?? '');

        if ($first === 'NO' || $first === 'N0') {
            return true;
        }

        $blockedKeywords = [
            'FASILITAS',
            'JATAH',
            'SHIFT KOSONG',
            'LANTAI',
            'KAPASITAS PERKULIAHAN',
            'KAPASITAS UJIAN',
            'PRODI',
            'KETERANGAN',
            'KELAS PER FAKULTAS',
        ];

        foreach ($blockedKeywords as $keyword) {
            if (str_contains($joined, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<int, string> $row
     */
    private function findRoomText(array $row): string
    {
        foreach ($row as $index => $value) {
            $cell = trim($value);
            if ($cell === '') {
                continue;
            }

            $upper = strtoupper($cell);

            if ($upper === 'RUANG' && isset($row[$index + 1]) && trim((string) $row[$index + 1]) !== '') {
                return 'RUANG '.trim((string) $row[$index + 1]);
            }

            if (
                str_contains($upper, 'RUANG')
                || preg_match('/KU\d\.\d{2}\.\d{2}/i', $cell)
                || preg_match('/<\s*B\d{3}[A-Z]?\s*>/i', $cell)
                || preg_match('/\b\d{4}\b/', $cell)
            ) {
                return $cell;
            }
        }

        return '';
    }

    private function extractCode(string $roomText): string
    {
        if (preg_match('/<\s*([A-Z]\d{3}[A-Z]?)\s*>/i', $roomText, $match) === 1) {
            return strtoupper($match[1]);
        }

        if (preg_match('/(KU\d\.\d{2}\.\d{2})/i', $roomText, $match) === 1) {
            return strtoupper($match[1]);
        }

        if (preg_match('/\b(\d{4})\b/', $roomText, $match) === 1) {
            return $match[1];
        }

        if (preg_match('/\b([A-Z]\d{3}[A-Z]?)\b/i', $roomText, $match) === 1) {
            return strtoupper($match[1]);
        }

        return '';
    }

    /**
     * @param array<int, string> $row
     */
    private function extractCapacity(array $row): int
    {
        $cells = array_slice($row, 1);

        foreach ($cells as $value) {
            if (preg_match('/\b(\d{2,3})\b/', $value, $match) === 1) {
                $num = (int) $match[1];
                if ($num >= 10 && $num <= 300) {
                    return $num;
                }
            }
        }

        return 30;
    }

    /**
     * @param array<int, string> $row
     */
    private function extractFasilitas(array $row, string $roomText): string
    {
        $joined = strtoupper(implode(' ', $row).' '.$roomText);

        if (str_contains($joined, 'ACTIVE LEARNING')) {
            return 'Active learning setup, layar presentasi, kursi fleksibel';
        }

        if (str_contains($joined, 'LAB')) {
            return 'Laboratorium komputer, proyektor, akses jaringan';
        }

        if (str_contains($joined, 'KURSI CHITOSE')) {
            return 'Kursi chitose, whiteboard, proyektor';
        }

        if (str_contains($joined, 'BERMEJA')) {
            return 'Meja kursi kuliah, whiteboard, proyektor';
        }

        return 'Whiteboard, proyektor, meja kursi kuliah';
    }

    private function extractLokasi(string $gedung, string $baseCode, string $roomText): string
    {
        $gedungLabel = match (strtoupper($gedung)) {
            'TULT' => 'Gedung TULT',
            'GKU' => 'Gedung GKU',
            'B' => 'Gedung B',
            default => 'Gedung '.$gedung,
        };

        $lantai = null;

        if (preg_match('/KU\d\.(\d{2})\.\d{2}/i', $roomText, $match) === 1) {
            $lantai = (int) $match[1];
        } elseif (preg_match('/^\d{4}$/', $baseCode) === 1) {
            $lantai = (int) substr($baseCode, 0, 1);
        } elseif (preg_match('/^[A-Z](\d)/', $baseCode, $match) === 1) {
            $lantai = (int) $match[1];
        }

        if ($lantai !== null && $lantai > 0) {
            return $gedungLabel.', Lantai '.$lantai;
        }

        return $gedungLabel;
    }

    /**
     * @param array<int, string> $row
     */
    private function extractStatus(array $row): string
    {
        $joined = strtoupper(implode(' ', $row));

        if (
            str_contains($joined, 'RUSAK')
            || str_contains($joined, 'RENOVASI')
            || str_contains($joined, 'NONAKTIF')
            || str_contains($joined, 'TIDAK TERSEDIA')
        ) {
            return 'tidak_tersedia';
        }

        return 'tersedia';
    }

    /**
     * @param array<int, mixed> $row
     */
    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }
}
