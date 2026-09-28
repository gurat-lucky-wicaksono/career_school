<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'nama',
        'slug',
        'jenis',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
