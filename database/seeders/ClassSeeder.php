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

        if (!$pplg) {
            $pplg = Major::create([
                'kode' => 'PPLG',
                'nama' => 'Pengembangan Perangkat Lunak dan Gim',
                'kepala_jurusan' => 'Bambang Tri Setiadi, S.Kom.',
                'keterangan' => 'Konsentrasi keahlian rekayasa perangkat lunak, pemrograman web, mobile, dan gim.',
            ]);
        }

        $classes = [
            ['major_id' => $pplg->id, 'nama' => 'X PPLG 1', 'tingkat' => 10, 'tahun_ajaran' => '2025/2026'],
            ['major_id' => $pplg->id, 'nama' => 'X PPLG 2', 'tingkat' => 10, 'tahun_ajaran' => '2025/2026'],
            ['major_id' => $pplg->id, 'nama' => 'XI PPLG 1', 'tingkat' => 11, 'tahun_ajaran' => '2025/2026'],
            ['major_id' => $pplg->id, 'nama' => 'XI PPLG 2', 'tingkat' => 11, 'tahun_ajaran' => '2025/2026'],
            ['major_id' => $pplg->id, 'nama' => 'XII PPLG 1', 'tingkat' => 12, 'tahun_ajaran' => '2025/2026'],
            ['major_id' => $pplg->id, 'nama' => 'XII PPLG 2', 'tingkat' => 12, 'tahun_ajaran' => '2025/2026'],
        ];

        foreach ($classes as $item) {
            SchoolClass::firstOrCreate(
                ['nama' => $item['nama'], 'tahun_ajaran' => $item['tahun_ajaran']],
                $item
            );
        }
    }
}
