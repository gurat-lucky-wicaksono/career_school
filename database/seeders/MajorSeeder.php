<?php

namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Seeder;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $majors = [
            [
                'kode' => 'PPLG',
                'nama' => 'Pengembangan Perangkat Lunak dan Gim',
                'kepala_jurusan' => 'Bambang Tri Setiadi, S.Kom.',
                'keterangan' => 'Konsentrasi keahlian rekayasa perangkat lunak, pemrograman web, mobile, dan gim.',
            ],
        ];

        foreach ($majors as $major) {
            Major::firstOrCreate(['kode' => $major['kode']], $major);
        }
    }
}
