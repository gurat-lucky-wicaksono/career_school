<?php

namespace Database\Seeders;

use App\Models\SchoolProfile;
use Illuminate\Database\Seeder;

class SchoolProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SchoolProfile::firstOrCreate(
            ['id' => 1],
            [
                'nama_sekolah' => 'SMKN 1 Cirebon',
                'alamat' => 'Jl. Perjuangan, Sunyaragi, Kec. Kesambi, Kota Cirebon, Jawa Barat 45132',
                'telepon' => '0231-480202',
                'email' => 'info@smkn1-cirebon.sch.id',
                'visi' => 'Menjadi SMK Unggul Berkarakter, Berdaya Saing Global, dan Berwawasan Lingkungan.',
                'misi' => 'Membentuk tamatan yang berakhlak mulia, kompeten, berjiwa wirausaha, dan siap bersaing di dunia industri global.',
                'sejarah' => 'SMKN 1 Cirebon adalah salah satu sekolah menengah kejuruan terkemuka di Kota Cirebon yang berfokus pada teknologi rekayasa dan informasi.',
                'logo' => null,
            ]
        );
    }
}
