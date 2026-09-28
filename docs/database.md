# Database Guide — CareerSchool

Dokumentasi skema basis data Paket 15 CareerSchool. Diturunkan dari dokumen tender CV. Gurat Cipta Mandiri, dipakai sebagai acuan tunggal saat menulis migration Laravel supaya ERD dan kode tidak pernah berbeda.

## 1. Spesifikasi Umum

| Aspek | Nilai |
| --- | --- |
| Mesin basis data | MySQL 8 |
| Storage engine | InnoDB |
| Karakter | utf8mb4 (mendukung karakter Indonesia dan emoji) |
| Jumlah tabel | 34 |
| Jumlah kelompok data | 6 (A–F) |

## 2. Konvensi Penamaan dan Aturan Umum

1. Nama tabel huruf kecil, bentuk jamak, pemisah garis bawah — contoh `student_skills`.
2. Setiap tabel punya kunci utama `id` bertipe `BIGINT AUTO_INCREMENT`, kecuali tabel penghubung yang pakai kunci gabungan (`post_hashtag`).
3. Kolom kunci asing memakai pola `<nama_tabel_tunggal>_id`, contoh `job_id`, `skill_id`.
4. Semua tabel punya `created_at` dan `updated_at` untuk audit, kecuali tabel penghubung sederhana yang cukup `created_at`.
5. Kolom status memakai `ENUM` supaya nilai yang tersimpan terbatas pada pilihan yang sah.
6. Kolom uang memakai `DECIMAL`, bukan `FLOAT`, supaya tidak ada pembulatan yang merugikan.
7. Data pengguna tidak dihapus permanen di tabel `posts` — pakai `deleted_at` (soft delete) supaya moderator masih bisa menelusuri riwayat.

## 3. Normalisasi

Skema mengikuti 3NF. Dua hal sengaja didenormalisasi secara sadar, dengan alasan performa baca:

| Denormalisasi | Alasan |
| --- | --- |
| Kolom penghitung di `posts` (`like_count`, `comment_count`, `share_count`, `view_count`) dan `jobs` (`view_count`, `application_count`) | Feed tidak perlu menghitung ulang ribuan baris tiap dibuka. Konsistensi dijaga kode aplikasi dan disegarkan berkala lewat scheduled command |
| Kolom `students.major_id` menyimpan jurusan langsung, walau bisa diturunkan dari `classes.major_id` | Penyaringan data berdasar jurusan tidak perlu join 3 tabel. Diisi otomatis dari kelas saat data siswa dibuat |

## 4. Kelompok Data

| Kode | Kelompok | Isi | Jumlah tabel |
| --- | --- | --- | --- |
| A | Pengguna dan Hak Akses | Akun, peran, profil, pemberitahuan | 4 |
| B | Data Induk Sekolah | Jurusan, kelas, siswa, guru, profil sekolah, fasilitas | 7 |
| C | Pengumuman dan Agenda Sekolah | Pengumuman resmi dan agenda akademik | 2 |
| D | Media Sosial dan Konten | Kategori, postingan, komentar, interaksi, tanda pagar, simpanan, pengikut | 9 |
| E | Karier, Keahlian, dan Portofolio | Keahlian, validasi guru, portofolio, perusahaan, lowongan, lamaran | 8 |
| F | Algoritma dan Analitik | Riwayat pencarian, interaksi, metrik konten, tren, rekomendasi | 5 |

**Catatan fase:** Kelompok A–E dipakai aktif sejak fase MVP. Empat dari lima tabel di Kelompok F (`content_metrics`, `user_interactions`, `trending_topics`, `recommendations`) migration-nya dibuat belakangan saat masuk Fase Lanjutan (Engagement Ranking, Recommendation, Trending). Tabel `search_logs` tetap dibuat dari awal karena berguna untuk pencarian dasar di fase MVP.

## 5. Rincian Tabel

### Kelompok A — Pengguna dan Hak Akses

#### roles
Daftar peran pengguna (admin, guru, siswa, perusahaan). Indeks: `UNIQUE(nama)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | Kunci utama |
| nama | VARCHAR(30) | UK | ya | - | Nama peran, unik |
| label | VARCHAR(50) | - | ya | - | Sebutan peran di antarmuka |
| keterangan | VARCHAR(150) | - | tidak | NULL | Penjelasan hak akses |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |
| updated_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### users
Akun seluruh pengguna, pintu masuk autentikasi. Indeks: `INDEX(role_id)`, `UNIQUE(username)`, `UNIQUE(email)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| role_id | BIGINT(20) | FK | ya | - | → roles.id |
| name | VARCHAR(100) | - | ya | - | Nama lengkap |
| username | VARCHAR(50) | UK | ya | - | Nama pengguna |
| email | VARCHAR(150) | UK | ya | - | Untuk masuk dan notifikasi |
| password | VARCHAR(255) | - | ya | - | Hash bcrypt |
| avatar | VARCHAR(255) | - | tidak | NULL | Berkas foto profil |
| is_active | BOOLEAN | - | ya | TRUE | Akun aktif/diblokir |
| last_login_at | TIMESTAMP | - | tidak | NULL | |
| remember_token | VARCHAR(100) | - | tidak | NULL | Bawaan Laravel |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |
| updated_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### profiles
Data profil tambahan, dipisah dari `users` biar autentikasi ringkas. Indeks: `UNIQUE(user_id)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| user_id | BIGINT(20) | FK, UK | ya | - | → users.id, satu profil per akun |
| bio | TEXT | - | tidak | NULL | |
| telepon | VARCHAR(20) | - | tidak | NULL | |
| tanggal_lahir | DATE | - | tidak | NULL | |
| jenis_kelamin | ENUM(L,P) | - | tidak | NULL | |
| kota | VARCHAR(80) | - | tidak | NULL | |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |
| updated_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### notifications
Pemberitahuan waktu nyata. Indeks: `INDEX(user_id, dibaca)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| user_id | BIGINT(20) | FK | ya | - | Penerima |
| jenis | VARCHAR(40) | - | ya | - | like, comment, follow, application, verification |
| judul | VARCHAR(150) | - | ya | - | Teks singkat |
| referensi_tipe | VARCHAR(30) | - | tidak | NULL | Jenis data terkait |
| referensi_id | BIGINT(20) | - | tidak | NULL | Id data terkait |
| dibaca | BOOLEAN | - | ya | FALSE | |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

### Kelompok B — Data Induk Sekolah

#### majors
Data jurusan. Indeks: `UNIQUE(kode)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| kode | VARCHAR(10) | UK | ya | - | Contoh: PPLG |
| nama | VARCHAR(100) | - | ya | - | |
| kepala_jurusan | VARCHAR(100) | - | tidak | NULL | |
| keterangan | TEXT | - | tidak | NULL | |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |
| updated_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### classes
Data kelas beserta wali kelas dan jurusan. Indeks: `INDEX(major_id)`, `INDEX(wali_kelas_id)`, `UNIQUE(nama, tahun_ajaran)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| major_id | BIGINT(20) | FK | ya | - | → majors.id |
| wali_kelas_id | BIGINT(20) | FK | tidak | NULL | → teachers.id |
| nama | VARCHAR(50) | - | ya | - | Contoh: XI PPLG 2 |
| tingkat | TINYINT(2) | - | ya | - | 10/11/12 |
| tahun_ajaran | VARCHAR(9) | - | ya | - | Contoh: 2026/2027 |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |
| updated_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### students
Data siswa, NISN dipakai untuk aktivasi akun. Indeks: `UNIQUE(user_id)`, `UNIQUE(nisn)`, `INDEX(class_id)`, `INDEX(major_id)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| user_id | BIGINT(20) | FK, UK | ya | - | → users.id |
| nisn | VARCHAR(20) | UK | ya | - | Kunci aktivasi akun |
| class_id | BIGINT(20) | FK | ya | - | → classes.id |
| major_id | BIGINT(20) | FK | ya | - | → majors.id (denormalisasi sadar) |
| angkatan | YEAR(4) | - | ya | - | Tahun masuk |
| status | ENUM(aktif,lulus,pindah) | - | ya | aktif | |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |
| updated_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### teachers
Data guru. Indeks: `UNIQUE(user_id)`, `UNIQUE(nip)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| user_id | BIGINT(20) | FK, UK | ya | - | → users.id |
| nip | VARCHAR(20) | UK | ya | - | |
| mata_pelajaran | VARCHAR(100) | - | tidak | NULL | |
| is_wali_kelas | BOOLEAN | - | ya | FALSE | |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |
| updated_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### school_profiles
Profil sekolah di landing page. **Hanya diisi satu baris data** karena hanya untuk satu sekolah. Indeks: `INDEX(updated_by)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| nama_sekolah | VARCHAR(150) | - | ya | - | |
| alamat | TEXT | - | tidak | NULL | |
| telepon | VARCHAR(20) | - | tidak | NULL | |
| email | VARCHAR(150) | - | tidak | NULL | |
| visi | TEXT | - | tidak | NULL | |
| misi | TEXT | - | tidak | NULL | |
| sejarah | TEXT | - | tidak | NULL | |
| logo | VARCHAR(255) | - | tidak | NULL | |
| updated_by | BIGINT(20) | FK | tidak | NULL | Admin yang terakhir update |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |
| updated_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### facilities
Fasilitas sekolah untuk halaman profil. Indeks: `INDEX(school_profile_id)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| school_profile_id | BIGINT(20) | FK | ya | - | → school_profiles.id |
| nama | VARCHAR(100) | - | ya | - | |
| deskripsi | TEXT | - | tidak | NULL | |
| foto | VARCHAR(255) | - | tidak | NULL | |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

### Kelompok C — Pengumuman dan Agenda Sekolah

#### announcements
Pengumuman resmi, satu arah dan berjadwal, dipisah dari `posts`. Indeks: `INDEX(user_id)`, `INDEX(status, mulai_tampil)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| user_id | BIGINT(20) | FK | ya | - | Admin pembuat |
| judul | VARCHAR(150) | - | ya | - | |
| isi | TEXT | - | ya | - | |
| prioritas | ENUM(rendah,normal,penting) | - | ya | normal | |
| lampiran | VARCHAR(255) | - | tidak | NULL | |
| mulai_tampil | DATETIME | - | tidak | NULL | |
| selesai_tampil | DATETIME | - | tidak | NULL | |
| status | ENUM(draft,terbit,arsip) | - | ya | draft | |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |
| updated_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### agendas
Agenda akademik (ujian, libur, kegiatan resmi). Indeks: `INDEX(user_id)`, `INDEX(tanggal_mulai)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| user_id | BIGINT(20) | FK | ya | - | Admin pembuat |
| judul | VARCHAR(150) | - | ya | - | |
| deskripsi | TEXT | - | tidak | NULL | |
| tanggal_mulai | DATE | - | ya | - | |
| tanggal_selesai | DATE | - | tidak | NULL | |
| lokasi | VARCHAR(120) | - | tidak | NULL | |
| jenis | ENUM(akademik,umum,ujian,libur) | - | ya | akademik | |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

### Kelompok D — Media Sosial dan Konten

#### categories
Kategori postingan. Indeks: `UNIQUE(slug)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| nama | VARCHAR(60) | - | ya | - | |
| slug | VARCHAR(60) | UK | ya | - | |
| jenis | ENUM(umum,materi,pengumuman,karier) | - | ya | umum | |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### posts
Inti media sosial. Indeks: `INDEX(user_id)`, `INDEX(category_id)`, `INDEX(status, created_at)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| user_id | BIGINT(20) | FK | ya | - | Penulis |
| category_id | BIGINT(20) | FK | tidak | NULL | |
| jenis | ENUM(post,materi,pengumuman,karier) | - | ya | post | |
| isi | TEXT | - | ya | - | |
| media | VARCHAR(255) | - | tidak | NULL | Gambar/video |
| status | ENUM(terbit,ditinjau,disembunyikan) | - | ya | terbit | |
| like_count | INT(11) | - | ya | 0 | Denormalisasi |
| comment_count | INT(11) | - | ya | 0 | Denormalisasi |
| share_count | INT(11) | - | ya | 0 | Denormalisasi |
| view_count | INT(11) | - | ya | 0 | Denormalisasi |
| deleted_at | TIMESTAMP | - | tidak | NULL | Soft delete |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |
| updated_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### comments
Mendukung balasan bertingkat via `parent_id`. Indeks: `INDEX(post_id)`, `INDEX(user_id)`, `INDEX(parent_id)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| post_id | BIGINT(20) | FK | ya | - | |
| user_id | BIGINT(20) | FK | ya | - | |
| parent_id | BIGINT(20) | FK | tidak | NULL | Komentar induk bila balasan |
| isi | TEXT | - | ya | - | |
| status | ENUM(terbit,disembunyikan) | - | ya | terbit | |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### likes
Indeks: `UNIQUE(post_id, user_id)` — mencegah suka ganda.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| post_id | BIGINT(20) | FK | ya | - | |
| user_id | BIGINT(20) | FK | ya | - | |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### shares
Indeks: `INDEX(post_id)`, `INDEX(user_id)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| post_id | BIGINT(20) | FK | ya | - | |
| user_id | BIGINT(20) | FK | ya | - | |
| kanal | VARCHAR(30) | - | tidak | NULL | feed, tautan, luar sistem |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### bookmarks
Relasi opsional ke `posts` atau `jobs`. Indeks: `UNIQUE(user_id, post_id)`, `UNIQUE(user_id, job_id)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| user_id | BIGINT(20) | FK | ya | - | |
| post_id | BIGINT(20) | FK | tidak | NULL | |
| job_id | BIGINT(20) | FK | tidak | NULL | |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### followers
Self-relation pada `users`. Indeks: `UNIQUE(follower_id, following_id)`, `INDEX(following_id)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| follower_id | BIGINT(20) | FK | ya | - | Yang mengikuti |
| following_id | BIGINT(20) | FK | ya | - | Yang diikuti |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### hashtags
Hasil pencarian ditampilkan dalam tab kategori: Lowongan, Siswa, Perusahaan, Tanda Pagar. Indeks: `UNIQUE(tag)`, `INDEX(jumlah_pakai)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| tag | VARCHAR(50) | UK | ya | - | Tanpa simbol # |
| jumlah_pakai | INT(11) | - | ya | 0 | Denormalisasi |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### post_hashtag
Tabel penghubung, kunci gabungan. Indeks: `PRIMARY KEY(post_id, hashtag_id)`, `INDEX(hashtag_id)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| post_id | BIGINT(20) | PK, FK | ya | - | |
| hashtag_id | BIGINT(20) | PK, FK | ya | - | |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

### Kelompok E — Karier, Keahlian, dan Portofolio

#### skills
Indeks: `UNIQUE(nama)`, `INDEX(kategori)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| nama | VARCHAR(60) | UK | ya | - | Contoh: Laravel |
| kategori | VARCHAR(50) | - | tidak | NULL | Pemrograman, desain, jaringan, dst |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### student_skills
Indeks: `UNIQUE(student_id, skill_id)`, `INDEX(skill_id)`, `INDEX(tervalidasi)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| student_id | BIGINT(20) | FK | ya | - | |
| skill_id | BIGINT(20) | FK | ya | - | |
| tingkat | TINYINT(1) | - | ya | 1 | Skala 1-5 |
| tervalidasi | BOOLEAN | - | ya | FALSE | Sudah di-endorse guru |
| updated_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### endorsements
Riwayat validasi keahlian oleh guru. Indeks: `INDEX(student_skill_id)`, `INDEX(teacher_id)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| student_skill_id | BIGINT(20) | FK | ya | - | |
| teacher_id | BIGINT(20) | FK | ya | - | |
| catatan | TEXT | - | tidak | NULL | |
| tanggal | DATE | - | ya | - | |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### portfolios
Karya siswa sebagai bukti kompetensi. Indeks: `INDEX(student_id)`, `INDEX(kategori)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| student_id | BIGINT(20) | FK | ya | - | |
| judul | VARCHAR(150) | - | ya | - | |
| kategori | VARCHAR(50) | - | tidak | NULL | |
| deskripsi | TEXT | - | tidak | NULL | |
| lampiran | VARCHAR(255) | - | tidak | NULL | |
| tautan | VARCHAR(255) | - | tidak | NULL | |
| status | ENUM(terbit,ditinjau) | - | ya | terbit | |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |
| updated_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### companies
Indeks: `UNIQUE(user_id)`, `INDEX(status_verifikasi)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| user_id | BIGINT(20) | FK, UK | ya | - | |
| nama | VARCHAR(150) | - | ya | - | |
| bidang | VARCHAR(100) | - | tidak | NULL | |
| alamat | TEXT | - | tidak | NULL | |
| kota | VARCHAR(80) | - | tidak | NULL | |
| situs | VARCHAR(150) | - | tidak | NULL | |
| logo | VARCHAR(255) | - | tidak | NULL | |
| deskripsi | TEXT | - | tidak | NULL | |
| status_verifikasi | ENUM(menunggu,terverifikasi,ditolak) | - | ya | menunggu | |
| verified_by | BIGINT(20) | FK | tidak | NULL | Admin yang verifikasi |
| verified_at | TIMESTAMP | - | tidak | NULL | |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |
| updated_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### jobs
Indeks: `INDEX(company_id)`, `INDEX(status, created_at)`, `UNIQUE(slug)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| company_id | BIGINT(20) | FK | ya | - | |
| judul | VARCHAR(150) | - | ya | - | |
| slug | VARCHAR(170) | UK | ya | - | |
| deskripsi | TEXT | - | ya | - | |
| lokasi | VARCHAR(120) | - | tidak | NULL | |
| jenis | ENUM(penuh_waktu,paruh_waktu,magang,proyek) | - | ya | penuh_waktu | |
| gaji_min | DECIMAL(12,2) | - | tidak | NULL | |
| gaji_max | DECIMAL(12,2) | - | tidak | NULL | |
| kuota | TINYINT(3) | - | ya | 1 | |
| batas_lamaran | DATE | - | tidak | NULL | |
| status | ENUM(dibuka,ditutup,selesai) | - | ya | dibuka | |
| view_count | INT(11) | - | ya | 0 | Denormalisasi |
| application_count | INT(11) | - | ya | 0 | Denormalisasi |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |
| updated_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### job_skills
Bahan utama perhitungan Job Matching. Indeks: `UNIQUE(job_id, skill_id)`, `INDEX(skill_id)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| job_id | BIGINT(20) | FK | ya | - | |
| skill_id | BIGINT(20) | FK | ya | - | |
| bobot | TINYINT(1) | - | ya | 1 | Tingkat kepentingan |
| wajib | BOOLEAN | - | ya | FALSE | Wajib atau pendukung |

#### applications
Indeks: `UNIQUE(job_id, student_id)`, `INDEX(student_id)`, `INDEX(status)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| job_id | BIGINT(20) | FK | ya | - | |
| student_id | BIGINT(20) | FK | ya | - | |
| skor_kecocokan | DECIMAL(5,2) | - | ya | 0 | Hasil Job Matching (0-100) |
| status | ENUM(tertunda,ditinjau,wawancara,diterima,ditolak) | - | ya | tertunda | |
| pesan | TEXT | - | tidak | NULL | |
| catatan_hrd | TEXT | - | tidak | NULL | |
| reviewed_by | BIGINT(20) | FK | tidak | NULL | |
| reviewed_at | TIMESTAMP | - | tidak | NULL | |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |
| updated_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

### Kelompok F — Algoritma dan Analitik *(dibuat awal: search_logs. Sisanya Fase Lanjutan)*

#### search_logs *(fase MVP)*
Indeks: `INDEX(user_id)`, `INDEX(kata_kunci)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| user_id | BIGINT(20) | FK | tidak | NULL | Boleh kosong untuk pencarian anonim |
| kata_kunci | VARCHAR(150) | - | ya | - | |
| tipe | VARCHAR(30) | - | tidak | NULL | pengguna, lowongan, postingan, tagar |
| jumlah_hasil | INT(11) | - | ya | 0 | |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### user_interactions *(fase lanjutan)*
Indeks: `INDEX(user_id, created_at)`, `INDEX(referensi_tipe, referensi_id)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| user_id | BIGINT(20) | FK | ya | - | |
| jenis | VARCHAR(30) | - | ya | - | view, like, comment, save, apply |
| referensi_tipe | VARCHAR(30) | - | ya | - | post, job, portfolio |
| referensi_id | BIGINT(20) | - | ya | - | |
| bobot | TINYINT(1) | - | ya | 1 | |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### content_metrics *(fase lanjutan)*
Indeks: `UNIQUE(post_id)`, `INDEX(skor_engagement)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| post_id | BIGINT(20) | FK, UK | ya | - | |
| likes | INT(11) | - | ya | 0 | |
| comments | INT(11) | - | ya | 0 | |
| shares | INT(11) | - | ya | 0 | |
| saves | INT(11) | - | ya | 0 | |
| views | INT(11) | - | ya | 0 | |
| skor_engagement | DECIMAL(10,2) | - | ya | 0 | |
| updated_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### trending_topics *(fase lanjutan)*
Indeks: `UNIQUE(hashtag_id, periode)`, `INDEX(skor_tren)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| hashtag_id | BIGINT(20) | FK | ya | - | |
| periode | VARCHAR(20) | - | ya | - | Contoh: 2026-09 |
| volume | INT(11) | - | ya | 0 | |
| growth | DECIMAL(8,2) | - | ya | 0 | Persen dibanding periode sebelumnya |
| engagement | DECIMAL(8,2) | - | ya | 0 | |
| skor_tren | DECIMAL(10,2) | - | ya | 0 | |
| updated_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

#### recommendations *(fase lanjutan)*
Indeks: `INDEX(user_id, tipe)`, `UNIQUE(user_id, tipe, referensi_id)`.

| Field | Tipe | Kunci | Wajib | Bawaan | Keterangan |
| --- | --- | --- | --- | --- | --- |
| id | BIGINT(20) | PK | ya | AUTO_INCREMENT | |
| user_id | BIGINT(20) | FK | ya | - | |
| tipe | VARCHAR(20) | - | ya | - | job atau post |
| referensi_id | BIGINT(20) | - | ya | - | |
| skor | DECIMAL(6,2) | - | ya | 0 | |
| alasan | VARCHAR(200) | - | tidak | NULL | Contoh: sesuai jurusan dan keahlian |
| created_at | TIMESTAMP | - | ya | CURRENT_TIMESTAMP | |

## 6. Ringkasan Relasi dan Aturan Penghapusan

Relasi ke data induk sekolah (jurusan, kelas, guru, siswa) pakai `RESTRICT` supaya riwayat tidak hilang tanpa sengaja. Data turunan (komentar, suka, syarat keahlian) pakai `CASCADE`.

| Relasi | Kardinalitas | Aturan hapus |
| --- | --- | --- |
| roles → users | 1:N | RESTRICT |
| users → profiles | 1:1 | CASCADE jika akun dihapus; RESTRICT jika masih ada konten |
| users → notifications | 1:N | CASCADE jika akun dihapus; RESTRICT jika masih ada konten |
| majors → classes | 1:N | CASCADE |
| teachers → classes (wali kelas) | 1:N | CASCADE |
| classes → students | 1:N | CASCADE |
| majors → students | 1:N | CASCADE |
| users → students | 1:1 | CASCADE jika akun dihapus; RESTRICT jika masih ada konten |
| users → teachers | 1:1 | CASCADE jika akun dihapus; RESTRICT jika masih ada konten |
| users → school_profiles (updated_by) | 1:N | CASCADE jika akun dihapus; RESTRICT jika masih ada konten |
| school_profiles → facilities | 1:N | CASCADE |
| users → announcements | 1:N | CASCADE jika akun dihapus; RESTRICT jika masih ada konten |
| users → agendas | 1:N | CASCADE jika akun dihapus; RESTRICT jika masih ada konten |
| categories → posts | 1:N | RESTRICT |
| posts → comments | 1:N | CASCADE |
| users → comments | 1:N | CASCADE jika akun dihapus; RESTRICT jika masih ada konten |
| posts → likes | 1:N | CASCADE |
| posts → shares | 1:N | CASCADE |
| users → bookmarks | 1:N | CASCADE jika akun dihapus; RESTRICT jika masih ada konten |
| posts → bookmarks | 1:N | CASCADE |
| jobs → bookmarks | 1:N | CASCADE |
| users → followers | 1:N | CASCADE jika akun dihapus; RESTRICT jika masih ada konten |
| posts ↔ hashtags | N:M | RESTRICT |
| students → student_skills | 1:N | CASCADE |
| skills → student_skills | 1:N | CASCADE |
| student_skills → endorsements | 1:N | CASCADE |
| teachers → endorsements | 1:N | CASCADE |
| students → portfolios | 1:N | CASCADE |
| users → companies | 1:1 | CASCADE jika akun dihapus; RESTRICT jika masih ada konten |
| companies → jobs | 1:N | CASCADE |
| jobs → job_skills | 1:N | CASCADE |
| skills → job_skills | 1:N | CASCADE |
| jobs → applications | 1:N | CASCADE |
| students → applications | 1:N | CASCADE |
| companies → applications | 1:N | CASCADE |
| users → search_logs | 1:N | CASCADE jika akun dihapus; RESTRICT jika masih ada konten |
| users → user_interactions | 1:N | CASCADE jika akun dihapus; RESTRICT jika masih ada konten |
| posts → content_metrics | 1:1 | CASCADE |
| hashtags → trending_topics | 1:N | CASCADE |
| users → recommendations | 1:N | CASCADE jika akun dihapus; RESTRICT jika masih ada konten |

## 7. Kesesuaian ERD dan Kode

Seluruh 34 tabel di sini wajib tercermin identik di ERD (Figma/diagram) dan di berkas migration Laravel. Kalau ada penambahan atau pengurangan tabel di kemudian hari, tiga tempat ini — dokumen ini, ERD, dan migration — diperbarui bersamaan di hari yang sama, supaya tidak ada yang tertinggal.

## 8. Urutan Pembuatan Migration

Supaya foreign key tidak gagal karena tabel rujukan belum ada, urutan pembuatan migration ikuti kelompok data: **A → B → C → D → E → search_logs (F sebagian)**. Empat tabel F sisanya (`user_interactions`, `content_metrics`, `trending_topics`, `recommendations`) dibuat saat mulai mengerjakan Fase Lanjutan, bukan di awal.
