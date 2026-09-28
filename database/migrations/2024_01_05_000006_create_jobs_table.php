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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('judul', 150);
            $table->string('slug', 170)->unique();
            $table->text('deskripsi');
            $table->string('lokasi', 120)->nullable();
            $table->enum('jenis', ['penuh_waktu', 'paruh_waktu', 'magang', 'proyek'])->default('penuh_waktu');
            $table->decimal('gaji_min', 12, 2)->nullable();
            $table->decimal('gaji_max', 12, 2)->nullable();
            $table->tinyInteger('kuota')->default(1);
            $table->date('batas_lamaran')->nullable();
            $table->enum('status', ['dibuka', 'ditutup', 'selesai'])->default('dibuka');
            $table->integer('view_count')->default(0);
            $table->integer('application_count')->default(0);
            $table->timestamps();

            $table->index('company_id');
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
