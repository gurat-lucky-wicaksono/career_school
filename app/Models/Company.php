<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'bidang',
        'alamat',
        'kota',
        'situs',
        'logo',
        'deskripsi',
        'status_verifikasi',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    public function isVerified(): bool
    {
        return $this->status_verifikasi === 'terverifikasi';
    }
}
