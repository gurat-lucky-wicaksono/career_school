<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Endorsement extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'student_skill_id',
        'teacher_id',
        'catatan',
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function studentSkill(): BelongsTo
    {
        return $this->belongsTo(StudentSkill::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
}
