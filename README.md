# CareerSchool — SMKN 1 Cirebon

Platform Karier, Portofolio Karya, dan Bursa Kerja Khusus (BKK) Vokasi Resmi SMK Negeri 1 Cirebon  
Paket 15 PKWU XI PPLG • CV. Gurat Cipta Mandiri

---

## Ringkasan Proyek

CareerSchool adalah ekosistem digital terpadu yang dirancang untuk mengintegrasikan seluruh pemangku kepentingan pendidikan vokasi di SMK Negeri 1 Cirebon: Siswa, Guru Kejuruan, Pengelola Bursa Kerja Khusus (BKK) Sekolah, dan Mitra Industri Dunia Usaha/Dunia Industri (DUDI).

Platform ini dibangun sesuai spesifikasi Kerangka Acuan Kerja (KAK) Smart School Social Platform (Paket 15) dengan keunggulan utama:
1. Validasi Kompetensi Vokasi: Keahlian siswa divalidasi dan diakui langsung oleh Guru Kejuruan pengampu.
2. Portofolio Digital Terverifikasi: Dokumentasi karya riil siswa yang terhubung langsung dengan profil kompetensi teknis.
3. Algoritma Job Matching Terukur: Formula matematis objektif untuk menghitung persentase kecocokan antara profil kandidat siswa dan kualifikasi kebutuhan industri mitra.
4. Portal Mitra DUDI Mandiri: Perusahaan dapat mengunggah lowongan magang (PKL) maupun kerja penuh waktu, meninjau berkas pelamar, dan mengatur tahapan seleksi secara transparan.
5. Manajemen Data Master Sekolah Terpusat: Administrator BKK dapat memonitor keterserapan lulusan, verifikasi profil industri, serta mengelola data guru, siswa, kelas, dan jurusan.

---

## Tech Stack & Arsitektur Sistem

- Backend: Laravel 11.x (PHP 8.2+)
- Basis Data: MySQL 8.x
- Frontend: Laravel Blade Components, Tailwind CSS v3.4, Alpine.js v3.x
- Komponen & Ikon: Lucide Icons, Choices.js
- Build Tooling: Vite 6, PostCSS, Autoprefixer
- Testing Suite: PHPUnit 11 (32 Feature & Unit Tests, 181 Assertions)

---

## Fitur Utama Aplikasi

### 1. Landing Page Publik & Simulator Algoritma
- Rute Bersih Tanpa Hash: Navigasi modern `/beranda`, `/platform`, `/sekolah`, `/fitur-unggulan`, `/cara-kerja`, `/statistik`, dan `/faq` dengan sinkronisasi History API.
- Simulator Job Matching Interaktif: Pengujian formula kecocokan secara langsung berbasis Alpine.js.
- Tema Adaptif: Dukungan Dark Mode dan Light Mode penuh yang tersimpan pada penyimpanan lokal pengguna.

### 2. Autentikasi & Otorisasi Berbasis Peran (Role Guard)
- Login Terpadu: Mendukung kredensial Email atau Username dengan visibilitas sandi interaktif.
- Pengalihan Cerdas: Pengguna secara otomatis diarahkan ke dashboard sesuai hak akses (Admin ke `/admin/dashboard`, Perusahaan ke `/company/dashboard`, dsb).
- Registrasi Terarah:
  - Siswa: Validasi 10 digit NISN, pemilihan Jurusan PPLG, dan kelas aktif.
  - Guru: Validasi NIP resmi, gelar akademik, dan penugasan mata pelajaran kejuruan.
  - Mitra Perusahaan: Profil instansi, bidang usaha, penanggung jawab (PIC/HRD), dan domisili kantor.

### 3. Portal Dashboard Administrator BKK
- Ringkasan Metrik: Kartu statistik total siswa terdaftar, mitra terverifikasi, lowongan dibuka, dan total lamaran masuk.
- Visualisasi Keterserapan: Grafik tren keterserapan lulusan dan persentase siswa aktif PKL.
- Navigasi Keyboard-First: Shortcut `Ctrl + B` untuk toggle sidebar dan `Ctrl + K` untuk membuka pencarian global cepat.
- Manajemen Data Master Lengkap:
  - Guru Kejuruan: Penambahan, pengeditan, pencarian, dan penugasan wali kelas.
  - Siswa Vokasi: Pengelolaan NISN, angkatan, penempatan kelas, dan profil kompetensi.
  - Rombongan Belajar: Manajemen kelas X, XI, XII PPLG.
  - Konsentrasi Keahlian: Manajemen kurikulum kejuruan PPLG.
  - Standar Kompetensi: Bank keahlian teknis (Web, Mobile, Database, UI/UX, Server).
  - Verifikasi Mitra Industri: Antarmuka persetujuan (approve), penolakan, atau penangguhan akun perusahaan pendaftar.
  - Pusat Notifikasi & Pengaturan: Konfigurasi profil sekolah dan log aktivitas sistem.

### 4. Portal Mitra Industri (Company Portal)
- Dashboard Rekrutmen: Metrik pelamar baru, lowongan aktif, dan jadwal seleksi.
- Manajemen Lowongan Kerja & PKL:
  - Formulir penambahan lowongan dengan kriteria jenis kerja (magang, penuh waktu, paruh waktu, proyek).
  - Penentuan bobot keahlian (1-5) dan status keahlian wajib/pendukung.
  - Toggle buka/tutup status pendaftaran lowongan secara instan.
- Tinjauan Kandidat Pelamar:
  - Halaman detail pelamar komprehensif menampilkan profil, kontak, dan riwayat portofolio.
  - Analisis matriks kesesuaian syarat keahlian lowongan vs kompetensi siswa.
  - Pembaruan status lamaran (tertunda, ditinjau, wawancara, diterima, ditolak) disertai catatan resmi tim HRD.
- Profil Perusahaan: Pengaturan identitas instansi, legalitas, deskripsi, situs web, dan kontak representatif.

### 5. Komponen UI Reusable & Standar Desain
- Koleksi komponen Blade modular: Button, IconButton, Card, StatCard, Badge, Modal dialog melayang, Breadcrumb, EmptyState, DataTable, dan Pagination kustom.
- Dropdown kustom bertenaga Choices.js dengan styling rounded yang selaras dengan tema input formulir.

---

## Formula Algoritma Job Matching (KAK Paket 15)

Kalkulasi persentase kecocokan antara profil kandidat siswa dan kualifikasi lowongan industri dihitung menggunakan formula:

$$\text{Total Skor} = (0.60 \times K) + (0.15 \times J) + (0.10 \times T) + (0.10 \times P) + (0.05 \times L)$$

| Komponen | Bobot | Deskripsi |
| :--- | :---: | :--- |
| Keahlian ($K$) | 60% | Tingkat pemenuhan keahlian teknis siswa terhadap syarat lowongan berbobot |
| Jurusan ($J$) | 15% | Kesesuaian konsentrasi jurusan siswa dengan bidang lowongan kerja |
| Tingkat Kemahiran ($T$) | 10% | Rata-rata tingkat kemahiran penguasaan keahlian (Skala 1 - 5) |
| Portofolio Terkait ($P$) | 10% | Kuantitas dan relevansi bukti karya portofolio siswa |
| Kesediaan Lokasi ($L$) | 5% | Kesesuaian preferensi penempatan kerja siswa dengan domisili industri |

---

## Status Progres Pengembangan

### Modul yang Telah Selesai
- [x] Fondasi arsitektur sistem, skema basis data relasional KAK Paket 15, dan relasi Eloquent.
- [x] Landing page interaktif, navigasi clean URL, dark mode toggle, dan simulator formula matching.
- [x] Autentikasi multi-peran (login, registrasi multi-entitas, logout, dan pengalihan dinamis).
- [x] Komponen UI kustom (Blade components, modal pop-up, data table, choices dropdown, pagination).
- [x] Portal Admin BKK (Dashboard metrik, CRUD data master lengkap, verifikasi mitra DUDI, global search).
- [x] Portal Mitra Industri (Dashboard lowongan, kelola posting PKL/kerja, tinjau pelamar, update status seleksi).
- [x] Pembersihan data demo dan seeder realistis terfokus pada konsentrasi keahlian PPLG.
- [x] Rangkaian pengujian otomatis PHPUnit Feature Tests (32 pengujian, 181 assertions, 100% lulus).

### Modul Tahap Berikutnya (Roadmap)
- [ ] Portal Siswa (Dashboard personal, pembuatan portofolio proyek, eksplorasi lowongan, dan pelacakan lamaran).
- [ ] Portal Guru Kejuruan (Validasi keahlian siswa perwalian, evaluasi kompetensi, dan modul pendampingan).
- [ ] Feed Sosial Vokasi (Berbagi karya, materi pembelajaran, interaksi komentar, like, dan hashtag).
- [ ] Sistem Notifikasi Real-time (Pemberitahuan panggilan wawancara dan validasi portofolio).

---

## Struktur Direktori Utama

```
career_school/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/           # Controller portal Admin BKK
│   │   │   ├── Company/         # Controller portal Mitra Industri DUDI
│   │   │   └── AuthController.php
│   │   └── Middleware/
│   │       └── EnsureRole.php   # Middleware proteksi hak akses peran
│   └── Models/                  # Model Eloquent (User, Student, Teacher, Company, Job, dsb)
├── database/
│   ├── migrations/              # 34 migrasi basis data relasional
│   └── seeders/                 # Seeder master PPLG, profil sekolah, dan akun demo realistis
├── resources/
│   ├── css/app.css              # Konfigurasi Tailwind CSS dan token warna
│   ├── js/app.js                # Inisialisasi Lucide icons dan Alpine.js
│   └── views/
│       ├── admin/               # Tampilan modul portal Administrator
│       ├── company/             # Tampilan modul portal Perusahaan
│       ├── components/          # Komponen UI Blade dan tata letak
│       └── layouts/             # Template layout admin, company, dan auth
├── routes/
│   └── web.php                  # Definisi rute autentikasi, admin, dan company
└── tests/
    └── Feature/                 # 32 pengujian otomatis PHPUnit
```

---

## Panduan Instalasi dan Menjalankan Proyek

Ikuti langkah-langkah di bawah ini untuk menyiapkan dan menjalankan proyek CareerSchool di lingkungan lokal Anda:

### 1. Prasyarat Sistem
Pastikan perangkat lunak berikut telah terpasang pada komputer Anda:
- PHP >= 8.2 (dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `bcmath`, `curl`, `fileinfo`)
- Composer >= 2.x
- Node.js >= 18.x dan NPM
- Server MySQL (Laragon, XAMPP, atau instalasi mandiri)
- Git

### 2. Kloning Repositori
Buka terminal dan unduh repositori:
```bash
git clone https://github.com/gurat-lucky-wicaksono/career_school.git
cd career_school
```

### 3. Instalasi Dependensi Backend dan Frontend
Pasang pustaka dependensi PHP:
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
cp .env.example .env
```

Bagi pengguna Windows PowerShell:
```powershell
copy .env.example .env
```

Hasilkan kunci enkripsi aplikasi:
```bash
php artisan key:generate
```

Buka berkas `.env` dan sesuaikan konfigurasi koneksi basis data MySQL Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=career_school
DB_USERNAME=root
DB_PASSWORD=
```
Pastikan database `career_school` telah dibuat pada server MySQL Anda.

### 5. Eksekusi Migrasi Basis Data dan Seeder Demo
Jalankan migrasi skema tabel beserta pengisian data awal:
```bash
php artisan migrate:fresh --seed
```
Perintah ini akan menyusun seluruh tabel relasional dan mengisi data master PPLG, rombel, akun demo sekolah, guru kejuruan, siswa, mitra DUDI, lowongan, serta contoh lamaran kerja.

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
Jalankan server aplikasi Laravel:
```bash
php artisan serve
```

Aplikasi dapat diakses melalui peramban web pada alamat:
`http://127.0.0.1:8000`

---

## Akun Demo Pengujian

Semua akun demo di bawah ini menggunakan kata sandi yang seragam: `password`

| Peran Akun | Nama Pengguna | Alamat Email | Halaman Akses |
| :--- | :--- | :--- | :--- |
| Admin Sekolah | Administrator BKK SMKN 1 | `admin@demo.com` | `http://127.0.0.1:8000/admin/dashboard` |
| Guru Kejuruan | Bambang Sudarsono, S.Kom. | `guru@demo.com` | `http://127.0.0.1:8000/login` |
| Siswa PPLG | Rifqi Pratama | `siswa@demo.com` | `http://127.0.0.1:8000/login` |
| Mitra Industri | PT Telkom Akses Cirebon | `perusahaan@demo.com` | `http://127.0.0.1:8000/company/dashboard` |

---

## Pengujian Otomatis (Automated Tests)

Untuk memverifikasi keandalan sistem, jalankan seluruh rangkaian pengujian fitur:
```bash
php -d memory_limit=512M vendor/bin/phpunit
```
Seluruh 32 pengujian fitur mencakup autentikasi, dashboard admin, CRUD data master, pencarian global, dan alur seleksi mitra industri terkonfigurasi lulus 100% tanpa meninggalkan data dummy di basis data utama.

---

## Lisensi dan Hak Cipta

Proyek ini dikembangkan khusus untuk SMK Negeri 1 Cirebon dalam rangka pelaksanaan program PKWU Paket 15 (Smart School Social Platform) oleh CV. Gurat Cipta Mandiri. Seluruh hak cipta dilindungi undang-undang.
