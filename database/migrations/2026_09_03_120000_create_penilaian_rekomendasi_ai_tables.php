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
        // Hapus tabel lama jika ada
        Schema::dropIfExists('penilaian_fn_ai');
        Schema::dropIfExists('penilaian_klaim_ai');
        Schema::dropIfExists('detail_penilaian_rekomendasi');
        Schema::dropIfExists('penilaian_rekomendasi_ai');

        // Tabel Penilaian Expert (Header)
        Schema::create('penilaian_rekomendasi_ai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_rekomendasi_ai')->constrained('rekomendasi_ai')->onDelete('cascade');
            $table->string('nama_penilai');
            $table->string('jabatan');
            $table->string('prodi_unit');
            $table->timestamps();
        });

        // Tabel Detail Penilaian Per Klaim
        Schema::create('detail_penilaian_rekomendasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_penilaian')->constrained('penilaian_rekomendasi_ai')->onDelete('cascade');
            $table->foreignId('id_iku')->nullable()->constrained('iku')->onDelete('set null');
            $table->text('klaim');
            $table->float('persentase_fakta', 8, 2)->default(0);
            $table->float('persentase_halusinasi', 8, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_fn_ai');
        Schema::dropIfExists('penilaian_klaim_ai');
        Schema::dropIfExists('detail_penilaian_rekomendasi');
        Schema::dropIfExists('penilaian_rekomendasi_ai');
    }
};


