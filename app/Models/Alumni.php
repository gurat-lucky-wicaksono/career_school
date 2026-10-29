<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alumni extends Model
{
    use HasFactory;

    protected $table = 'alumni';

    protected $fillable = [
        'user_id',
        'major_id',
        'tahun_lulus',
        'pekerjaan',
        'perusahaan',
        'status_bmw',
        'nama_instansi',
        'posisi_studi',
        'pendapatan_bulanan',
        'keselarasan',
        'waktu_tunggu_bulan',
        'testimoni',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'tahun_lulus' => 'integer',
        'waktu_tunggu_bulan' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function getStatusBmwLabelAttribute(): string
    {
        return match ($this->status_bmw) {
            'bekerja' => 'Bekerja',
            'kuliah' => 'Melanjutkan Kuliah',
            'wirausaha' => 'Wirausaha / Freelance',
            'mencari_kerja' => 'Mencari Kerja',
            default => 'Belum Ditentukan',
        };
    }

    public function getKeselarasanLabelAttribute(): string
    {
        return match ($this->keselarasan) {
            'sangat_sesuai' => 'Sangat Sesuai',
            'sesuai' => 'Sesuai',
            'kurang_sesuai' => 'Kurang Sesuai',
            'tidak_sesuai' => 'Tidak Sesuai',
            default => 'Belum Ditentukan',
        };
    }
}
