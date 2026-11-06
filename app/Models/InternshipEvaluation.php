<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternshipEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'company_id',
        'student_id',
        'teacher_id',
        'nilai_teknis',
        'nilai_disiplin',
        'nilai_kerjasama',
        'nilai_akhir',
        'predikat',
        'catatan_industri',
        'catatan_guru',
        'status',
        'nomor_sertifikat',
        'submitted_at',
        'verified_at',
    ];

    protected $casts = [
        'nilai_teknis' => 'decimal:2',
        'nilai_disiplin' => 'decimal:2',
        'nilai_kerjasama' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('status', 'verified');
    }

    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->where('status', 'submitted');
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    public static function calculateFinalScore(float $teknis, float $disiplin, float $kerjasama): float
    {
        $score = ($teknis * 0.40) + ($disiplin * 0.30) + ($kerjasama * 0.30);
        return round($score, 2);
    }

    public static function determinePredicate(float $score): string
    {
        if ($score >= 85) {
            return 'A';
        }

        if ($score >= 75) {
            return 'B';
        }

        if ($score >= 60) {
            return 'C';
        }

        return 'D';
    }

    public function getPredicateLabelAttribute(): string
    {
        return match ($this->predikat) {
            'A' => 'Amat Baik',
            'B' => 'Baik',
            'C' => 'Cukup',
            'D' => 'Kurang',
            default => 'Belum Ditentukan',
        };
    }

    public function getPredicateBadgeClassAttribute(): string
    {
        return match ($this->predikat) {
            'A' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60',
            'B' => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/40 dark:text-sky-300 dark:border-sky-800/60',
            'C' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60',
            'D' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800/60',
            default => 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'verified' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60',
            'submitted' => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/40 dark:text-indigo-300 dark:border-indigo-800/60',
            'draft' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60',
            default => 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'verified' => 'Terverifikasi Sekolah',
            'submitted' => 'Menunggu Verifikasi Guru',
            'draft' => 'Draf Penilaian',
            default => 'Belum Dievaluasi',
        };
    }

    public static function generateCertificateNumber(int $id, ?string $year = null): string
    {
        $year = $year ?? date('Y');
        $padded = str_pad((string) $id, 4, '0', STR_PAD_LEFT);
        return "SMKN1-PKL/{$year}/{$padded}";
    }
}
