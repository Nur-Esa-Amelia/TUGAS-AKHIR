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
        Schema::create('ai_prompts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama/label prompt (misal: "Prompt Standar", "Prompt Ringkas")
            $table->longText('prompt_template'); // Template prompt dengan placeholder {variabel}
            $table->enum('status', ['aktif', 'nonaktif'])->default('nonaktif');
            $table->text('keterangan')->nullable(); // Catatan/keterangan admin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_prompts');
    }
};
