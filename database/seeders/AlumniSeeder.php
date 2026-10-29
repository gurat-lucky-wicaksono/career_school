<?php

namespace Database\Seeders;

use App\Models\Alumni;
use App\Models\Major;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AlumniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $siswaRole = Role::where('nama', 'siswa')->first();
        $major = Major::first() ?? Major::create([
            'kode' => 'PPLG',
            'nama' => 'Pengembangan Perangkat Lunak dan Gim',
            'deskripsi' => 'Konsentrasi keahlian software engineering dan game dev.',
        ]);

        $alumniData = [
            [
                'name' => 'Dimas Maulana Akbar',
                'username' => 'dimas_alumni',
                'email' => 'dimas.alumni@demo.com',
                'tahun_lulus' => 2024,
                'status_bmw' => 'bekerja',
                'nama_instansi' => 'PT Telkom Akses Cirebon',
                'posisi_studi' => 'Junior Frontend Engineer',
                'pendapatan_bulanan' => 'Rp 4.000.000 - Rp 7.000.000',
                'keselarasan' => 'sangat_sesuai',
                'waktu_tunggu_bulan' => 2,
                'testimoni' => 'Pembelajaran kurikulum PPLG di SMKN 1 Cirebon sangat relevan dengan kebutuhan industri software modern. BKK sangat membantu proses rekrutmen saya.',
                'is_public' => true,
            ],
            [
                'name' => 'Annisa Rahmawati',
                'username' => 'annisa_alumni',
                'email' => 'annisa.alumni@demo.com',
                'tahun_lulus' => 2024,
                'status_bmw' => 'kuliah',
                'nama_instansi' => 'Institut Teknologi Bandung',
                'posisi_studi' => 'S1 Teknik Informatika',
                'pendapatan_bulanan' => null,
                'keselarasan' => 'sangat_sesuai',
                'waktu_tunggu_bulan' => 1,
                'testimoni' => 'Fondasi algoritma dan problem solving dari guru-guru kejuruan SMKN 1 Cirebon membuat saya sangat siap bersaing di perkuliahan PTN ternama.',
                'is_public' => true,
            ],
            [
                'name' => 'Bintang Pamungkas',
                'username' => 'bintang_alumni',
                'email' => 'bintang.alumni@demo.com',
                'tahun_lulus' => 2023,
                'status_bmw' => 'wirausaha',
                'nama_instansi' => 'Grage Digital Creative',
                'posisi_studi' => 'Founder & Fullstack Developer',
                'pendapatan_bulanan' => '> Rp 7.000.000',
                'keselarasan' => 'sangat_sesuai',
                'waktu_tunggu_bulan' => 1,
                'testimoni' => 'Mental kemandirian dan proyek nyata selama sekolah di PPLG memberikan saya keberanian untuk membuka agensi digital sendiri bersama rekan seangkatan.',
                'is_public' => true,
            ],
            [
                'name' => 'Siti Nurhaliza',
                'username' => 'siti_alumni',
                'email' => 'siti.alumni@demo.com',
                'tahun_lulus' => 2024,
                'status_bmw' => 'bekerja',
                'nama_instansi' => 'RSUD Gunung Jati Cirebon',
                'posisi_studi' => 'Staff IT & SIMRS Support',
                'pendapatan_bulanan' => 'Rp 2.000.000 - Rp 4.000.000',
                'keselarasan' => 'sesuai',
                'waktu_tunggu_bulan' => 3,
                'testimoni' => 'Keterampilan manajemen basis data dan jaringan yang diajarkan di sekolah sangat terpakai dalam operasional rumah sakit harian.',
                'is_public' => true,
            ],
            [
                'name' => 'Fajar Sidik Permana',
                'username' => 'fajar_alumni',
                'email' => 'fajar.alumni@demo.com',
                'tahun_lulus' => 2025,
                'status_bmw' => 'mencari_kerja',
                'nama_instansi' => null,
                'posisi_studi' => 'Web Developer Enthusiast',
                'pendapatan_bulanan' => null,
                'keselarasan' => null,
                'waktu_tunggu_bulan' => 0,
                'testimoni' => 'Sedang giat mengasah portofolio proyek Laravel dan aktif memantau bursa kerja khusus SMKN 1 Cirebon.',
                'is_public' => true,
            ],
        ];

        foreach ($alumniData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'role_id' => $siswaRole?->id ?? 3,
                    'name' => $data['name'],
                    'username' => $data['username'],
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]
            );

            Alumni::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'major_id' => $major->id,
                    'tahun_lulus' => $data['tahun_lulus'],
                    'status_bmw' => $data['status_bmw'],
                    'nama_instansi' => $data['nama_instansi'],
                    'posisi_studi' => $data['posisi_studi'],
                    'pekerjaan' => $data['posisi_studi'],
                    'perusahaan' => $data['nama_instansi'],
                    'pendapatan_bulanan' => $data['pendapatan_bulanan'],
                    'keselarasan' => $data['keselarasan'],
                    'waktu_tunggu_bulan' => $data['waktu_tunggu_bulan'],
                    'testimoni' => $data['testimoni'],
                    'is_public' => $data['is_public'],
                ]
            );
        }
    }
}
