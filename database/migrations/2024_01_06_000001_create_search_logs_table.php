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
        Schema::create('search_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('kata_kunci', 150);
            $table->string('tipe', 30)->nullable(); // pengguna, lowongan, postingan, tagar
            $table->integer('jumlah_hasil')->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id');
            $table->index('kata_kunci');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_logs');
    }
};
