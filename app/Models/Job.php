<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'judul',
        'slug',
        'deskripsi',
        'lokasi',
        'jenis',
        'gaji_min',
        'gaji_max',
        'kuota',
        'batas_lamaran',
        'status',
        'view_count',
        'application_count',
    ];

    protected $casts = [
        'gaji_min' => 'decimal:2',
        'gaji_max' => 'decimal:2',
        'kuota' => 'integer',
        'batas_lamaran' => 'date',
        'view_count' => 'integer',
        'application_count' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function jobSkills(): HasMany
    {
        return $this->hasMany(JobSkill::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'job_skills')
                    ->withPivot('id', 'bobot', 'wajib');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'dibuka';
    }
}
