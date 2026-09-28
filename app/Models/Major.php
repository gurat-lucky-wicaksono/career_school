<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Major extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'kepala_jurusan',
        'keterangan',
    ];

    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'major_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'major_id');
    }

    public function alumni(): HasMany
    {
        return $this->hasMany(Alumni::class, 'major_id');
    }
}
