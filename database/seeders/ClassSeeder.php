<?php

namespace Database\Seeders;

use App\Models\Major;
use App\Models\SchoolClass;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    public function run(): void
    {
        $pplg = Major::where('kode', 'PPLG')->first();
        $tjkt = Major::where('kode', 'TJKT')->first();
        $dkv  = Major::where('kode', 'DKV')->first();

        $classes = [
            // PPLG
            ['major_id' => $pplg?->id ?? 1, 'nama' => 'X PPLG 1', 'tingkat' => 10, 'tahun_ajaran' => '2025/2026'],
            ['major_id' => $pplg?->id ?? 1, 'nama' => 'X PPLG 2', 'tingkat' => 10, 'tahun_ajaran' => '2025/2026'],
            ['major_id' => $pplg?->id ?? 1, 'nama' => 'XI PPLG 1', 'tingkat' => 11, 'tahun_ajaran' => '2025/2026'],
            ['major_id' => $pplg?->id ?? 1, 'nama' => 'XI PPLG 2', 'tingkat' => 11, 'tahun_ajaran' => '2025/2026'],
            ['major_id' => $pplg?->id ?? 1, 'nama' => 'XII PPLG 1', 'tingkat' => 12, 'tahun_ajaran' => '2025/2026'],

            // TJKT
            ['major_id' => $tjkt?->id ?? 2, 'nama' => 'X TJKT 1', 'tingkat' => 10, 'tahun_ajaran' => '2025/2026'],
            ['major_id' => $tjkt?->id ?? 2, 'nama' => 'XI TJKT 1', 'tingkat' => 11, 'tahun_ajaran' => '2025/2026'],
            ['major_id' => $tjkt?->id ?? 2, 'nama' => 'XII TJKT 1', 'tingkat' => 12, 'tahun_ajaran' => '2025/2026'],

            // DKV
            ['major_id' => $dkv?->id ?? 3, 'nama' => 'X DKV 1', 'tingkat' => 10, 'tahun_ajaran' => '2025/2026'],
            ['major_id' => $dkv?->id ?? 3, 'nama' => 'XI DKV 1', 'tingkat' => 11, 'tahun_ajaran' => '2025/2026'],
            ['major_id' => $dkv?->id ?? 3, 'nama' => 'XII DKV 1', 'tingkat' => 12, 'tahun_ajaran' => '2025/2026'],
        ];

        foreach ($classes as $item) {
            SchoolClass::firstOrCreate(
                ['nama' => $item['nama'], 'tahun_ajaran' => $item['tahun_ajaran']],
                $item
            );
        }
    }
}
