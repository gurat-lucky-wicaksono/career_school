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
        Schema::create('internship_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained('applications')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();

            $table->decimal('nilai_teknis', 5, 2)->default(0);
            $table->decimal('nilai_disiplin', 5, 2)->default(0);
            $table->decimal('nilai_kerjasama', 5, 2)->default(0);
            $table->decimal('nilai_akhir', 5, 2)->default(0);
            $table->enum('predikat', ['A', 'B', 'C', 'D'])->default('B');

            $table->text('catatan_industri')->nullable();
            $table->text('catatan_guru')->nullable();
            $table->enum('status', ['draft', 'submitted', 'verified'])->default('draft');
            $table->string('nomor_sertifikat', 100)->nullable()->unique();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index('company_id');
            $table->index('student_id');
            $table->index('teacher_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internship_evaluations');
    }
};
