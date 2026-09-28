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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('major_id')->constrained('majors')->cascadeOnDelete();
            $table->foreignId('wali_kelas_id')->nullable()->constrained('teachers')->cascadeOnDelete();
            $table->string('nama', 50);
            $table->tinyInteger('tingkat');
            $table->string('tahun_ajaran', 9);
            $table->timestamps();

            $table->index('major_id');
            $table->index('wali_kelas_id');
            $table->unique(['nama', 'tahun_ajaran']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
