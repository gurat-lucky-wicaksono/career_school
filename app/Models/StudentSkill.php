<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentSkill extends Model
{
    use HasFactory;

    public const CREATED_AT = null;

    protected $fillable = [
        'student_id',
        'skill_id',
        'tingkat',
        'tervalidasi',
    ];

    protected $casts = [
        'tingkat' => 'integer',
        'tervalidasi' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    public function endorsements(): HasMany
    {
        return $this->hasMany(Endorsement::class);
    }
}
