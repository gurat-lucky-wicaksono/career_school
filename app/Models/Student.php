<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nisn',
        'class_id',
        'major_id',
        'angkatan',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function schoolClass(): BelongsTo
    {
        return $this->class();
    }

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    // Relasi Kelompok E
    public function studentSkills()
    {
        return $this->hasMany(StudentSkill::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'student_skills')
                    ->withPivot('id', 'tingkat', 'tervalidasi');
    }

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
