<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Hashtag extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'tag',
        'jumlah_pakai',
    ];

    protected $casts = [
        'jumlah_pakai' => 'integer',
    ];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_hashtag')
                    ->withPivot('created_at');
    }
}
