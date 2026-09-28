# CareerSchool — SMKN 1 Cirebon

<p align="center">
  <img src="public/images/logos/logo-career-school.png" width="96" height="96" alt="CareerSchool Logo">
</p>

<p align="center">
  <strong>Platform Karier, Portofolio Karya, dan Jejaring Resmi SMK Negeri 1 Cirebon</strong><br>
  <em>Paket 15 PKWU XI PPLG • CV. Gurat Cipta Mandiri</em>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/TailwindCSS-3.4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine.js">
  <img src="https://img.shields.io/badge/Choices.js-11.x-10B981?style=for-the-badge" alt="Choices.js">
  <img src="https://img.shields.io/badge/Database-MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
</p>

---

## 📌 Ringkasan Proyek

**CareerSchool** adalah platform karier dan jejaring sosial vokasi resmi untuk **SMK Negeri 1 Cirebon**. Platform ini dibangun sesuai spesifikasi **Kerangka Acuan Kerja (KAK) SMART SCHOOL SOCIAL PLATFORM (Paket 15)** dan dokumen tender **CV. Gurat Cipta Mandiri**.

Berbeda dengan bursa kerja digital konvensional, CareerSchool mengintegrasikan:
1. **Validasi Kompetensi Siswa**: Setiap keahlian yang tercantum divalidasi dan diakui (*endorsed*) oleh Guru Kejuruan.
2. **Portofolio Terverifikasi**: Menampilkan bukti nyata karya siswa yang terhubung langsung dengan profil kompetensi.
3. **Formula Job Matching Transparan**: Algoritma cerdas yang menghitung persentase kecocokan antara profil kandidat siswa dan kualifikasi lowongan industri mitra sekolah.

---

## 🚀 Fitur yang Telah Selesai

### 1. Landing Page Interaktif & Clean Section Routing
- **Navigasi Seamless Tanpa Hash**: Mendukung URL bersih `/beranda`, `/platform`, `/sekolah`, `/fitur-unggulan`, `/cara-kerja`, `/statistik`, dan `/faq` dengan sinkronisasi otomatis History API dan *scroll-spy*.
- **Simulator Job Matching Formula**: Interaktif bertenaga Alpine.js untuk menguji perhitungan skor kecocokan secara *real-time*.
- **Dukungan Dark Mode Penuh**: Sistem tema adaptif dengan penyimpanan preferensi di `localStorage`.
- **Desain & Aset Grafis Eksklusif**: Menggunakan palet token warna resmi, tipografi *Plus Jakarta Sans* & *Sora*, serta ilustrasi vektor Figma tanpa elemen emoji keyboard.

### 2. Autentikasi Multi-Peran
- **Login Terpadu (`/login`)**:
  - Mendukung login dengan Email atau Username.
  - Tampilan *split layout*: form modern di sisi kiri dan ilustrasi grafis di sisi kanan.
  - Interaksi *show/hide password* dengan animasi transisi ikon mata.
- **Registrasi Terarah (`/register`)**:
  - Pemilihan peran terstruktur di awal: **Siswa**, **Guru Kejuruan**, dan **Mitra Perusahaan**.
  - Aset visual samping berganti secara kontekstual mengikuti peran yang dipilih.
  - **Aktivasi Siswa**: Verifikasi 10 digit NISN, pemilihan Jurusan dan Kelas dengan *dependent dropdown*.
  - **Pendaftaran Guru**: Validasi NIP, nama lengkap, gelar, dan konsentrasi kejuruan.
  - **Kemitraan Industri**: Data instansi perusahaan, PIC/HRD, bidang usaha, dan domisili kantor.

### 3. Komponen Dropdown Kustom (Choices.js)
- Seluruh elemen dropdown menggunakan **Choices.js** dengan tampilan *rounded-pill* (`rounded-full`) yang identik dengan form input.
- Mendukung mode terang dan gelap (*dark mode*) secara otomatis.
- Dilengkapi *hairline angle chevron indicator* dan menu melayang (*floating card*) berbayang lembut.
- Mendukung *reactive dependent filtering* (pilihan kelas otomatis menyesuaikan jurusan yang dipilih).

---

## 🧮 Formula Algoritma Job Matching (KAK Paket 15)

Kalkulasi persentase kecocokan kandidat dan lowongan dihitung secara objektif menggunakan formula:

$$\text{Total Skor} = (0.60 \times K) + (0.15 \times J) + (0.10 \times T) + (0.10 \times P) + (0.05 \times L)$$

| Komponen | Bobot | Deskripsi |
| :--- | :---: | :--- |
| **Keahlian ($K$)** | **60%** | Kesesuaian keahlian siswa dengan syarat lowongan berbobot |
| **Jurusan ($J$)** | **15%** | Kesesuaian jurusan siswa dengan bidang kerja lowongan |
| **Tingkat Kemahiran ($T$)** | **10%** | Rata-rata level penguasaan keahlian (Skala 1 - 5) |
| **Portofolio Terkait ($P$)** | **10%** | Kuantitas & relevansi karya portofolio siswa |
| **Kesediaan Lokasi ($L$)** | **5%** | Kesediaan penempatan lokasi kerja yang disyaratkan |

---

## 🛠️ Tech Stack & Arsitektur

- **Backend Framework**: Laravel 12 (PHP 8.2+)
- **Database**: MySQL 8.x
- **Frontend Architecture**: Laravel Blade, Tailwind CSS v3.4, Alpine.js v3
- **Custom UI Libraries**: Choices.js v11, Lucide Icons
- **Build Tooling**: Vite 6, PostCSS, Autoprefixer
- **Testing Suite**: PHPUnit / Laravel Feature & Unit Tests

---

## 📁 Struktur Direktori Utama

```
career_school/
├── app/
│   ├── Http/Controllers/    # AuthController & Landing Controllers
│   └── Models/              # User, Student, Teacher, Company, Major, Skill, Job, dsb.
├── database/
│   ├── migrations/          # Skema basis data relasional KAK Paket 15
│   └── seeders/             # Data master sekolah, jurusan, kelas & akun demo
├── docs/                    # Dokumentasi KAK, PRD, Desain & Standar Koding
│   ├── prd.md               # Product Requirements Document
│   ├── database.md          # Dokumentasi ERD & Skema Database
│   ├── design.md            # Design System & Token Warna
│   └── coding-standards.md  # Konvensi Kode & Keamanan
├── public/
│   ├── images/              # Logo & aset ilustrasi vektor Figma
│   └── vendor/              # Library mandiri (Choices.js)
├── resources/
│   ├── css/app.css          # Token desain Tailwind & tema kustom Choices.js
│   ├── js/app.js            # Inisialisasi Lucide, tema & dropdown Choices
│   └── views/
│       ├── auth/            # login.blade.php & register.blade.php
│       ├── components/      # Komponen landing (hero, features, simulator, navbar, footer)
│       ├── layouts/         # auth.blade.php & landing.blade.php
│       └── welcome.blade.php # Halaman utama landing multi-section
├── routes/
│   └── web.php              # Rute autentikasi & rute landing page
└── tests/
    └── Feature/AuthTest.php # Pengujian otomatis autentikasi (100% Pass)
```

---

## ⚙️ Panduan Instalasi Lokal

### 1. Prasyarat
- PHP >= 8.2 dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `bcmath`, `curl`
- Composer >= 2.x
- Node.js >= 18.x & NPM
- MySQL / Laragon / XAMPP

### 2. Kloning Repositori
```bash
git clone https://github.com/gurat-lucky-wicaksono/career_school.git
cd career_school
```

### 3. Instalasi Dependensi
```bash
composer install
npm install
```

### 4. Konfigurasi Lingkungan (.env)
Salin berkas konfigurasi lingkungan dan sesuaikan kredensial basis data:
```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan konfigurasi database pada `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=career_school
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Migrasi & Seed Database
```bash
php artisan migrate --seed
```

### 6. Kompilasi Aset Frontend
```bash
# Untuk mode pengembangan (hot-reload)
npm run dev

# Atau kompilasi produksi
npm run build
```

### 7. Menjalankan Server Lokal
```bash
php artisan serve
```
Akses aplikasi melalui browser di: `http://127.0.0.1:8000`

---

## 🧪 Pengujian Otomatis

Jalankan test suite untuk memverifikasi fungsionalitas modul autentikasi dan alur pendaftaran:
```bash
php artisan test --filter=AuthTest
```

Seluruh pengujian saat ini berstatus **PASS (7 passed, 26 assertions)**.

---

## 📄 Lisensi & Hak Cipta

Proyek ini dikembangkan khusus untuk **SMK Negeri 1 Cirebon** dalam rangka pelaksanaan **PKWU Paket 15 (SMART SCHOOL SOCIAL PLATFORM)** oleh **CV. Gurat Cipta Mandiri**. Seluruh hak cipta dilindungi.
