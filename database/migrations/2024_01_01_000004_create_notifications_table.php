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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('jenis', 40);
            $table->string('judul', 150);
            $table->string('referensi_tipe', 30)->nullable();
            $table->unsignedBigInteger('referensi_id')->nullable();
            $table->boolean('dibaca')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'dibaca']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
