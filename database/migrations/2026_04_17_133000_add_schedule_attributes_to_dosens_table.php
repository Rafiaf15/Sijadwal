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
        Schema::table('dosens', function (Blueprint $table) {
            $table->string('kode_dosen')->nullable()->unique()->after('id');
            $table->string('nama_mata_kuliah')->nullable()->after('nama');
            $table->text('ketersediaan_waktu')->nullable()->after('nama_mata_kuliah');
            $table->unsignedTinyInteger('beban_mengajar')->default(0)->after('ketersediaan_waktu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dosens', function (Blueprint $table) {
            $table->dropColumn(['kode_dosen', 'nama_mata_kuliah', 'ketersediaan_waktu', 'beban_mengajar']);
        });
    }
};
