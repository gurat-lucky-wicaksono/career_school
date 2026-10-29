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
        Schema::table('alumni', function (Blueprint $table) {
            $table->enum('status_bmw', ['bekerja', 'kuliah', 'wirausaha', 'mencari_kerja'])->default('mencari_kerja')->after('tahun_lulus');
            $table->string('nama_instansi', 150)->nullable()->after('status_bmw');
            $table->string('posisi_studi', 100)->nullable()->after('nama_instansi');
            $table->string('pendapatan_bulanan', 50)->nullable()->after('posisi_studi');
            $table->enum('keselarasan', ['sangat_sesuai', 'sesuai', 'kurang_sesuai', 'tidak_sesuai'])->nullable()->after('pendapatan_bulanan');
            $table->unsignedTinyInteger('waktu_tunggu_bulan')->nullable()->after('keselarasan');
            $table->text('testimoni')->nullable()->after('waktu_tunggu_bulan');
            $table->boolean('is_public')->default(true)->after('testimoni');

            $table->index('status_bmw');
            $table->index('keselarasan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alumni', function (Blueprint $table) {
            $table->dropIndex(['status_bmw']);
            $table->dropIndex(['keselarasan']);
            $table->dropColumn([
                'status_bmw',
                'nama_instansi',
                'posisi_studi',
                'pendapatan_bulanan',
                'keselarasan',
                'waktu_tunggu_bulan',
                'testimoni',
                'is_public',
            ]);
        });
    }
};
