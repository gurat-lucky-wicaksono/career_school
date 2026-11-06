# Panduan Modul Sistem & Peta Fitur CareerSchool
**SMK Negeri 1 Cirebon &mdash; Paket 15 PKWU XI PPLG**  
*Pengembang: CV. Gurat Cipta Mandiri*

Dokumen ini memetakan seluruh modul sistem yang telah selesai dibangun pada platform **CareerSchool**, mencakup rute endpoint, hak akses otorisasi, dependensi controller/service, serta cakupan pengujian otomatis.

---

## 1. Peta 11 Modul Pilar Sistem

| No | Modul Sistem | Prefix / Rute Utama | Hak Akses (Role) | Controller & Service Utama |
| :---: | :--- | :--- | :--- | :--- |
| 1 | **Landing Page & Simulator KAK 15** | `/`, `/beranda`, `/platform`, `/sekolah`, `/fitur-unggulan`, `/cara-kerja`, `/statistik`, `/faq` | Publik (Guest) | Closure Route (`routes/web.php`) |
| 2 | **Autentikasi & Role Guard** | `/login`, `/register`, `/logout` | Publik & Pengguna | `AuthController.php`, `EnsureRole.php` |
| 3 | **Portal Administrator BKK** | `/admin/*` | `admin` | `Admin\DashboardController`, `Admin\CompanyController`, `Admin\StudentController`, `Admin\TeacherController`, `Admin\SchoolClassController`, `Admin\MajorController`, `Admin\SkillController` |
| 4 | **Portal Mitra Industri DUDI** | `/company/*` | `perusahaan` | `Company\DashboardController`, `Company\JobController`, `Company\ApplicationController`, `Company\ProfileController` |
| 5 | **Portal Siswa Vokasi PPLG** | `/student/*` | `siswa` | `Student\DashboardController`, `Student\JobController`, `Student\ApplicationController`, `Student\PortfolioController`, `Student\ProfileController` |
| 6 | **Portal Guru Kejuruan & PKL** | `/teacher/*` | `guru` | `Teacher\DashboardController`, `Teacher\SkillValidationController`, `Teacher\StudentMentoringController` |
| 7 | **Pusat Informasi BKK Publik** | `/pengumuman`, `/agenda` | Publik & Terautentikasi | `PublicInformationController.php`, `Admin\AnnouncementController`, `Admin\AgendaController` |
| 8 | **Jejaring Vokasi & Feed Komunitas** | `/feed`, `/komunitas`, `/posts/*`, `/u/{username}` | Terautentikasi (Semua Peran) | `FeedController.php`, `SocialInteractionController.php`, `UserProfileController.php` |
| 9 | **Tracer Study Alumni BMW** | `/alumni`, `/tracer-study/kuesioner`, `/admin/tracer` | Publik, Alumni, `admin` | `TracerStudyController.php`, `Admin\TracerStudyController` |
| 10 | **Pusat Notifikasi Interaktif** | `/notifications`, `/api/notifications/*` | Terautentikasi (Semua Peran) | `NotificationController.php`, `NotificationService.php` |
| 11 | **Laporan BKK & Cetak Kop Resmi** | `/admin/reports/*`, `/student/applications/{id}/print` | `admin`, `siswa` | `Admin\ReportController`, `ReportExportService.php` |
| 12 | **Audit Trail & Keamanan Sistem** | `/admin/activity-logs/*` | `admin` | `Admin\ActivityLogController`, `ActivityLogService.php` |
| 13 | **Pengaturan Lembaga & Akademik** | `/admin/settings` | `admin` | `Admin\SettingController`, `SchoolSettingService.php` |
| 14 | **Penilaian & Evaluasi Kinerja PKL** | `/company/evaluations/*`, `/teacher/evaluations/*`, `/student/evaluations/*` | `perusahaan`, `guru`, `siswa` | `Company\InternshipEvaluationController`, `Teacher\InternshipEvaluationController`, `Student\InternshipEvaluationController` |

---

## 2. Arsitektur Layanan Sentral (Core Services)

Aplikasi mengimplementasikan *Service-Layer Pattern* untuk mengisolasi logika bisnis dari controller:

1. **`App\Services\JobMatchingService`**:
   - Menghitung persentase kecocokan 5-komponen KAK 15 secara real-time.
   - Formula:
     $$Skor = (0.60 \times Keahlian) + (0.15 \times Jurusan) + (0.10 \times Tingkat) + (0.10 \times Portofolio) + (0.05 \times Lokasi)$$

2. **`App\Services\ActivityLogService`**:
   - Mencatat log audit immutable lintas modul (login, logout, verifikasi kemitraan, perubahan status lamaran HRD, validasi guru, pengajuan lamaran siswa, dan ekspor laporan).
   - Menyediakan streaming CSV ter-encode UTF-8 BOM untuk Microsoft Excel.

3. **`App\Services\NotificationService`**:
   - Mengelola pengiriman notifikasi in-app real-time untuk event lamaran, validasi kompetensi, serta interaksi komunitas sosial (like, comment, follow).

4. **`App\Services\ReportExportService`**:
   - Mengompilasi data agregat IKU akreditasi, statistik penyerapan kerja, persentase BMW, dan keselarasan kurikulum.
   - Menghasilkan tampilan cetak dokumen A4 siap cetak dengan kop surat resmi Jawa Barat dan tanda tangan digital.
   - Menghasilkan unduhan berkas CSV datasets untuk tracer study dan rekapitulasi pelamar.

5. **`App\Services\SchoolSettingService`**:
   - Mengelola identitas sekolah, nomor SK BKK, pejabat penandatangan, tahun ajaran aktif, dan kuota maksimal lamaran aktif siswa.
   - Terintegrasi dengan Laravel Cache Layer untuk respon cepat tanpa kueri database redundan.

---

## 3. Matriks Hak Akses & Pengalihan Pasca-Login

| Peran (Role) | Default Dashboard | Batas Hak Akses |
| :--- | :--- | :--- |
| `admin` | `/admin/dashboard` | Akses penuh master data, verifikasi mitra, laporan, audit logs, dan pengaturan sistem. |
| `perusahaan` | `/company/dashboard` | Akses penerbitan lowongan, peninjauan pelamar industri, dan pembaruan status seleksi. |
| `guru` | `/teacher/dashboard` | Akses validasi keahlian siswa, pengesahan endorsement resmi, dan monitoring siswa PKL. |
| `siswa` | `/student/dashboard` | Akses eksplorasi lowongan, riwayat lamaran, portofolio digital, klaim keahlian, dan cetak bukti registrasi. |

---

## 4. Ringkasan Pengujian Otomatis (Automated Testing)

Pengujian fitur dijalankan menggunakan PHPUnit 11 dengan struktur pengujian sebagai berikut:

- `tests/Feature/AuthTest.php`: 9 tests (Autentikasi, NISN/NIP, remember me, proteksi).
- `tests/Feature/AdminDashboardTest.php`: 3 tests (Dashboard ringkasan, redirect).
- `tests/Feature/AdminMasterCrudTest.php`: 11 tests (CRUD guru, siswa, kelas, jurusan, keahlian).
- `tests/Feature/AdminGlobalSearchTest.php`: 4 tests (Pencarian global keyboard-first).
- `tests/Feature/CompanyPortalTest.php`: 12 tests (Verifikasi industri, lowongan, review pelamar).
- `tests/Feature/StudentPortalTest.php`: 19 tests (Dashboard, eksplorasi lowongan, apply, portofolio, profil).
- `tests/Feature/TeacherPortalTest.php`: 11 tests (Validasi keahlian, endorsement, monitoring PKL).
- `tests/Feature/BkkInformationCenterTest.php`: 14 tests (CRUD pengumuman & agenda, filter publik, widget).
- `tests/Feature/CommunityFeedTest.php`: 13 tests (Linimasa feed, posting, like, comment, bookmark, follow).
- `tests/Feature/TracerStudyTest.php`: 14 tests (Direktori alumni BMW, kuesioner tracer, agregasi IKU).
- `tests/Feature/UnifiedNotificationTest.php`: 10 tests (Komponen header, mark as read, event triggers).
- `tests/Feature/BkkReportExportTest.php`: 8 tests (Kop dinas resmi, ekspor CSV UTF-8 BOM, slip registrasi).
- `tests/Feature/ActivityLogTest.php`: 11 tests (Dasbor audit log, filter peran, detail modal JSON, ekspor CSV).
- `tests/Feature/SchoolSettingTest.php`: 8 tests (Dasbor pengaturan, invalidasi cache, kop dinamis, kuota lamaran).
- `tests/Feature/ExampleTest.php`: 2 tests (Landing page & direct section routes).
- `tests/Unit/ExampleTest.php`: 1 test (Unit baseline).

**Total Hasil Pengujian**: **140 Tests, 536 Assertions, 100% Lulus (0 Error, 0 Failure)**.
