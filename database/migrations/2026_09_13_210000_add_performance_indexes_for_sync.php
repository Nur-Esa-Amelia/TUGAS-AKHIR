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
        Schema::table('pengisian_bukti', function (Blueprint $table) {
            $table->index(['tahun', 'status', 'id_iku'], 'idx_pengisian_tahun_status_iku');
            $table->index('id_user', 'idx_pengisian_id_user');
        });

        Schema::table('file_isi_bukti', function (Blueprint $table) {
            $table->index('id_pengisian_bukti', 'idx_file_isi_pengisian');
        });

        Schema::table('iku_pencapaian', function (Blueprint $table) {
            $table->index(['id_prodi', 'tahun', 'id_iku'], 'idx_pencapaian_prodi_tahun_iku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengisian_bukti', function (Blueprint $table) {
            $table->dropIndex('idx_pengisian_tahun_status_iku');
            $table->dropIndex('idx_pengisian_id_user');
        });

        Schema::table('file_isi_bukti', function (Blueprint $table) {
            $table->dropIndex('idx_file_isi_pengisian');
        });

        Schema::table('iku_pencapaian', function (Blueprint $table) {
            $table->dropIndex('idx_pencapaian_prodi_tahun_iku');
        });
    }
};
