<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_kuliah_id')->constrained('matakuliahs')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('dosen_id')->constrained('dosens')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('ruang_id')->constrained('ruangs')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('hari');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->enum('status', ['aktif', 'batal'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};
