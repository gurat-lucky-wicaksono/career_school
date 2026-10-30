<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'jenis',
        'judul',
        'referensi_tipe',
        'referensi_id',
        'dibaca',
        'created_at',
    ];

    protected $casts = [
        'dibaca' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('dibaca', false);
    }

    public function scopeRecent(Builder $query, int $limit = 8): Builder
    {
        return $query->latest('created_at')->take($limit);
    }

    public function getUrlAttribute(): string
    {
        $user = auth()->user() ?? $this->user;
        $role = strtolower($user?->role?->nama ?? '');

        return match ($this->jenis) {
            'lamaran' => match ($role) {
                'perusahaan' => $this->referensi_id ? route('company.applications.show', $this->referensi_id) : route('company.applications.index'),
                'siswa' => $this->referensi_id ? route('student.applications.show', $this->referensi_id) : route('student.applications.index'),
                default => route('notifications.index'),
            },
            'keahlian' => match ($role) {
                'guru' => route('teacher.skills.index'),
                'siswa' => route('student.profile'),
                default => route('notifications.index'),
            },
            'komunitas' => route('feed.index'),
            'kemitraan' => route('admin.companies.verification'),
            'lowongan' => $role === 'siswa' ? route('student.jobs.index') : route('admin.jobs.index'),
            'tracer_study' => route('admin.tracer.index'),
            default => route('notifications.index'),
        };
    }

    public function getIconAttribute(): string
    {
        return match ($this->jenis) {
            'lamaran' => 'briefcase',
            'keahlian' => 'award',
            'komunitas' => 'message-square',
            'kemitraan' => 'building-2',
            'lowongan' => 'compass',
            'tracer_study' => 'graduation-cap',
            default => 'bell',
        };
    }

    public function getJenisBadgeClassAttribute(): string
    {
        return match ($this->jenis) {
            'lamaran' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
            'keahlian' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
            'komunitas' => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
            'kemitraan' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20',
            default => 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20',
        };
    }
}
