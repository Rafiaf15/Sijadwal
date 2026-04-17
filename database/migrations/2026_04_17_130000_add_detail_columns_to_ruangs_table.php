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
        Schema::table('ruangs', function (Blueprint $table) {
            $table->text('fasilitas')->nullable()->after('kapasitas');
            $table->string('lokasi')->nullable()->after('fasilitas');
            $table->string('status')->default('tersedia')->after('lokasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ruangs', function (Blueprint $table) {
            $table->dropColumn(['fasilitas', 'lokasi', 'status']);
        });
    }
};
