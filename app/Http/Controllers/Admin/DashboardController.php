<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class DashboardController extends Controller
{
    public function index()
    {
        $counts = [
            'mata_kuliah' => DB::table('matakuliahs')->count(),
            'ruang' => DB::table('ruangs')->count(),
            'dosen' => DB::table('dosens')->count(),
            'jadwal_aktif' => DB::table('jadwals')->where('status', 'aktif')->count(),
            'mahasiswa' => $this->countMahasiswaSi(),
        ];

        $metrics = [
            'konflik' => $this->calculateConflictScore(),
            'utilisasi_ruang' => $this->calculateRoomUtilizationScore(),
            'preferensi_dosen' => $this->calculateTeacherPreferenceScore(),
        ];

        return view('dashboard.index', [
            'counts' => $counts,
            'metrics' => $metrics,
            'activities' => $this->buildRecentActivities(),
        ]);
    }

    public function mataKuliah()
    {
        return view('mata-kuliah.index');
    }

    public function ruang()
    {
        $ruangs = DB::table('ruangs')
            ->select('id', 'kode', 'nama', 'kapasitas', 'fasilitas', 'lokasi', 'status')
            ->orderBy('kode')
            ->get();

        return view('ruang.index', [
            'ruangs' => $ruangs,
        ]);
    }

    public function dosen()
    {
        $dosens = DB::table('dosens')
            ->select('id', 'kode_dosen', 'nip', 'nama_mata_kuliah', 'ketersediaan_waktu', 'beban_mengajar')
            ->orderBy('kode_dosen')
            ->orderBy('nip')
            ->get();

        return view('dosen.index', [
            'dosens' => $dosens,
        ]);
    }

    public function waktu()
    {
        return view('waktu.index');
    }

    public function algoritmaGenetika()
    {
        return view('algoritma-genetika.index');
    }

    public function generateJadwal()
    {
        $counts = [
            'mata_kuliah' => DB::table('matakuliahs')->count(),
            'ruang' => DB::table('ruangs')->count(),
            'dosen' => DB::table('dosens')->count(),
            'jadwal_aktif' => DB::table('jadwals')->where('status', 'aktif')->count(),
        ];

        return view('generate-jadwal.index', [
            'counts' => $counts,
            'result' => session('generationResult'),
        ]);
    }

    public function prosesGenerateJadwal(Request $request): RedirectResponse
    {
        $populationSize = max(10, min(100, (int) $request->input('population_size', 30)));
        $maxGenerations = max(10, min(500, (int) $request->input('max_generations', 80)));
        $mutationRate = max(0.01, min(0.5, (float) $request->input('mutation_rate', 0.1)));

        $mataKuliahIds = DB::table('matakuliahs')->pluck('id')->all();
        $dosenIds = DB::table('dosens')->pluck('id')->all();
        $ruangIds = DB::table('ruangs')->pluck('id')->all();

        if (empty($mataKuliahIds) || empty($dosenIds) || empty($ruangIds)) {
            return redirect()
                ->route('generate-jadwal.index')
                ->with('generationResult', [
                    'success' => false,
                    'message' => 'Data mata kuliah, dosen, dan ruang harus tersedia sebelum generate jadwal.',
                ]);
        }

        $slots = $this->buildSlots();

        $gaResult = $this->runPythonGa([
            'course_ids' => $mataKuliahIds,
            'dosen_ids' => $dosenIds,
            'ruang_ids' => $ruangIds,
            'slots' => $slots,
            'params' => [
                'population_size' => $populationSize,
                'max_generations' => $maxGenerations,
                'mutation_rate' => $mutationRate,
            ],
        ]);

        if (! $gaResult['success']) {
            return redirect()
                ->route('generate-jadwal.index')
                ->with('generationResult', [
                    'success' => false,
                    'message' => $gaResult['message'] ?? 'Proses GA Python gagal.',
                ]);
        }

        $bestSchedule = $gaResult['best_schedule'] ?? [];
        if (empty($bestSchedule)) {
            return redirect()
                ->route('generate-jadwal.index')
                ->with('generationResult', [
                    'success' => false,
                    'message' => 'GA Python tidak mengembalikan jadwal.',
                ]);
        }

        DB::transaction(function () use ($bestSchedule): void {
            DB::table('jadwals')->delete();

            $now = now();
            $payload = [];

            foreach ($bestSchedule as $gene) {
                $payload[] = [
                    'mata_kuliah_id' => (int) $gene['mata_kuliah_id'],
                    'dosen_id' => (int) $gene['dosen_id'],
                    'ruang_id' => (int) $gene['ruang_id'],
                    'hari' => (string) $gene['hari'],
                    'jam_mulai' => (string) $gene['jam_mulai'],
                    'jam_selesai' => (string) $gene['jam_selesai'],
                    'status' => 'aktif',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('jadwals')->insert($payload);
        });

        return redirect()
            ->route('generate-jadwal.index')
            ->with('generationResult', [
                'success' => true,
                'message' => $gaResult['message'] ?? 'Optimisasi selesai dari Python GA.',
                'finalFitness' => $gaResult['final_fitness'] ?? 0,
                'scheduleCount' => $gaResult['schedule_count'] ?? count($bestSchedule),
                'conflicts' => $gaResult['conflicts'] ?? 0,
                'executionTime' => ($gaResult['execution_time_seconds'] ?? 0) . ' detik',
                'roomUtilization' => $gaResult['room_utilization'] ?? 0,
                'teacherPreferences' => $gaResult['teacher_preferences'] ?? 0,
                'generationCount' => $gaResult['generation_count'] ?? $maxGenerations,
            ]);
    }

    public function kalender()
    {
        $jadwals = DB::table('jadwals')
            ->join('matakuliahs', 'jadwals.mata_kuliah_id', '=', 'matakuliahs.id')
            ->where('jadwals.status', 'aktif')
            ->select('jadwals.hari', 'jadwals.jam_mulai', 'jadwals.jam_selesai', 'matakuliahs.kode', 'matakuliahs.nama')
            ->get();

        $events = $this->mapJadwalToCalendarEvents($jadwals);

        return view('kalender.index', [
            'events' => $events,
        ]);
    }

    private function countMahasiswaSi(): int
    {
        if (! Schema::hasTable('mahasiswas')) {
            return 0;
        }

        $query = DB::table('mahasiswas');

        if (Schema::hasColumn('mahasiswas', 'prodi')) {
            $query->where('prodi', 'like', '%sistem informasi%')
                ->orWhere('prodi', 'like', '%s1 si%')
                ->orWhere('prodi', 'like', '%si%');
        }

        return $query->count();
    }

    private function calculateConflictScore(): int
    {
        $total = DB::table('jadwals')->where('status', 'aktif')->count();
        if ($total === 0) {
            return 0;
        }

        $duplicateSlots = DB::table('jadwals')
            ->select('hari', 'jam_mulai', 'jam_selesai', 'ruang_id', DB::raw('count(*) as total'))
            ->where('status', 'aktif')
            ->groupBy('hari', 'jam_mulai', 'jam_selesai', 'ruang_id')
            ->havingRaw('count(*) > 1')
            ->get()
            ->sum(fn ($row) => (int) $row->total - 1);

        $score = (int) round((1 - ($duplicateSlots / max(1, $total))) * 100);

        return max(0, min(100, $score));
    }

    private function calculateRoomUtilizationScore(): int
    {
        $ruangCount = DB::table('ruangs')->count();
        $jadwalCount = DB::table('jadwals')->where('status', 'aktif')->count();
        $slotPerMinggu = 20;

        if ($ruangCount === 0) {
            return 0;
        }

        $maxCapacity = $ruangCount * $slotPerMinggu;
        $score = (int) round(($jadwalCount / max(1, $maxCapacity)) * 100);

        return max(0, min(100, $score));
    }

    private function calculateTeacherPreferenceScore(): int
    {
        if (! Schema::hasTable('dosens') || ! Schema::hasColumn('dosens', 'ketersediaan_waktu')) {
            return 0;
        }

        $totalAktif = DB::table('jadwals')->where('status', 'aktif')->count();
        if ($totalAktif === 0) {
            return 0;
        }

        $matchCount = DB::table('jadwals')
            ->join('dosens', 'jadwals.dosen_id', '=', 'dosens.id')
            ->where('jadwals.status', 'aktif')
            ->whereNotNull('dosens.ketersediaan_waktu')
            ->whereRaw('LOWER(dosens.ketersediaan_waktu) LIKE CONCAT("%", LOWER(jadwals.hari), "%")')
            ->count();

        $score = (int) round(($matchCount / max(1, $totalAktif)) * 100);

        return max(0, min(100, $score));
    }

    /**
     * @return array<int, string>
     */
    private function buildRecentActivities(): array
    {
        $activities = [];

        $tables = [
            'matakuliahs' => 'Data mata kuliah diperbarui',
            'ruangs' => 'Data ruang diperbarui',
            'dosens' => 'Data dosen diperbarui',
            'jadwals' => 'Jadwal diproses',
        ];

        foreach ($tables as $table => $label) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $latest = DB::table($table)->max('updated_at');
            if (! $latest) {
                continue;
            }

            $time = Carbon::parse($latest)->diffForHumans();
            $activities[] = $label.' ('.$time.')';
        }

        if (empty($activities)) {
            $activities[] = 'Belum ada aktivitas data yang tercatat.';
        }

        return array_slice($activities, 0, 4);
    }

    private function buildSlots(): array
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $hours = [
            ['08:00:00', '10:00:00'],
            ['10:15:00', '12:15:00'],
            ['13:00:00', '15:00:00'],
            ['15:15:00', '17:15:00'],
        ];

        $slots = [];
        foreach ($days as $day) {
            foreach ($hours as [$start, $end]) {
                $slots[] = [
                    'hari' => $day,
                    'jam_mulai' => $start,
                    'jam_selesai' => $end,
                ];
            }
        }

        return $slots;
    }

    private function runPythonGa(array $payload): array
    {
        $scriptPath = base_path('python/ga_scheduler.py');
        if (! file_exists($scriptPath)) {
            return [
                'success' => false,
                'message' => 'File Python GA tidak ditemukan di folder python/ga_scheduler.py.',
            ];
        }

        $tempDir = storage_path('app/private');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $token = uniqid('ga_', true);
        $inputPath = $tempDir.DIRECTORY_SEPARATOR.$token.'_input.json';
        $outputPath = $tempDir.DIRECTORY_SEPARATOR.$token.'_output.json';

        file_put_contents($inputPath, json_encode($payload, JSON_PRETTY_PRINT));

        $pythonBin = env('GA_PYTHON_BIN', 'python');
        $command = $pythonBin
            . ' '.escapeshellarg($scriptPath)
            . ' --input '.escapeshellarg($inputPath)
            . ' --output '.escapeshellarg($outputPath);

        try {
            $logs = [];
            $exitCode = 1;
            exec($command.' 2>&1', $logs, $exitCode);

            if ($exitCode !== 0) {
                return [
                    'success' => false,
                    'message' => 'Eksekusi Python GA gagal: '.implode("\n", $logs),
                ];
            }

            if (! file_exists($outputPath)) {
                return [
                    'success' => false,
                    'message' => 'Output JSON dari Python GA tidak ditemukan.',
                ];
            }

            $rawOutput = file_get_contents($outputPath);
            $decoded = json_decode($rawOutput, true);

            if (! is_array($decoded)) {
                return [
                    'success' => false,
                    'message' => 'Output Python GA tidak valid JSON.',
                ];
            }

            return $decoded;
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Gagal menjalankan Python GA: '.$e->getMessage(),
            ];
        } finally {
            if (file_exists($inputPath)) {
                unlink($inputPath);
            }

            if (file_exists($outputPath)) {
                unlink($outputPath);
            }
        }
    }

    private function mapJadwalToCalendarEvents($jadwals): array
    {
        $dayMap = [
            'Senin' => 0,
            'Selasa' => 1,
            'Rabu' => 2,
            'Kamis' => 3,
            'Jumat' => 4,
            'Sabtu' => 5,
            'Minggu' => 6,
        ];

        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $events = [];

        foreach ($jadwals as $jadwal) {
            if (! isset($dayMap[$jadwal->hari])) {
                continue;
            }

            $date = $startOfWeek->copy()->addDays($dayMap[$jadwal->hari])->format('Y-m-d');

            $events[] = [
                'title' => $jadwal->nama.' - '.$jadwal->kode,
                'start' => $date.'T'.$jadwal->jam_mulai,
                'end' => $date.'T'.$jadwal->jam_selesai,
            ];
        }

        return $events;
    }
}
