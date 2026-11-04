# CareerSchool &mdash; SMKN 1 Cirebon

**Platform Karier, Portofolio Karya, dan Bursa Kerja Khusus (BKK) Vokasi Resmi SMK Negeri 1 Cirebon**  
*Paket 15 PKWU XI PPLG &bull; Dikembangkan oleh CV. Gurat Cipta Mandiri*

---

## 1. Ringkasan Eksekutif

**CareerSchool** adalah ekosistem digital terpadu yang dirancang untuk mengintegrasikan empat pemangku kepentingan utama pendidikan vokasi di SMK Negeri 1 Cirebon:
1. **Pengelola Bursa Kerja Khusus (BKK) / Administrator Sekolah**
2. **Mitra Dunia Usaha & Dunia Industri (DUDI)**
3. **Siswa Vokasi (Konsentrasi Keahlian Pengembangan Perangkat Lunak dan Gim / PPLG)**
4. **Guru Kejuruan & Pembimbing Praktik Kerja Lapangan (PKL)**

Platform ini dibangun berdasarkan spesifikasi Kerangka Acuan Kerja (KAK) Smart School Social Platform (Paket 15) dengan keunggulan utama:
- **Validasi Kompetensi Vokasi Berjenjang**: Pengesahan keahlian teknis siswa langsung oleh guru pengampu kejuruan dengan catatan kualitatif (*endorsement*).
- **Portofolio Digital Terverifikasi**: Dokumentasi karya riil siswa vokasi (tautan GitHub, demo aplikasi, tangkapan layar) yang terhubung ke kurikulum.
- **Algoritma Pencocokan Lowongan Objektif (KAK 15)**: Perhitungan skor kesesuaian multi-komponen antara kualifikasi pelamar dan kebutuhan industri.
- **Portal Industri Mandiri**: Akses langsung bagi mitra industri untuk menerbitkan lowongan kerja/PKL, menyeleksi kandidat, dan memberikan catatan HRD.
- **Tata Kelola BKK & Kepatuhan Regulasi**: Pelaporan statistik IKU akreditasi, penelusuran alumni BMW, cetak dokumen resmi A4 ber-kop dinas, audit trail aktivitas, serta pengaturan identitas lembaga dinamis.

---

## 2. Tech Stack & Arsitektur Sistem

- **Backend Framework**: Laravel 11.x (PHP 8.2+ / PHP 8.4)
- **Basis Data**: MySQL 8.x (37 Tabel Migrasi Relasional)
- **Frontend Architecture**: Laravel Blade Components, Tailwind CSS v3.4, Alpine.js v3.x
- **Ikonografi & UI Enhancement**: Lucide Icons, Choices.js
- **Mesin Dokumen & Pelaporan**: Print Engine CSS (A4 ISO 216), StreamedResponse CSV RFC 4180 dengan UTF-8 BOM
- **Build Tooling**: Vite 6, PostCSS, Autoprefixer
- **Testing Suite**: PHPUnit 11 (**140 Feature & Unit Tests, 536 Assertions, 100% Lulus**)

---

## 3. Pilar Fitur Utama Aplikasi (Status: 100% Selesai)

### A. Landing Page Publik & Simulator Algoritma KAK 15
- **Navigasi Bersih (Clean URLs)**: Akses rute publik tanpa fragment hash (`/beranda`, `/platform`, `/sekolah`, `/fitur-unggulan`, `/cara-kerja`, `/statistik`, `/faq`) dengan sinkronisasi History API browser.
- **Simulator Pencocokan Interaktif**: Form kalkulator skor kecocokan berbasis Alpine.js untuk mensimulasikan formula KAK 15 secara transparan di halaman depan.
- **Tema Adaptif Global**: Dark Mode dan Light Mode terintegrasi penuh di seluruh portal dengan penyimpanan preferensi lokal dan skrip anti-FOUC.

### B. Autentikasi & Otorisasi Berbasis Peran (Role Guard)
- **Login Terpadu**: Mendukung autentikasi menggunakan Email atau Username dengan tombol periksa sandi interaktif.
- **Role-Based Redirection**: Pengalihan otomatis pasca-login ke dasbor masing-masing entitas (`admin`, `perusahaan`, `guru`, `siswa`).
- **Registrasi Khusus Peran**:
  - Siswa: Validasi 10 digit NISN, pemilihan kelas aktif (X, XI, XII PPLG), dan angkatan.
  - Guru: Validasi NIP resmi, gelar akademik, dan mata pelajaran kejuruan.
  - Perusahaan Mitra: Profil instansi, bidang usaha, penanggung jawab (HRD/PIC), dan legalitas alamat kantor.

### C. Portal Administrator BKK SMKN 1 Cirebon (`/admin`)
- **Executive Dashboard**: Metrik siswa aktif, mitra industri terverifikasi, lowongan dibuka, rasio penyerapan kerja, dan grafik aktivitas sistem.
- **Navigasi Keyboard-First**: Pintasan `Ctrl + B` untuk mengubah ukuran sidebar (dual-mode) dan `Ctrl + K` untuk pencarian global cepat.
- **Manajemen Data Master Lengkap**:
  - Guru Kejuruan: Penambahan, pengeditan, pencarian, dan penugasan wali kelas.
  - Siswa Vokasi: Pengelolaan NISN, angkatan, penempatan rombel, dan kompetensi keahlian.
  - Rombongan Belajar & Jurusan: Manajemen kelas X, XI, XII PPLG serta konsentrasi keahlian.
  - Bank Keahlian: Taksonomi kompetensi teknis (Web, Mobile, Database, UI/UX, Cloud/Server).
  - Verifikasi Mitra Industri: Antarmuka persetujuan (*approve*), penolakan (*reject*), dan peninjauan legalitas DUDI.

### D. Portal Mitra Industri DUDI (`/company`)
- **Penerbitan Lowongan Kerja & Magang**: Pembuatan lowongan PKL dan kerja penuh waktu dengan kriteria bobot keahlian (skala 1-5).
- **Pipeline Manajemen Pelamar**: Pelacakan status berjenjang (`tertunda` -> `ditinjau` -> `wawancara` -> `diterima` / `ditolak`).
- **Analisis Kecocokan Kandidat**: Integrasi skor matematis KAK 15 pada setiap berkas pelamar disertai rincian kecocokan kompetensi.
- **Catatan Seleksi HRD**: Pemberian umpan balik dan catatan rekruter yang langsung terkirim ke pelamar siswa.

### E. Portal Siswa Vokasi PPLG (`/student`)
- **Eksplorasi Lowongan & Magang**: Pencarian lowongan dengan filter tipe, lokasi, dan perhitungan skor kecocokan personal.
- **Pelacakan Lamaran & Timeline Seleksi**: Riwayat pengajuan lamaran lengkap dengan linimasa tahapan seleksi dan catatan HRD.
- **Portofolio Digital**: Manajemen proyek karya siswa, tautan repositori GitHub, URL demo aplikasi, dan unggahan tangkapan layar.
- **Keahlian Mandiri**: Pengajuan keahlian teknis siswa yang siap ditinjau dan divalidasi oleh guru pembimbing.
- **Cetak Bukti Pendaftaran & Rekomendasi Magang**: Lembar cetak resmi A4 berkop SMKN 1 Cirebon dengan verifikasi digital sistem.

### F. Portal Guru Kejuruan & Pembimbing PKL (`/teacher`)
- **Validasi Keahlian Siswa**: Tinjauan mandiri maupun massal (*bulk validation*) untuk mengesahkan tingkat kemahiran kompetensi siswa (skala 1-5).
- **Pengesahan Endorsement Resmi**: Pemberian catatan pengesahan kompetensi resmi yang terhubung ke profil publik dan resume pelamar siswa.
- **Pemantauan Praktik Kerja Lapangan**: Monitoring status penempatan magang PKL siswa binaan di industri mitra.

### G. Pusat Informasi BKK SMKN 1 Cirebon (`/pengumuman`, `/agenda`)
- **Pengumuman Resmi BKK**: Informasi seleksi kampus rekrutmen, bursa kerja khusus, dan edaran dinas sekolah lengkap dengan lampiran berkas resmi.
- **Kalender Agenda Kegiatan**: Jadwal rekrutmen langsung, pembekalan magang, dan seminar karir industri.
- **Integrasi Lintas Portal**: Widget pengumuman terbaru muncul otomatis pada dashboard siswa dan guru.

### H. Jejaring Sosial Vokasi & Community Feed (`/komunitas`)
- **Linimasa Multi-Kategori**: Tab linimasa Umum, Prestasi & Portofolio, Lowongan & Peluang Karir, serta Diskusi Tanya Jawab.
- **Penyusunan Konten Kaya**: Editor postingan dengan tagar otomatis, blok kode program (*syntax highlighted code block*), dan gambar.
- **Interaksi Sosial Lengkap**: Fitur Like/Unlike, komentar berulir (*threaded comments*), penyimpanan (*bookmarks*), dan relasi Follow/Unfollow.
- **Profil Anggota Komunitas**: Direktori siswa, guru, alumni, dan mitra industri dengan daftar karya riil dan keahlian terverifikasi.

### I. Modul Tracer Study & Penelusuran Alumni BMW (`/alumni`, `/tracer-study`)
- **Direktori Alumni Terbuka**: Penelusuran alumni berstatus Bekerja, Melanjutkan Pendidikan, atau Wirausaha (BMW).
- **Kuesioner Tracer Study Interaktif**: Formulir pelacakan transisi karir lulusan, relevansi kurikulum, dan masa tunggu kerja.
- **Pengaturan Privasi Kontak**: Pengendalian privasi nomor telepon, email, dan akun media sosial oleh masing-masing alumni.
- **Panel Statistik BKK Admin**: Metrik agregat penyerapan lulusan SMKN 1 Cirebon untuk kebutuhan pelaporan akreditasi sekolah.

### J. Pusat Notifikasi Interaktif Terpadu (Unified Notification Center)
- **Komponen Lonceng Header Terpadu**: Komponen Alpine.js interaktif dengan counter notifikasi belum dibaca (*unread badge*), dropdown 5 notifikasi terkini, aksi tandai baca cepat, dan tombol tandai semua telah dibaca.
- **Halaman Riwayat Notifikasi Universal (`/notifications`)**: Tab filter status dan kategori (Semua, Belum Dibaca, Lamaran, Keahlian, Komunitas).
- **Pemicu Event Otomatis Real-time**: Notifikasi otomatis saat siswa melamar pekerjaan, status seleksi berubah, guru mengesahkan keahlian, atau terjadi interaksi sosial.

### K. Modul Ekspor Laporan Resmi BKK (Print-Ready PDF & CSV Datasets)
- **Dasbor Pusat Laporan BKK (`/admin/reports`)**: Filter berdasarkan angkatan/tahun dan jurusan, ringkasan IKU penyerapan kerja, persentase BMW alumni, dan relevansi kurikulum.
- **Laporan Resmi Siap Cetak (Print-Ready PDF View)**: Format dokumen standar A4 lengkap dengan kop surat resmi Pemerintah Provinsi Jawa Barat, Dinas Pendidikan Cabang Wilayah X, SMKN 1 Cirebon, tabel data terstruktur, serta kolom pengesahan Kepala Sekolah dan Ketua BKK.
- **Ekspor CSV Tracer Study Alumni**: Unduhan berkas RFC 4180 CSV ber-encoding UTF-8 BOM untuk kompatibilitas penuh dengan Microsoft Excel.
- **Ekspor CSV Rekapitulasi Lamaran**: Unduhan berkas pendaftaran kerja/PKL lengkap dengan skor algoritma KAK 15 dan catatan seleksi industri.

### L. Audit Trail & Log Aktivitas Sistem (Security Monitor)
- **Dasbor Pusat Audit Log Admin (`/admin/activity-logs`)**: Panel audit dengan 4 kartu metrik real-time (Total Aktivitas 24 Jam, Login & Keamanan, Mutasi Status Lamaran, Validasi Keahlian Guru).
- **Pencarian Multi-Parameter**: Filter berdasarkan kata kunci, peran pengguna, kategori aksi (Autentikasi, Kemitraan, Lamaran, Validasi, Ekspor Laporan), dan rentang tanggal.
- **Modal Rincian JSON Interaktif**: Inspeksi mendalam dengan pop-up Alpine.js yang menampilkan metadata teknis kejadian, User-Agent browser, alamat IP klien, waktu relatif, dan payload JSON diff perubahan status.
- **Ekspor Berkas CSV Audit**: Unduhan riwayat log aktivitas berstandar RFC 4180 untuk kebutuhan kepatuhan regulasi dan audit keamanan TI.

### M. Pengaturan Identitas Lembaga & Manajemen Tahun Akademik
- **Dasbor Konfigurasi Multi-Tab (`/admin/settings`)**: Pengaturan profil sekolah (nama, NPSN, alamat, telepon, email, website), pimpinan (Kepala Sekolah & NIP), legalitas BKK (nama unit, Ketua BKK, nomor SK Disnaker, hotline), tahun ajaran berjalan, semester aktif, dan kuota lamaran.
- **Penyimpanan Key-Value Ter-Cache**: Akses data pengaturan cepat melalui `SchoolSettingService` dengan invalidasi cache otomatis.
- **Kop Dokumen Dinamis**: Seluruh berkas cetak resmi (laporan BKK dan bukti pendaftaran siswa) membaca data identitas langsung dari basis data.
- **Penegakan Aturan Bisnis**: Batas maksimal lamaran aktif per siswa dan opsi penutupan pendaftaran mandiri siswa.

---

## 4. Formula Matematis Algoritma Job Matching (KAK Paket 15)

Kalkulasi persentase kecocokan antara profil kandidat siswa dan kualifikasi lowongan industri dihitung menggunakan formula:

$$\text{Total Skor} = (0.60 \times K) + (0.15 \times J) + (0.10 \times T) + (0.10 \times P) + (0.05 \times L)$$

| Komponen | Bobot | Deskripsi Teknis |
| :--- | :---: | :--- |
| **Keahlian ($K$)** | 60% | Persentase pemenuhan keahlian teknis siswa terhadap syarat keahlian berbobot lowongan. |
| **Jurusan ($J$)** | 15% | Kesesuaian konsentrasi keahlian siswa (PPLG) dengan bidang keahlian lowongan. |
| **Tingkat Kemahiran ($T$)** | 10% | Rata-rata tingkat kemahiran penguasaan keahlian yang dimiliki siswa (skala 1 - 5). |
| **Portofolio Terkait ($P$)** | 10% | Kuantitas dan relevansi bukti karya portofolio siswa terhadap posisi yang dilamar. |
| **Kesediaan Lokasi ($L$)** | 5% | Kesesuaian preferensi penempatan kerja siswa dengan domisili industri mitra. |

---

## 5. Matriks Akun Uji Coba Multi-Peran

Semua akun demo di bawah ini menggunakan kata sandi standar yang seragam: `password`

| Peran (Role) | Entitas / Nama Pengguna | Alamat Email | Halaman Akses Utama |
| :--- | :--- | :--- | :--- |
| **Admin BKK** | Administrator BKK SMKN 1 | `admin@demo.com` | `/admin/dashboard`, `/admin/reports`, `/admin/activity-logs`, `/admin/settings` |
| **Mitra Industri** | PT Telkom Akses Cirebon | `perusahaan@demo.com` | `/company/dashboard`, `/company/jobs`, `/company/applications` |
| **Guru Kejuruan** | Bambang Sudarsono, S.Kom. | `guru@demo.com` | `/teacher/dashboard`, `/teacher/skills`, `/teacher/mentoring` |
| **Siswa Vokasi** | Rifqi Pratama (XI PPLG 1) | `siswa@demo.com` | `/student/dashboard`, `/student/jobs`, `/student/applications`, `/student/portfolios` |
| **Alumni (Bekerja)** | Dimas Maulana Akbar | `dimas.alumni@demo.com` | `/alumni`, `/tracer-study/kuesioner` |
| **Alumni (Kuliah)** | Annisa Rahmawati | `annisa.alumni@demo.com` | `/alumni`, `/tracer-study/kuesioner` |

---

## 6. Panduan Instalasi dan Menjalankan Proyek Lokal

Ikuti langkah-langkah di bawah ini untuk menyiapkan dan menjalankan proyek CareerSchool di lingkungan lokal Anda:

### 1. Prasyarat Sistem
Pastikan perangkat lunak berikut telah terpasang pada komputer Anda:
- PHP >= 8.2 (dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `bcmath`, `curl`, `fileinfo`)
- Composer >= 2.x
- Node.js >= 18.x dan NPM
- Server MySQL (Laragon, XAMPP, atau instalasi mandiri MySQL 8.x)
- Git

### 2. Kloning Repositori
Buka terminal dan unduh repositori proyek:
```bash
git clone https://github.com/gurat-lucky-wicaksono/career_school.git
cd career_school
```

### 3. Instalasi Dependensi Backend dan Frontend
Pasang paket dependensi PHP:
```bash
composer install
```

Pasang paket dependensi JavaScript:
```bash
npm install
```

### 4. Konfigurasi Lingkungan (.env)
Salin berkas konfigurasi sampel menjadi berkas `.env` aktif:
```bash
# Pengguna Linux / macOS:
cp .env.example .env

# Pengguna Windows PowerShell:
copy .env.example .env
```

Hasilkan kunci enkripsi aplikasi:
```bash
php artisan key:generate
```

Buka berkas `.env` dan sesuaikan koneksi database MySQL Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=career_school
DB_USERNAME=root
DB_PASSWORD=
```
*Pastikan database `career_school` telah dibuat pada server MySQL Anda.*

### 5. Eksekusi Migrasi Basis Data dan Seeder Demo
Jalankan migrasi skema tabel beserta pengisian data awal:
```bash
php artisan migrate:fresh --seed
```
*Perintah ini akan menyusun seluruh 37 tabel relasional dan mengisi data master PPLG, rombongan belajar, akun multi-peran, profil lembaga, lowongan mitra industri, kuesioner tracer study, dan pengaturan sistem.*

Buat tautan simbolis penyimpanan berkas publik:
```bash
php artisan storage:link
```

### 6. Kompilasi Aset Frontend
Untuk mode pengembangan aktif (Hot-Module-Replacement):
```bash
npm run dev
```

Atau untuk mengompilasi aset siap produksi:
```bash
npm run build
```

### 7. Menjalankan Server Lokal
Jalankan server pengembangan Laravel:
```bash
php artisan serve
```

Aplikasi dapat diakses melalui peramban web pada alamat:
`http://127.0.0.1:8000`

---

## 7. Pengujian Otomatis (Automated Tests)

Aplikasi CareerSchool dilengkapi dengan cakupan pengujian otomatis penuh menggunakan PHPUnit 11 untuk memastikan integritas logika bisnis, otorisasi peran, validasi input, serta keamanan sistem:

Jalankan seluruh pengujian fitur dan unit:
```bash
php -d memory_limit=512M vendor/bin/phpunit
```

### Ringkasan Distribusi Pengujian (140 Tests, 100% Lulus):
- `tests/Feature/AuthTest.php`: 9 tests (Autentikasi login, registrasi siswa/guru/perusahaan, remember me, proteksi).
- `tests/Feature/AdminDashboardTest.php`: 3 tests (Akses dashboard, metrik statistik, pengalihan login).
- `tests/Feature/AdminMasterCrudTest.php`: 11 tests (Operasi CRUD data guru, siswa, kelas, jurusan, keahlian).
- `tests/Feature/AdminGlobalSearchTest.php`: 4 tests (Pencarian global terpadu keyboard-first).
- `tests/Feature/CompanyPortalTest.php`: 12 tests (Verifikasi industri, lowongan kerja/PKL, review pelamar, status seleksi).
- `tests/Feature/StudentPortalTest.php`: 19 tests (Dashboard siswa, filter lowongan, pengajuan lamaran, portofolio, profil).
- `tests/Feature/TeacherPortalTest.php`: 11 tests (Dashboard guru, validasi keahlian, pengesahan endorsement, monitoring PKL).
- `tests/Feature/BkkInformationCenterTest.php`: 14 tests (CRUD pengumuman & agenda, filter publik, unduhan lampiran).
- `tests/Feature/CommunityFeedTest.php`: 13 tests (Feed timeline, pembuatan postingan, tagar, like, comment, bookmark, follow).
- `tests/Feature/TracerStudyTest.php`: 14 tests (Direktori alumni BMW, kuesioner tracer, privasi kontak, agregasi IKU admin).
- `tests/Feature/UnifiedNotificationTest.php`: 10 tests (Komponen bell header, filter kategori, mark as read, event triggers).
- `tests/Feature/BkkReportExportTest.php`: 8 tests (Kop dinas resmi, ekspor CSV tracer study & rekap lamaran, slip registrasi).
- `tests/Feature/ActivityLogTest.php`: 11 tests (Dasbor audit log, filter peran, detail modal JSON diff, ekspor CSV).
- `tests/Feature/SchoolSettingTest.php`: 8 tests (Dasbor pengaturan, invalidasi cache, kop dinamis, kuota lamaran aktif).
- `tests/Feature/ExampleTest.php`: 2 tests (Landing page & direct section routes).
- `tests/Unit/ExampleTest.php`: 1 test (Unit baseline).

---

## 8. Struktur Direktori Utama

```
career_school/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/                  # Controller portal Administrator BKK
│   │   │   ├── Company/                # Controller portal Mitra Industri DUDI
│   │   │   ├── Student/                # Controller portal Siswa Vokasi
│   │   │   ├── Teacher/                # Controller portal Guru Kejuruan & PKL
│   │   │   ├── AuthController.php      # Autentikasi terpadu multi-peran
│   │   │   ├── FeedController.php      # Linimasa jejaring sosial vokasi
│   │   │   ├── NotificationController.php # Pusat riwayat notifikasi
│   │   │   ├── PublicInformationController.php # Pengumuman & agenda publik
│   │   │   ├── SocialInteractionController.php # Likes, comments, bookmark, follow
│   │   │   ├── TracerStudyController.php # Direktori alumni & kuesioner tracer
│   │   │   └── UserProfileController.php # Profil publik anggota komunitas
│   │   └── Middleware/
│   │       └── EnsureRole.php          # Middleware proteksi hak akses peran
│   ├── Models/                         # 32 Model Eloquent (User, Student, Job, ActivityLog, dll.)
│   └── Services/                       # 5 Layanan Bisnis Sentral
│       ├── ActivityLogService.php      # Pencatatan audit trail & ekspor CSV
│       ├── JobMatchingService.php      # Perhitungan skor algoritma KAK 15
│       ├── NotificationService.php     # Distribusi notifikasi in-app
│       ├── ReportExportService.php     # Penyusun laporan BKK & ekspor dataset
│       └── SchoolSettingService.php    # Manajemen profil sekolah & cache
├── database/
│   ├── migrations/                     # 37 migrasi basis data relasional
│   └── seeders/                        # 10 seeder master data dan akun demo realistis
├── docs/                               # Dokumentasi teknis sistem & standar desain
│   ├── coding-standards.md
│   ├── database.md
│   ├── design.md
│   ├── prd.md
│   └── system-modules.md               # Peta lengkap 11 modul sistem
├── resources/
│   ├── css/app.css                     # Konfigurasi Tailwind CSS dan tema warna
│   ├── js/app.js                       # Inisialisasi Alpine.js dan Lucide Icons
│   └── views/
│       ├── admin/                      # Tampilan portal Administrator BKK
│       ├── auth/                       # Tampilan login dan registrasi multi-peran
│       ├── company/                    # Tampilan portal Mitra Industri DUDI
│       ├── components/                 # Komponen modular Blade (Card, Button, Modal, dll.)
│       ├── feed/                       # Tampilan feed komunitas vokasi
│       ├── notifications/              # Tampilan pusat notifikasi pengguna
│       ├── public/                     # Tampilan pengumuman dan agenda bursa kerja
│       ├── student/                    # Tampilan portal Siswa Vokasi PPLG
│       ├── teacher/                    # Tampilan portal Guru Kejuruan & PKL
│       └── tracer/                     # Tampilan direktori alumni & kuesioner BMW
├── routes/
│   └── web.php                         # Definisi 167 rute terpadu aplikasi
└── tests/
    └── Feature/                        # 15 berkas pengujian fitur (140 tests)
```

---

## 9. Lisensi dan Hak Cipta

Proyek ini dikembangkan khusus untuk **SMK Negeri 1 Cirebon** dalam rangka pelaksanaan tugas pembelajaran berbasis proyek **Paket 15 PKWU XI PPLG** oleh **CV. Gurat Cipta Mandiri**. Seluruh hak cipta dilindungi undang-undang.
