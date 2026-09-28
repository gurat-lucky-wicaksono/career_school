<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'nama' => 'admin',
                'label' => 'Admin Sekolah',
                'keterangan' => 'Akses penuh pengelolaan sistem, data master, moderasi, dan verifikasi',
            ],
            [
                'nama' => 'guru',
                'label' => 'Guru Kejuruan',
                'keterangan' => 'Validasi keahlian siswa, unggah materi, dan pemantauan karier',
            ],
            [
                'nama' => 'siswa',
                'label' => 'Siswa / Peserta Didik',
                'keterangan' => 'Pencari kerja, pemilik portofolio, dan interaksi sosial',
            ],
            [
                'nama' => 'perusahaan',
                'label' => 'Perusahaan Mitra',
                'keterangan' => 'Pemasang lowongan dan peninjau kandidat kerja',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['nama' => $role['nama']], $role);
        }
    }
}
