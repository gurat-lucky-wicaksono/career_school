<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Company;
use App\Models\Job;
use App\Models\Major;
use App\Models\Profile;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Skill;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('nama', 'admin')->first();
        $guruRole = Role::where('nama', 'guru')->first();
        $siswaRole = Role::where('nama', 'siswa')->first();
        $perusahaanRole = Role::where('nama', 'perusahaan')->first();

        $defaultPassword = Hash::make('password');

        // Pastikan Jurusan PPLG Ada
        $majorPPLG = Major::firstOrCreate(
            ['kode' => 'PPLG'],
            [
                'nama' => 'Pengembangan Perangkat Lunak dan Gim',
                'kepala_jurusan' => 'Bambang Tri Setiadi, S.Kom.',
                'keterangan' => 'Konsentrasi keahlian rekayasa perangkat lunak, pemrograman web, mobile, dan gim.',
            ]
        );

        // Kelas PPLG
        $classes = [
            'X PPLG 1' => SchoolClass::firstOrCreate(['nama' => 'X PPLG 1', 'tahun_ajaran' => '2025/2026'], ['major_id' => $majorPPLG->id, 'tingkat' => 10, 'tahun_ajaran' => '2025/2026']),
            'X PPLG 2' => SchoolClass::firstOrCreate(['nama' => 'X PPLG 2', 'tahun_ajaran' => '2025/2026'], ['major_id' => $majorPPLG->id, 'tingkat' => 10, 'tahun_ajaran' => '2025/2026']),
            'XI PPLG 1' => SchoolClass::firstOrCreate(['nama' => 'XI PPLG 1', 'tahun_ajaran' => '2025/2026'], ['major_id' => $majorPPLG->id, 'tingkat' => 11, 'tahun_ajaran' => '2025/2026']),
            'XI PPLG 2' => SchoolClass::firstOrCreate(['nama' => 'XI PPLG 2', 'tahun_ajaran' => '2025/2026'], ['major_id' => $majorPPLG->id, 'tingkat' => 11, 'tahun_ajaran' => '2025/2026']),
            'XII PPLG 1' => SchoolClass::firstOrCreate(['nama' => 'XII PPLG 1', 'tahun_ajaran' => '2025/2026'], ['major_id' => $majorPPLG->id, 'tingkat' => 12, 'tahun_ajaran' => '2025/2026']),
            'XII PPLG 2' => SchoolClass::firstOrCreate(['nama' => 'XII PPLG 2', 'tahun_ajaran' => '2025/2026'], ['major_id' => $majorPPLG->id, 'tingkat' => 12, 'tahun_ajaran' => '2025/2026']),
        ];

        // Skills Map
        $skillMap = Skill::pluck('id', 'nama')->toArray();

        // ==========================================
        // 1. ADMIN SEKOLAH (Akun Demo Utama)
        // ==========================================
        $admin = User::updateOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'role_id' => $adminRole->id,
                'name' => 'Administrator BKK SMKN 1',
                'username' => 'admin_bkk',
                'password' => $defaultPassword,
                'is_active' => true,
            ]
        );
        Profile::updateOrCreate(['user_id' => $admin->id], [
            'bio' => 'Administrator Pengelola Bursa Kerja Khusus (BKK) dan Sistem Karir Kemitraan SMKN 1 Cirebon.',
            'telepon' => '0231-201456',
            'kota' => 'Kota Cirebon',
            'jenis_kelamin' => 'L',
        ]);

        // ==========================================
        // 2. GURU KEJURUAN PPLG
        // ==========================================
        $teachersData = [
            [
                'name' => 'Bambang Sudarsono, S.Kom.',
                'email' => 'guru@demo.com', // Akun demo utama guru
                'username' => 'pak_bambang',
                'nip' => '198503152010011002',
                'mapel' => 'Pemrograman Web dan Mobile',
                'wali_kelas' => true,
                'telepon' => '081222334455',
                'jenis_kelamin' => 'L',
                'bio' => 'Guru Pengampu Pemrograman Web (Laravel) dan Wali Kelas XI PPLG 1 SMKN 1 Cirebon.',
            ],
            [
                'name' => 'Siti Rahmawati, M.Kom.',
                'email' => 'siti.rahmawati@smkn1cirebon.sch.id',
                'username' => 'bu_siti',
                'nip' => '198707192012022003',
                'mapel' => 'Rekayasa Perangkat Lunak & Basis Data',
                'wali_kelas' => true,
                'telepon' => '081333445566',
                'jenis_kelamin' => 'P',
                'bio' => 'Guru Basis Data Relasional, MySQL, dan Wali Kelas XII PPLG 1.',
            ],
            [
                'name' => 'Ahmad Fauzi, S.T.',
                'email' => 'ahmad.fauzi@smkn1cirebon.sch.id',
                'username' => 'pak_ahmad',
                'nip' => '199002102015031004',
                'mapel' => 'Dasar-Dasar Pengembangan Perangkat Lunak',
                'wali_kelas' => true,
                'telepon' => '081444556677',
                'jenis_kelamin' => 'L',
                'bio' => 'Guru Pengampu Algoritma & Logika Pemrograman. Wali Kelas X PPLG 1.',
            ],
            [
                'name' => 'Dewi Lestari, S.Pd., M.T.',
                'email' => 'dewi.lestari@smkn1cirebon.sch.id',
                'username' => 'bu_dewi',
                'nip' => '198609252011012005',
                'mapel' => 'Pemrograman Berorientasi Objek (OOP Java)',
                'wali_kelas' => true,
                'telepon' => '081555667788',
                'jenis_kelamin' => 'P',
                'bio' => 'Guru Pengampu OOP Java & Framework Backend. Wali Kelas XI PPLG 2.',
            ],
            [
                'name' => 'Hendra Kusuma, S.Kom.',
                'email' => 'hendra.kusuma@smkn1cirebon.sch.id',
                'username' => 'pak_hendra',
                'nip' => '199205142018021006',
                'mapel' => 'Pemodelan Perangkat Lunak & UI/UX Design',
                'wali_kelas' => true,
                'telepon' => '081666778899',
                'jenis_kelamin' => 'L',
                'bio' => 'Guru Pengampu Desain UI/UX Figma dan Riset Pengguna. Wali Kelas X PPLG 2.',
            ],
            [
                'name' => 'Nurul Hidayah, S.Kom.',
                'email' => 'nurul.hidayah@smkn1cirebon.sch.id',
                'username' => 'bu_nurul',
                'nip' => '199111082016042007',
                'mapel' => 'Cloud Computing & Git Version Control',
                'wali_kelas' => true,
                'telepon' => '081777889900',
                'jenis_kelamin' => 'P',
                'bio' => 'Guru Pengampu Version Control Git, API Services, dan Wali Kelas XII PPLG 2.',
            ],
        ];

        foreach ($teachersData as $tData) {
            $teacherUser = User::updateOrCreate(
                ['email' => $tData['email']],
                [
                    'role_id' => $guruRole->id,
                    'name' => $tData['name'],
                    'username' => $tData['username'],
                    'password' => $defaultPassword,
                    'is_active' => true,
                ]
            );

            Profile::updateOrCreate(['user_id' => $teacherUser->id], [
                'bio' => $tData['bio'],
                'telepon' => $tData['telepon'],
                'kota' => 'Kota Cirebon',
                'jenis_kelamin' => $tData['jenis_kelamin'],
            ]);

            Teacher::updateOrCreate(
                ['user_id' => $teacherUser->id],
                [
                    'nip' => $tData['nip'],
                    'mata_pelajaran' => $tData['mapel'],
                    'is_wali_kelas' => $tData['wali_kelas'],
                ]
            );
        }

        // ==========================================
        // 3. SISWA PPLG (Nama Realistis Laki-laki & Perempuan)
        // ==========================================
        $studentsData = [
            // XI PPLG 1 (Kelas Rifqi)
            [
                'name' => 'Rifqi Pratama',
                'email' => 'siswa@demo.com', // Akun demo utama siswa
                'username' => 'rifqipratama',
                'nisn' => '0061234567',
                'class' => 'XI PPLG 1',
                'angkatan' => 2024,
                'jenis_kelamin' => 'L',
                'bio' => 'Siswa XI PPLG SMKN 1 Cirebon. Fokus mendalami Web Development (Laravel, Tailwind CSS, REST API).',
                'skills' => ['Laravel' => 4, 'PHP' => 4, 'Tailwind CSS' => 4, 'MySQL' => 3, 'Git & GitHub' => 4],
            ],
            [
                'name' => 'Salsabila Putri Maharani',
                'email' => 'salsabila.putri@smkn1cirebon.sch.id',
                'username' => 'salsabilaputri',
                'nisn' => '0061234568',
                'class' => 'XI PPLG 1',
                'angkatan' => 2024,
                'jenis_kelamin' => 'P',
                'bio' => 'Penggemar antarmuka web interaktif, Tailwind CSS, Alpine.js, dan perancangan UI/UX di Figma.',
                'skills' => ['UI/UX Design' => 4, 'Figma' => 5, 'Tailwind CSS' => 4, 'JavaScript' => 3],
            ],
            [
                'name' => 'Arya Nugraha',
                'email' => 'arya.nugraha@smkn1cirebon.sch.id',
                'username' => 'aryanugraha',
                'nisn' => '0061234569',
                'class' => 'XI PPLG 1',
                'angkatan' => 2024,
                'jenis_kelamin' => 'L',
                'bio' => 'Tertarik pada backend engineering, query optimization database MySQL, dan REST API arsitektur.',
                'skills' => ['PHP' => 4, 'MySQL' => 4, 'REST API' => 3, 'Git & GitHub' => 3],
            ],
            [
                'name' => 'Muhammad Fadhil Pratama',
                'email' => 'fadhil.pratama@smkn1cirebon.sch.id',
                'username' => 'fadhilpratama',
                'nisn' => '0061234570',
                'class' => 'XI PPLG 1',
                'angkatan' => 2024,
                'jenis_kelamin' => 'L',
                'bio' => 'Pengembang frontend dan aplikasi mobile. Suka bereksplorasi dengan teknologi web modern.',
                'skills' => ['JavaScript' => 4, 'Tailwind CSS' => 4, 'Alpine.js' => 3],
            ],

            // XI PPLG 2
            [
                'name' => 'Nabila Zahra Ayundha',
                'email' => 'nabila.zahra@smkn1cirebon.sch.id',
                'username' => 'nabilazahra',
                'nisn' => '0062234571',
                'class' => 'XI PPLG 2',
                'angkatan' => 2024,
                'jenis_kelamin' => 'P',
                'bio' => 'Tertarik pada product design, user research, wireframing, dan visual branding produk digital.',
                'skills' => ['UI/UX Design' => 5, 'Figma' => 5, 'Tailwind CSS' => 3],
            ],
            [
                'name' => 'Dimas Aditya Ramadhan',
                'email' => 'dimas.aditya@smkn1cirebon.sch.id',
                'username' => 'dimasaditya',
                'nisn' => '0062234572',
                'class' => 'XI PPLG 2',
                'angkatan' => 2024,
                'jenis_kelamin' => 'L',
                'bio' => 'Mempelajari fullstack web development Laravel dan automasi deployment sederhana di server Linux.',
                'skills' => ['Laravel' => 3, 'PHP' => 3, 'MySQL' => 3, 'Linux & Server' => 3],
            ],
            [
                'name' => 'Annisa Fitriani',
                'email' => 'annisa.fitriani@smkn1cirebon.sch.id',
                'username' => 'annisafitriani',
                'nisn' => '0062234573',
                'class' => 'XI PPLG 2',
                'angkatan' => 2024,
                'jenis_kelamin' => 'P',
                'bio' => 'Siswa PPLG dengan ketertarikan di pengujian perangkat lunak (QA), dokumentasi API, dan UI testing.',
                'skills' => ['PHP' => 3, 'REST API' => 3, 'Git & GitHub' => 4],
            ],
            [
                'name' => 'Reza Kurniawan',
                'email' => 'reza.kurniawan@smkn1cirebon.sch.id',
                'username' => 'rezakurniawan',
                'nisn' => '0062234574',
                'class' => 'XI PPLG 2',
                'angkatan' => 2024,
                'jenis_kelamin' => 'L',
                'bio' => 'Hobi ngoding backend PHP dan membuat automasi bot serta integrasi payment gateway.',
                'skills' => ['PHP' => 4, 'Laravel' => 3, 'MySQL' => 4],
            ],

            // XII PPLG 1 (Siap Lulus / PKL Lanjutan)
            [
                'name' => 'Bagas Prasetyo',
                'email' => 'bagas.prasetyo@smkn1cirebon.sch.id',
                'username' => 'bagasprasetyo',
                'nisn' => '0051234575',
                'class' => 'XII PPLG 1',
                'angkatan' => 2023,
                'jenis_kelamin' => 'L',
                'bio' => 'Calon lulusan XII PPLG 1 dengan pengalaman membangun sistem informasi inventaris dan POS berbasis Laravel.',
                'skills' => ['Laravel' => 5, 'PHP' => 5, 'MySQL' => 4, 'Git & GitHub' => 4, 'Tailwind CSS' => 4],
            ],
            [
                'name' => 'Dinda Kirana Permata',
                'email' => 'dinda.kirana@smkn1cirebon.sch.id',
                'username' => 'dindakirana',
                'nisn' => '0051234576',
                'class' => 'XII PPLG 1',
                'angkatan' => 2023,
                'jenis_kelamin' => 'P',
                'bio' => 'Fokus pada frontend modern dan UI/UX design. Berpengalaman mengerjakan proyek portofolio klien nyata.',
                'skills' => ['Figma' => 5, 'UI/UX Design' => 5, 'Tailwind CSS' => 5, 'JavaScript' => 4],
            ],
            [
                'name' => 'Farhan Maulana',
                'email' => 'farhan.maulana@smkn1cirebon.sch.id',
                'username' => 'farhanmaulana',
                'nisn' => '0051234577',
                'class' => 'XII PPLG 1',
                'angkatan' => 2023,
                'jenis_kelamin' => 'L',
                'bio' => 'Backend developer dengan penguasaan REST API, integrasi Microservices sederhana, dan database MySQL.',
                'skills' => ['PHP' => 5, 'Laravel' => 4, 'REST API' => 5, 'MySQL' => 4],
            ],

            // XII PPLG 2
            [
                'name' => 'Tiara Anggraini',
                'email' => 'tiara.anggraini@smkn1cirebon.sch.id',
                'username' => 'tiaraanggraini',
                'nisn' => '0052234578',
                'class' => 'XII PPLG 2',
                'angkatan' => 2023,
                'jenis_kelamin' => 'P',
                'bio' => 'Desainer UI/UX dan Mobile Frontend developer. Suka merancang antarmuka aplikasi yang intuitif.',
                'skills' => ['Figma' => 5, 'UI/UX Design' => 4, 'Tailwind CSS' => 4],
            ],
            [
                'name' => 'Ilham Rizky Fauzan',
                'email' => 'ilham.rizky@smkn1cirebon.sch.id',
                'username' => 'ilhamrizky',
                'nisn' => '0052234579',
                'class' => 'XII PPLG 2',
                'angkatan' => 2023,
                'jenis_kelamin' => 'L',
                'bio' => 'Siswa XII PPLG dengan minat tinggi pada arsitektur sistem informasi dan keamanan aplikasi web.',
                'skills' => ['Laravel' => 4, 'PHP' => 4, 'Linux & Server' => 4, 'MySQL' => 4],
            ],
            [
                'name' => 'Gita Cantika Dewi',
                'email' => 'gita.cantika@smkn1cirebon.sch.id',
                'username' => 'gitacantika',
                'nisn' => '0052234580',
                'class' => 'XII PPLG 2',
                'angkatan' => 2023,
                'jenis_kelamin' => 'P',
                'bio' => 'Kreatif dalam pembuatan prototipe aplikasi dan pengujian interaktivitas pengguna berbasis data.',
                'skills' => ['Figma' => 4, 'UI/UX Design' => 4, 'Tailwind CSS' => 3],
            ],

            // X PPLG 1 (Tingkat Dasar)
            [
                'name' => 'Alif Firmansyah',
                'email' => 'alif.firmansyah@smkn1cirebon.sch.id',
                'username' => 'aliffirmansyah',
                'nisn' => '0071234581',
                'class' => 'X PPLG 1',
                'angkatan' => 2025,
                'jenis_kelamin' => 'L',
                'bio' => 'Siswa baru X PPLG yang antusias mempelajari logika dasar pemrograman, algoritma, dan dasar web HTML/CSS.',
                'skills' => ['PHP' => 2, 'Tailwind CSS' => 2],
            ],
            [
                'name' => 'Syifa Aulia Rahmah',
                'email' => 'syifa.aulia@smkn1cirebon.sch.id',
                'username' => 'syifaaulia',
                'nisn' => '0071234582',
                'class' => 'X PPLG 1',
                'angkatan' => 2025,
                'jenis_kelamin' => 'P',
                'bio' => 'Tertarik pada pembuatan desain grafis dan antarmuka web statis menggunakan Figma.',
                'skills' => ['Figma' => 3, 'UI/UX Design' => 2],
            ],
            [
                'name' => 'Zaky Mubarok',
                'email' => 'zaky.mubarok@smkn1cirebon.sch.id',
                'username' => 'zakymubarok',
                'nisn' => '0071234583',
                'class' => 'X PPLG 1',
                'angkatan' => 2025,
                'jenis_kelamin' => 'L',
                'bio' => 'Mempelajari dasar Git, Linux, dan logika pemrograman terstruktur.',
                'skills' => ['Git & GitHub' => 2, 'PHP' => 2],
            ],

            // X PPLG 2
            [
                'name' => 'Maya Indah Sari',
                'email' => 'maya.indah@smkn1cirebon.sch.id',
                'username' => 'mayaindah',
                'nisn' => '0072234584',
                'class' => 'X PPLG 2',
                'angkatan' => 2025,
                'jenis_kelamin' => 'P',
                'bio' => 'Siswa X PPLG 2 yang suka mendesain tata letak situs web dan mempelajari CSS modern.',
                'skills' => ['Tailwind CSS' => 2, 'Figma' => 3],
            ],
            [
                'name' => 'Reihan Saputra',
                'email' => 'reihan.saputra@smkn1cirebon.sch.id',
                'username' => 'reihansaputra',
                'nisn' => '0072234585',
                'class' => 'X PPLG 2',
                'angkatan' => 2025,
                'jenis_kelamin' => 'L',
                'bio' => 'Menyukai komputer dan game. Ingin menjadi Game Developer atau Web Engineer handal.',
                'skills' => ['PHP' => 2, 'MySQL' => 2],
            ],
            [
                'name' => 'Putri Amanda',
                'email' => 'putri.amanda@smkn1cirebon.sch.id',
                'username' => 'putriamanda',
                'nisn' => '0072234586',
                'class' => 'X PPLG 2',
                'angkatan' => 2025,
                'jenis_kelamin' => 'P',
                'bio' => 'Semangat belajar logika komputasi dan dasar pembuatan aplikasi komputer berbasis web.',
                'skills' => ['Figma' => 2, 'PHP' => 2],
            ],
        ];

        foreach ($studentsData as $sData) {
            $studentUser = User::updateOrCreate(
                ['email' => $sData['email']],
                [
                    'role_id' => $siswaRole->id,
                    'name' => $sData['name'],
                    'username' => $sData['username'],
                    'password' => $defaultPassword,
                    'is_active' => true,
                ]
            );

            Profile::updateOrCreate(['user_id' => $studentUser->id], [
                'bio' => $sData['bio'],
                'telepon' => '0877' . rand(10000000, 99999999),
                'kota' => 'Kota Cirebon',
                'jenis_kelamin' => $sData['jenis_kelamin'],
            ]);

            $targetClass = $classes[$sData['class']] ?? $classes['XI PPLG 1'];

            $student = Student::updateOrCreate(
                ['user_id' => $studentUser->id],
                [
                    'nisn' => $sData['nisn'],
                    'class_id' => $targetClass->id,
                    'major_id' => $majorPPLG->id,
                    'angkatan' => $sData['angkatan'],
                    'status' => 'aktif',
                ]
            );

            // Seed student skills
            if (!empty($sData['skills'])) {
                $skillsToSync = [];
                foreach ($sData['skills'] as $skillName => $level) {
                    if (isset($skillMap[$skillName])) {
                        $skillsToSync[$skillMap[$skillName]] = [
                            'tingkat' => $level,
                            'tervalidasi' => true,
                        ];
                    }
                }
                $student->skills()->sync($skillsToSync);
            }
        }

        // ==========================================
        // 4. MITRA INDUSTRI DUDI (Perusahaan Nyata & Relevan PPLG)
        // ==========================================
        $companiesData = [
            [
                'name' => 'PT Telkom Akses Cirebon',
                'email' => 'perusahaan@demo.com', // Akun demo utama perusahaan
                'username' => 'telkom_cirebon',
                'bidang' => 'Telekomunikasi & Jaringan Digital',
                'kota' => 'Kota Cirebon',
                'alamat' => 'Jl. Pagongan No. 11, Pekalangan, Kejaksan, Kota Cirebon',
                'deskripsi' => 'Anak perusahaan PT Telkom Indonesia yang bergerak di bidang konstruksi infrastruktur jaringan akses telekomunikasi digital dan pemeliharaan sistem terpadu.',
                'situs' => 'https://telkomakses.co.id',
                'status' => 'terverifikasi',
                'telepon' => '0231-209876',
            ],
            [
                'name' => 'PT Solusi Digital Nusantara (SolusiDev)',
                'email' => 'hrd@solusidev.co.id',
                'username' => 'solusidev_cirebon',
                'bidang' => 'Software House & Web Application',
                'kota' => 'Kota Cirebon',
                'alamat' => 'Jl. Pemuda No. 45, Sunyaragi, Kesambi, Kota Cirebon',
                'deskripsi' => 'Perusahaan pengembang perangkat lunak kustom, aplikasi SaaS enterprise berbasis Laravel/Vue, dan solusi transformasi digital UMKM serta korporasi.',
                'situs' => 'https://solusidev.co.id',
                'status' => 'terverifikasi',
                'telepon' => '0231-882244',
            ],
            [
                'name' => 'PT Barata Cipta Teknologi',
                'email' => 'karir@baratatech.id',
                'username' => 'barata_tech',
                'bidang' => 'IT Consultant & Mobile Solutions',
                'kota' => 'Cirebon',
                'alamat' => 'Jl. Kartini No. 28, Sukapura, Kejaksan, Kota Cirebon',
                'deskripsi' => 'Konsultan teknologi informasi independen yang fokus pada pengembangan aplikasi mobile multi-platform, integrasi cloud, dan modernisasi sistem.',
                'situs' => 'https://baratatech.id',
                'status' => 'terverifikasi',
                'telepon' => '0231-773355',
            ],
            [
                'name' => 'Bank BJB Kantor Cabang Cirebon',
                'email' => 'rekrutmen.bjb@bankbjb.co.id',
                'username' => 'bank_bjb_cirebon',
                'bidang' => 'Perbankan & IT FinTech Support',
                'kota' => 'Kota Cirebon',
                'alamat' => 'Jl. Siliwangi No. 102, Kebonbaru, Kejaksan, Kota Cirebon',
                'deskripsi' => 'Badan usaha perbankan daerah Jawa Barat yang membutuhkan tenaga muda talenta vokasi untuk dukungan sistem operasional, data administration, dan IT support.',
                'situs' => 'https://bankbjb.co.id',
                'status' => 'terverifikasi',
                'telepon' => '0231-205566',
            ],
            [
                'name' => 'PT Kreasi Media Studio',
                'email' => 'halo@kreasimedia.id',
                'username' => 'kreasimedia_studio',
                'bidang' => 'Studio UI/UX & Game Development',
                'kota' => 'Cirebon',
                'alamat' => 'Jl. Tuparev No. 88, Kedawung, Cirebon',
                'deskripsi' => 'Studio kreatif rintisan yang memproduksi aset digital, desain antarmuka aplikasi interaktif, dan purwarupa video game edukasi 2D.',
                'situs' => 'https://kreasimedia.id',
                'status' => 'menunggu', // Untuk keperluan pengujian verifikasi admin
                'telepon' => '0231-449911',
            ],
        ];

        $companies = [];

        foreach ($companiesData as $cData) {
            $companyUser = User::updateOrCreate(
                ['email' => $cData['email']],
                [
                    'role_id' => $perusahaanRole->id,
                    'name' => $cData['name'],
                    'username' => $cData['username'],
                    'password' => $defaultPassword,
                    'is_active' => true,
                ]
            );

            Profile::updateOrCreate(['user_id' => $companyUser->id], [
                'bio' => $cData['deskripsi'],
                'telepon' => $cData['telepon'],
                'kota' => $cData['kota'],
            ]);

            $company = Company::updateOrCreate(
                ['user_id' => $companyUser->id],
                [
                    'nama' => $cData['name'],
                    'bidang' => $cData['bidang'],
                    'kota' => $cData['kota'],
                    'alamat' => $cData['alamat'],
                    'deskripsi' => $cData['deskripsi'],
                    'situs' => $cData['situs'],
                    'status_verifikasi' => $cData['status'],
                ]
            );

            $companies[$cData['username']] = $company;
        }

        // ==========================================
        // 5. LOWONGAN PEKERJAAN & MAGANG VOKASI REALISTIS
        // ==========================================
        $jobsData = [
            [
                'company_key' => 'telkom_cirebon',
                'judul' => 'Praktik Kerja Lapangan (PKL) Web Application Monitoring',
                'jenis' => 'magang',
                'kuota' => 4,
                'lokasi' => 'Kota Cirebon (On-site)',
                'gaji_min' => 1200000,
                'gaji_max' => 1800000,
                'batas_lamaran' => now()->addDays(45)->toDateString(),
                'status' => 'dibuka',
                'deskripsi' => "Membuka kesempatan bagi siswa PPLG SMKN 1 Cirebon untuk mengikuti program magang PKL di Divisi IT & Jaringan PT Telkom Akses Cirebon.\n\nTugas & Tanggung Jawab:\n1. Membantu pemeliharaan dashboard monitoring infrastruktur jaringan berbasis web internal.\n2. Mengolah dan memvalidasi log database performa jaringan harian.\n3. Melakukan dokumentasi perbaikan sistem dan pembuatan laporan progres mingguan.\n\nFasilitas:\n- Uang saku bulanan dan sertifikat resmi industri berskala nasional.\n- Pembimbing teknis langsung dari engineer Telkom Akses.",
                'skills' => [
                    'Laravel' => ['bobot' => 5, 'wajib' => true],
                    'PHP' => ['bobot' => 4, 'wajib' => true],
                    'MySQL' => ['bobot' => 4, 'wajib' => true],
                    'Git & GitHub' => ['bobot' => 3, 'wajib' => false],
                ],
            ],
            [
                'company_key' => 'solusidev_cirebon',
                'judul' => 'Junior Web Developer (Laravel & MySQL)',
                'jenis' => 'penuh_waktu',
                'kuota' => 2,
                'lokasi' => 'Kota Cirebon (Hybrid)',
                'gaji_min' => 3500000,
                'gaji_max' => 4800000,
                'batas_lamaran' => now()->addDays(30)->toDateString(),
                'status' => 'dibuka',
                'deskripsi' => "Kami mencari lulusan SMK PPLG berbakat untuk bergabung sebagai Junior Backend Web Developer di tim inti pengembang sistem ERP dan SaaS SolusiDev.\n\nKualifikasi:\n1. Memahami konsep OOP pada PHP dan framework Laravel.\n2. Mampu membuat query MySQL yang efisien dan mendesain skema relasional.\n3. Mampu membangun RESTful API untuk dikonsumsi tim frontend/mobile.\n\nBenefit:\n- Gaji pokok kompetitif dan bonus performa proyek.\n- Kesempatan mentoring karir teknologi berkelanjutan.",
                'skills' => [
                    'Laravel' => ['bobot' => 5, 'wajib' => true],
                    'PHP' => ['bobot' => 5, 'wajib' => true],
                    'MySQL' => ['bobot' => 4, 'wajib' => true],
                    'REST API' => ['bobot' => 4, 'wajib' => false],
                    'Git & GitHub' => ['bobot' => 3, 'wajib' => false],
                ],
            ],
            [
                'company_key' => 'barata_tech',
                'judul' => 'Frontend Web Engineer (Tailwind CSS & JavaScript)',
                'jenis' => 'penuh_waktu',
                'kuota' => 2,
                'lokasi' => 'Kota Cirebon (On-site)',
                'gaji_min' => 3800000,
                'gaji_max' => 5000000,
                'batas_lamaran' => now()->addDays(40)->toDateString(),
                'status' => 'dibuka',
                'deskripsi' => "PT Barata Cipta Teknologi membuka lowongan Junior Frontend Web Engineer untuk menangani slicing desain UI menjadi tampilan web modern yang responsif dan interaktif.\n\nKualifikasi:\n1. Menguasai HTML5, CSS3, dan framework utility Tailwind CSS.\n2. Mampu mengimplementasikan desain Figma pixel-perfect.\n3. Memahami interaktivitas JavaScript dasar / Alpine.js / Vue.\n4. Terbiasa menggunakan Git dalam kerja tim.",
                'skills' => [
                    'Tailwind CSS' => ['bobot' => 5, 'wajib' => true],
                    'JavaScript' => ['bobot' => 4, 'wajib' => true],
                    'Figma' => ['bobot' => 4, 'wajib' => false],
                    'Alpine.js' => ['bobot' => 3, 'wajib' => false],
                ],
            ],
            [
                'company_key' => 'solusidev_cirebon',
                'judul' => 'UI/UX Designer & Prototyper (Figma)',
                'jenis' => 'paruh_waktu',
                'kuota' => 1,
                'lokasi' => 'Kota Cirebon (Remote / WFH)',
                'gaji_min' => 2000000,
                'gaji_max' => 3000000,
                'batas_lamaran' => now()->addDays(20)->toDateString(),
                'status' => 'dibuka',
                'deskripsi' => "Mencari talenta desainer UI/UX berbakat untuk membuat wireframe, design system, dan prototipe aplikasi mobile dan dashboard web enterprise.\n\nPersyaratan:\n1. Menguasai tools Figma (Auto Layout, Components, Variants).\n2. Memiliki portofolio karya desain aplikasi web atau mobile.\n3. Memahami prinsip User-Centered Design dan estetika antarmuka bersih.",
                'skills' => [
                    'UI/UX Design' => ['bobot' => 5, 'wajib' => true],
                    'Figma' => ['bobot' => 5, 'wajib' => true],
                    'Tailwind CSS' => ['bobot' => 3, 'wajib' => false],
                ],
            ],
            [
                'company_key' => 'bank_bjb_cirebon',
                'judul' => 'IT Support & Database Administrator Trainee',
                'jenis' => 'magang',
                'kuota' => 3,
                'lokasi' => 'Kota Cirebon (On-site)',
                'gaji_min' => 1500000,
                'gaji_max' => 2200000,
                'batas_lamaran' => now()->addDays(60)->toDateString(),
                'status' => 'dibuka',
                'deskripsi' => "Program magang intensif di Bank BJB Cabang Cirebon untuk mendukung operasional divisi teknologi informasi dan pemeliharaan basis data perbankan daerah.\n\nTanggung Jawab:\n1. Membantu backup dan verifikasi integritas data MySQL / Oracle harian.\n2. Melakukan troubleshooting jaringan lokal dan perangkat komputer cabang.\n3. Membantu inventarisasi perangkat lunak dan perangkat keras kantor.",
                'skills' => [
                    'MySQL' => ['bobot' => 5, 'wajib' => true],
                    'Linux & Server' => ['bobot' => 4, 'wajib' => false],
                    'PHP' => ['bobot' => 3, 'wajib' => false],
                ],
            ],
        ];

        $createdJobs = [];

        foreach ($jobsData as $jData) {
            $company = $companies[$jData['company_key']] ?? null;
            if (!$company) continue;

            $slug = Str::slug($jData['judul']) . '-' . Str::lower(Str::random(5));

            $job = Job::create([
                'company_id' => $company->id,
                'judul' => $jData['judul'],
                'slug' => $slug,
                'deskripsi' => $jData['deskripsi'],
                'lokasi' => $jData['lokasi'],
                'jenis' => $jData['jenis'],
                'gaji_min' => $jData['gaji_min'],
                'gaji_max' => $jData['gaji_max'],
                'kuota' => $jData['kuota'],
                'batas_lamaran' => $jData['batas_lamaran'],
                'status' => $jData['status'],
            ]);

            $syncSkills = [];
            foreach ($jData['skills'] as $skillName => $meta) {
                if (isset($skillMap[$skillName])) {
                    $syncSkills[$skillMap[$skillName]] = [
                        'bobot' => $meta['bobot'],
                        'wajib' => $meta['wajib'],
                    ];
                }
            }
            $job->skills()->sync($syncSkills);

            $createdJobs[] = $job;
        }

        // ==========================================
        // 6. PELAMAR DEMO (Applications Terhubung)
        // ==========================================
        $rifqiStudent = Student::whereHas('user', fn($q) => $q->where('email', 'siswa@demo.com'))->first();
        $salsaStudent = Student::whereHas('user', fn($q) => $q->where('email', 'salsabila.putri@smkn1cirebon.sch.id'))->first();
        $aryaStudent = Student::whereHas('user', fn($q) => $q->where('email', 'arya.nugraha@smkn1cirebon.sch.id'))->first();

        // 1. Rifqi melamar ke lowongan PKL Telkom Akses (Job pertama)
        if ($rifqiStudent && isset($createdJobs[0])) {
            Application::updateOrCreate(
                [
                    'job_id' => $createdJobs[0]->id,
                    'student_id' => $rifqiStudent->id,
                ],
                [
                    'skor_kecocokan' => 88.50,
                    'status' => 'wawancara',
                    'pesan' => 'Selamat siang bapak/ibu HRD PT Telkom Akses Cirebon. Saya Rifqi Pratama dari XI PPLG 1 SMKN 1 Cirebon sangat bersemangat mengikuti program PKL ini untuk berkontribusi dalam pemeliharaan web monitoring.',
                    'catatan_hrd' => 'Kandidat memiliki portofolio Laravel dan pemahaman database yang solid. Dijadwalkan wawancara teknis hari Kamis pukul 09.30 WIB di kantor Telkom Pagongan.',
                ]
            );
        }

        // 2. Salsabila melamar ke lowongan UI/UX di SolusiDev (Job ke-4)
        if ($salsaStudent && isset($createdJobs[3])) {
            Application::updateOrCreate(
                [
                    'job_id' => $createdJobs[3]->id,
                    'student_id' => $salsaStudent->id,
                ],
                [
                    'skor_kecocokan' => 94.00,
                    'status' => 'ditinjau',
                    'pesan' => 'Halo tim rekrutmen SolusiDev. Saya Salsabila dari PPLG memiliki keahlian mendalam di Figma dan Tailwind CSS, serta aktif membuat prototipe desain aplikasi.',
                    'catatan_hrd' => 'Portofolio desain UI sangat rapi dan konsisten. Sedang diverifikasi oleh lead designer.',
                ]
            );
        }

        // 3. Arya melamar ke lowongan Junior Web Dev di SolusiDev (Job ke-2)
        if ($aryaStudent && isset($createdJobs[1])) {
            Application::updateOrCreate(
                [
                    'job_id' => $createdJobs[1]->id,
                    'student_id' => $aryaStudent->id,
                ],
                [
                    'skor_kecocokan' => 82.00,
                    'status' => 'tertunda',
                    'pesan' => 'Saya tertarik melamar posisi Junior Web Developer untuk mengasah kemampuan backend database MySQL dan framework Laravel.',
                    'catatan_hrd' => null,
                ]
            );
        }
    }
}
