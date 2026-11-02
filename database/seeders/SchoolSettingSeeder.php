<?php

namespace Database\Seeders;

use App\Models\SchoolSetting;
use Illuminate\Database\Seeder;

class SchoolSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Identitas Lembaga
            [
                'key' => 'school_name',
                'value' => 'SMK Negeri 1 Cirebon',
                'group' => 'identity',
                'type' => 'text',
                'label' => 'Nama Sekolah / Institusi',
                'description' => 'Nama resmi satuan pendidikan yang tertera pada kop dan sertifikat.',
            ],
            [
                'key' => 'school_npsn',
                'value' => '20222159',
                'group' => 'identity',
                'type' => 'text',
                'label' => 'Nomor Pokok Sekolah Nasional (NPSN)',
                'description' => 'Kode pengenal unik sekolah dari Kementerian Pendidikan.',
            ],
            [
                'key' => 'school_address',
                'value' => 'Jl. Perjuangan, Sunyaragi, Kec. Kesambi, Kota Cirebon, Jawa Barat 45131',
                'group' => 'identity',
                'type' => 'textarea',
                'label' => 'Alamat Kampus',
                'description' => 'Domisili fisik kampus utama SMKN 1 Cirebon.',
            ],
            [
                'key' => 'school_phone',
                'value' => '(0231) 480202',
                'group' => 'identity',
                'type' => 'text',
                'label' => 'Nomor Telepon Kantor',
                'description' => 'Kontak telepon resmi humas atau tata usaha sekolah.',
            ],
            [
                'key' => 'school_email',
                'value' => 'info@smkn1-cirebon.sch.id',
                'group' => 'identity',
                'type' => 'email',
                'label' => 'Alamat Email Resmi',
                'description' => 'Email korespondensi dinas dan kemitraan.',
            ],
            [
                'key' => 'school_website',
                'value' => 'https://smkn1-cirebon.sch.id',
                'group' => 'identity',
                'type' => 'url',
                'label' => 'Situs Web Resmi',
                'description' => 'Portal informasi induk sekolah.',
            ],
            [
                'key' => 'principal_name',
                'value' => 'Drs. H. Arifudin, M.Pd.',
                'group' => 'identity',
                'type' => 'text',
                'label' => 'Nama Kepala Sekolah',
                'description' => 'Nama lengkap beserta gelar akademis pimpinan sekolah.',
            ],
            [
                'key' => 'principal_nip',
                'value' => '19680512 199303 1 008',
                'group' => 'identity',
                'type' => 'text',
                'label' => 'NIP Kepala Sekolah',
                'description' => 'Nomor Induk Pegawai Kepala Sekolah.',
            ],

            // Struktur & Izin BKK
            [
                'key' => 'bkk_name',
                'value' => 'Bursa Kerja Khusus (BKK) SMK Negeri 1 Cirebon',
                'group' => 'bkk',
                'type' => 'text',
                'label' => 'Nama Unit BKK',
                'description' => 'Sebutan resmi lembaga bursa kerja khusus sekolah.',
            ],
            [
                'key' => 'bkk_leader_name',
                'value' => 'Bambang Sudarsono, S.Kom.',
                'group' => 'bkk',
                'type' => 'text',
                'label' => 'Nama Ketua BKK',
                'description' => 'Pejabat pengelola operasional BKK sekolah.',
            ],
            [
                'key' => 'bkk_leader_nip',
                'value' => '19820719 200902 1 003',
                'group' => 'bkk',
                'type' => 'text',
                'label' => 'NIP Ketua BKK',
                'description' => 'Nomor Induk Pegawai koordinator BKK.',
            ],
            [
                'key' => 'bkk_sk_number',
                'value' => '560/042/BKK-Disnaker/2023',
                'group' => 'bkk',
                'type' => 'text',
                'label' => 'Nomor Izin Operasional BKK',
                'description' => 'Surat Keputusan registrasi BKK dari Dinas Tenaga Kerja.',
            ],
            [
                'key' => 'bkk_contact_phone',
                'value' => '0812-3456-7890',
                'group' => 'bkk',
                'type' => 'text',
                'label' => 'Hotline / WhatsApp BKK',
                'description' => 'Kontak pesan cepat untuk komunikasi mitra industri.',
            ],

            // Tahun Akademik & Aturan Sistem
            [
                'key' => 'active_academic_year',
                'value' => '2025/2026',
                'group' => 'academic',
                'type' => 'text',
                'label' => 'Tahun Ajaran Aktif',
                'description' => 'Periode kalender pendidikan yang sedang berjalan.',
            ],
            [
                'key' => 'active_semester',
                'value' => 'Ganjil',
                'group' => 'academic',
                'type' => 'select',
                'label' => 'Semester Aktif',
                'description' => 'Semester akademik berjalan (Ganjil atau Genap).',
            ],
            [
                'key' => 'max_active_applications_per_student',
                'value' => '3',
                'group' => 'academic',
                'type' => 'number',
                'label' => 'Maksimal Lamaran Aktif per Siswa',
                'description' => 'Batas jumlah lamaran yang belum diputuskan (tertunda/ditinjau/wawancara) secara simultan.',
            ],
            [
                'key' => 'allow_student_registration',
                'value' => '1',
                'group' => 'academic',
                'type' => 'boolean',
                'label' => 'Izinkan Registrasi Mandiri Siswa',
                'description' => 'Membuka atau menutup formulir pendaftaran akun siswa baru di halaman registrasi.',
            ],
        ];

        foreach ($settings as $setting) {
            SchoolSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        SchoolSetting::flushCache();
    }
}
