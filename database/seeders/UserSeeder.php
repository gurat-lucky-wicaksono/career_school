<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Profile;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('nama', 'admin')->first();
        $guruRole = Role::where('nama', 'guru')->first();
        $siswaRole = Role::where('nama', 'siswa')->first();
        $perusahaanRole = Role::where('nama', 'perusahaan')->first();

        // 1. Admin Demo
        if ($adminRole) {
            $admin = User::firstOrCreate(
                ['email' => 'admin@demo.com'],
                [
                    'role_id' => $adminRole->id,
                    'name' => 'Admin BKK SMKN 1',
                    'username' => 'admin_bkk',
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]
            );
            Profile::firstOrCreate(['user_id' => $admin->id], [
                'bio' => 'Administrator Bursa Kerja Khusus (BKK) SMK Negeri 1 Cirebon.',
                'telepon' => '0231-123456',
            ]);
        }

        // 2. Guru Demo
        if ($guruRole) {
            $guru = User::firstOrCreate(
                ['email' => 'guru@demo.com'],
                [
                    'role_id' => $guruRole->id,
                    'name' => 'Bambang Sudarsono, S.Kom',
                    'username' => 'pak_bambang',
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]
            );
            Profile::firstOrCreate(['user_id' => $guru->id], [
                'bio' => 'Guru Kejuruan Pengembangan Perangkat Lunak dan Gim (PPLG) SMKN 1 Cirebon.',
                'telepon' => '081234567890',
            ]);
            Teacher::firstOrCreate(['user_id' => $guru->id], [
                'nip' => '198503152010011002',
                'mata_pelajaran' => 'Pemrograman Web dan Mobile',
                'is_wali_kelas' => true,
            ]);
        }

        // 3. Siswa Demo
        if ($siswaRole) {
            $class = SchoolClass::where('nama', 'XI PPLG 1')->first() ?? SchoolClass::first();
            $siswa = User::firstOrCreate(
                ['email' => 'siswa@demo.com'],
                [
                    'role_id' => $siswaRole->id,
                    'name' => 'Rifqi Pratama',
                    'username' => 'rifqipratama',
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]
            );
            Profile::firstOrCreate(['user_id' => $siswa->id], [
                'bio' => 'Siswa XI PPLG SMKN 1 Cirebon. Tertarik di Web Development (Laravel & Vue/Tailwind).',
                'telepon' => '087712345678',
            ]);
            if ($class) {
                Student::firstOrCreate(['user_id' => $siswa->id], [
                    'nisn' => '0061234567',
                    'class_id' => $class->id,
                    'major_id' => $class->major_id,
                    'angkatan' => 2024,
                    'status' => 'aktif',
                ]);
            }
        }

        // 4. Perusahaan Demo
        if ($perusahaanRole) {
            $perusahaan = User::firstOrCreate(
                ['email' => 'perusahaan@demo.com'],
                [
                    'role_id' => $perusahaanRole->id,
                    'name' => 'PT Telkom Akses Cirebon',
                    'username' => 'telkom_cirebon',
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]
            );
            Profile::firstOrCreate(['user_id' => $perusahaan->id], [
                'bio' => 'Mitra Industri DUDI SMKN 1 Cirebon bidang Teknologi Informasi & Telekomunikasi.',
                'telepon' => '0231-654321',
            ]);
            Company::firstOrCreate(['user_id' => $perusahaan->id], [
                'nama' => 'PT Telkom Akses Cirebon',
                'bidang' => 'Telekomunikasi & Jaringan',
                'alamat' => 'Jl. Pagongan No. 12, Kota Cirebon',
                'kota' => 'Cirebon',
                'situs' => 'https://www.telkomakses.co.id',
                'deskripsi' => 'Penyedia layanan infrastruktur jaringan dan solusi telekomunikasi terkemuka.',
                'status_verifikasi' => 'terverifikasi',
                'verified_at' => now(),
            ]);
        }
    }
}
