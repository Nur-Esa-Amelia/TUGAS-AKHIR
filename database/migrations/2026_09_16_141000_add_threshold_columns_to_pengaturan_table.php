<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengaturan', function (Blueprint $table) {
            if (!Schema::hasColumn('pengaturan', 'threshold_tercapai')) {
                $table->decimal('threshold_tercapai', 5, 2)->default(100.00)->after('jml_dosen');
            }
            if (!Schema::hasColumn('pengaturan', 'threshold_perlu_perhatian')) {
                $table->decimal('threshold_perlu_perhatian', 5, 2)->default(60.00)->after('threshold_tercapai');
            }
        });

        // Set default values for any existing records
        DB::table('pengaturan')->whereNull('threshold_tercapai')->update(['threshold_tercapai' => 100.00]);
        DB::table('pengaturan')->whereNull('threshold_perlu_perhatian')->update(['threshold_perlu_perhatian' => 60.00]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('pengaturan', 'threshold_tercapai')) {
                $columns[] = 'threshold_tercapai';
            }
            if (Schema::hasColumn('pengaturan', 'threshold_perlu_perhatian')) {
                $columns[] = 'threshold_perlu_perhatian';
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
