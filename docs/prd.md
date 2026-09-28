# PRD — CareerSchool

Paket 15, PKWU XI PPLG, SMKN 1 Cirebon. Disusun berdasarkan Kerangka Acuan Kerja (KAK) SMART SCHOOL SOCIAL PLATFORM dan dokumen tender CV. Gurat Cipta Mandiri. Dokumen ini jadi acuan tunggal soal fitur apa yang dibangun, buat siapa, dan urutan prioritasnya, supaya keputusan development tidak berubah-ubah di tengah jalan.

## 1. Ringkasan Produk

CareerSchool adalah platform karier sekolah yang sekaligus berfungsi sebagai media sosial. Bedanya dari papan lowongan digital biasa: keahlian siswa divalidasi guru, karya siswa terdokumentasi sebagai bukti, dan setiap lowongan punya syarat keahlian yang dihitung skor kecocokannya terhadap profil siswa. Kriteria keberhasilan utama sesuai KAK: sistem mampu menampilkan persentase kecocokan antara kandidat dan lowongan.

## 2. Masalah yang Diselesaikan

| Kode | Masalah | Dampak jika dibiarkan |
| --- | --- | --- |
| M-01 | Informasi karier tersebar dan tidak terpusat (papan pengumuman, grup chat, selebaran) | Peluang kerja terlewat |
| M-02 | Keahlian siswa tidak terdokumentasi dan tidak terverifikasi | Kompetensi siswa sulit dibuktikan ke perusahaan |
| M-03 | Portofolio karya siswa tidak terkelola | Karya siswa tidak terlihat sebagai bukti kompetensi |
| M-04 | Belum ada pencocokan keahlian dengan syarat lowongan | Penyaringan pelamar lambat dan tidak tepat sasaran |
| M-05 | Perusahaan luar sekolah tidak terverifikasi | Risiko informasi tidak sah bagi siswa |
| M-06 | Data siswa tidak terhubung dengan aktivitas karier | Pelaporan capaian karier sekolah sulit dilakukan |
| M-07 | Konten tidak berurutan menurut relevansi | Informasi penting tenggelam |

## 3. Target Pengguna

Empat peran aktif: **Siswa**, **Guru**, **Admin Sekolah**, **Perusahaan**.

| Peran | Tujuan utama | Kebutuhan inti |
| --- | --- | --- |
| Siswa | Menemukan lowongan yang cocok dan menunjukkan karyanya | Profil keahlian, portofolio, skor kecocokan, status lamaran |
| Guru | Menyalurkan siswa kompeten, mengakui keahlian yang benar dikuasai | Validasi keahlian (endorse), berbagi lowongan, moderasi konten sendiri |
| Admin Sekolah | Menjaga keakuratan data dan ketertiban kanal resmi | Data master, verifikasi perusahaan, moderasi, dasbor analitik |
| Perusahaan | Menemukan kandidat sesuai syarat dengan waktu penyaringan singkat | Kelola lowongan, daftar pelamar berskor, tinjau kandidat |

Contoh skenario pakai (dari user persona): Rifqi (siswa) membuka notifikasi lowongan berskor 82%, membaca detailnya, melamar, lalu memantau status. Pak Bambang (guru) memvalidasi keahlian Laravel milik Rifqi, lalu membagikan lowongan ke siswa bimbingannya. Bu Sari (admin) memverifikasi dua perusahaan baru dan menghapus satu komentar spam. Dina (staf HRD) memasang lowongan bersyarat keahlian, meninjau tiga kandidat berskor tertinggi, menandai dua untuk wawancara.

## 4. Ruang Lingkup

**Termasuk:** analisis kebutuhan dan solusi, desain UI/UX (sitemap, user flow, wireframe, mockup), perancangan dan pembangunan basis data, pemrograman seluruh modul dengan autentikasi multi-peran, algoritma pencocokan/ranking/rekomendasi/tren, pengujian dan perbaikan bug, dokumentasi, pelatihan admin.

**Di luar lingkup:** penerapan di luar lingkungan sekolah, domain dan hosting berbayar, integrasi dengan Dapodik atau basis data sekolah yang sudah berjalan, aplikasi mobile asli (produk berbentuk web responsif), data perusahaan dan lowongan nyata (memakai data simulasi), pemeliharaan berkala berbayar setelah masa garansi.

## 5. Fitur — Dibagi Fase MVP dan Fase Lanjutan

Pembagian fase supaya jelas urutan kerja: fase MVP harus stabil dulu sebelum fase lanjutan disentuh.

### 5.1 Fase MVP

| Modul | Fitur |
| --- | --- |
| Akun dan profil | Registrasi, login, logout, 4 role, aktivasi siswa dengan NISN, profil dengan bio dan foto |
| Keahlian dan portofolio | Daftar keahlian per siswa, validasi (endorse) oleh guru, unggah portofolio berkategori |
| Media sosial inti | Feed (urut kronologis), postingan CRUD, like, comment, follow, tanda pagar |
| Pencarian | Pencarian terpadu dengan hasil dalam tab kategori: Lowongan, Siswa, Perusahaan, Tanda Pagar. Perangkingan versi sederhana: keyword match dan relevansi dasar |
| Karier | Lowongan (CRUD oleh perusahaan), syarat keahlian berbobot, status lowongan (dibuka/ditutup) |
| Job Matching | Skor kecocokan 0-100 antara profil siswa dan syarat lowongan (rincian di bagian 6) |
| Lamaran | Siswa melamar (satu kali per lowongan), memantau status, perusahaan meninjau dan mengubah status |
| Verifikasi perusahaan | Pendaftaran perusahaan, status menunggu/terverifikasi/ditolak oleh admin, perusahaan belum terverifikasi tidak bisa memasang lowongan |
| Notifikasi | Like, comment, follow baru, perubahan status lamaran, hasil validasi keahlian |
| Data master sekolah | Jurusan, kelas, siswa, guru, profil sekolah (satu baris data, tampil di landing page) |
| Moderasi | Admin dan guru bisa menghapus postingan/komentar bermasalah |
| Admin dasar | Kelola data master, verifikasi perusahaan, moderasi, hak akses |

### 5.2 Fase Lanjutan

Dikerjakan setelah seluruh fitur MVP berjalan stabil dan sudah melalui pengujian dasar.

| Modul | Fitur | Alasan ditunda |
| --- | --- | --- |
| Engagement Ranking | Feed diurutkan berdasar skor interaksi berbobot, bukan cuma kronologis | Butuh volume interaksi dulu supaya skor bermakna |
| Recommendation Engine | Rekomendasi lowongan dan konten berbasis minat dan riwayat interaksi | Rawan cold start kalau data masih sedikit |
| Trending Engine | Deteksi tanda pagar dan topik yang sedang naik | Sama seperti Recommendation, butuh volume data |
| Dasbor Analitik lanjutan | Statistik pengguna, konten, dan karier yang lebih dalam dari dasbor dasar admin | Nice-to-have, bukan penentu kriteria keberhasilan utama KAK |

## 6. Algoritma Job Matching (Inti Produk)

Skor kecocokan dihitung dari lima komponen dengan bobot tetap, supaya hasilnya bisa diperiksa ulang secara manual:

```
Skor = 0,60 × keahlian + 0,15 × jurusan + 0,10 × tingkat + 0,10 × portofolio + 0,05 × lokasi
```

| Komponen | Cara hitung |
| --- | --- |
| Keahlian (60%) | (total bobot keahlian yang dimiliki siswa dari syarat lowongan) ÷ (total bobot seluruh syarat) × 100 |
| Jurusan (15%) | 100 jika jurusan siswa sesuai lowongan, 0 jika tidak |
| Tingkat (10%) | (rata-rata tingkat penguasaan keahlian yang dimiliki ÷ 5) × 100 |
| Portofolio (10%) | minimum(jumlah karya terkait ÷ 3, 1) × 100 |
| Lokasi (5%) | 100 jika siswa bersedia ditempatkan di lokasi lowongan, 0 jika tidak |

Contoh perhitungan:

| Kandidat | Keahlian dimiliki | Skor |
| --- | --- | --- |
| Rifqi | Laravel dan Tailwind dimiliki, MySQL belum | 83,0 |
| Salsa | Laravel, Tailwind, dan MySQL dimiliki semua | 91,0 |

Daftar keahlian yang belum dimiliki siswa ikut dikembalikan oleh sistem, ditampilkan sebagai saran belajar — bukan cuma angka skor kosong tanpa arah.

## 7. Kebutuhan Fungsional per Peran

### Siswa
1. Mendaftar dan mengaktifkan akun dengan NISN
2. Mengubah profil, bio, dan daftar keahlian
3. Mengunggah dan mengelola portofolio karya
4. Membuat, mengubah, menghapus postingan
5. Menyukai, mengomentari, membagikan, dan menyimpan konten
6. Mencari pengguna, lowongan, dan tanda pagar
7. Melihat skor kecocokan pada detail lowongan
8. Melamar dan memantau status lamaran

### Guru
1. Masuk memakai akun yang dibuat admin
2. Mengunggah materi belajar atau pengumuman
3. Mengubah dan menghapus unggahan sendiri
4. Menghapus komentar spam pada postingannya
5. Memvalidasi keahlian siswa
6. Membagikan lowongan kepada siswa
7. Melihat dasbor ringkasan pribadi
8. Menerima notifikasi pengikut dan komentar

### Admin Sekolah
1. Masuk ke dasbor administrator
2. Mengelola data master jurusan, kelas, siswa, guru
3. Memverifikasi pendaftaran perusahaan
4. Memperbarui profil sekolah dan pengumuman resmi
5. Memoderasi postingan dan komentar
6. Mengelola akun dan hak akses
7. Melihat dasbor analitik aktivitas

### Perusahaan
1. Mendaftar dan menunggu verifikasi admin
2. Mengelola profil perusahaan
3. Memasang lowongan beserta syarat keahlian
4. Melihat daftar pelamar beserta skor kecocokan
5. Meninjau keahlian dan portofolio kandidat
6. Mengubah status lamaran
7. Berinteraksi pada feed dan pencarian

## 8. Kebutuhan Non-Fungsional

| Aspek | Kebutuhan | Cara pembuktian |
| --- | --- | --- |
| Kinerja | Halaman utama terbuka di bawah tiga detik pada jaringan sekolah | Pengukuran waktu muat |
| Responsif | Tata letak menyesuaikan ponsel, tablet, dan desktop | Pengujian tiga lebar layar |
| Keamanan | Kata sandi ter-hash, kendali akses per peran, validasi masukan | Peninjauan kode dan uji lintas peran |
| Upload berkas | Gambar jpg/png maksimal 2MB, dokumen pdf maksimal 5MB | Uji unggah berkas melebihi batas |
| Keterpeliharaan | Struktur kode mengikuti pola MVC dan terdokumentasi | Peninjauan struktur folder |
| Keterjelasan algoritma | Setiap skor bisa dilacak asal perhitungannya | Perhitungan manual dibandingkan hasil sistem |
| Bahasa | Seluruh antarmuka berbahasa Indonesia | Peninjauan teks antarmuka |

## 9. Akun Demo (untuk presentasi dan pengujian)

Belum ada di dokumen tender, ditambahkan di sini sebagai kebutuhan operasional sebelum demo ke guru pembimbing.

| Role | Email | Catatan |
| --- | --- | --- |
| Admin | admin@demo.com | Akses penuh dasbor admin |
| Guru | guru@demo.com | Punya siswa bimbingan dengan keahlian belum divalidasi, untuk demo endorsement |
| Siswa | siswa@demo.com | Sudah punya beberapa keahlian dan satu portofolio |
| Perusahaan | perusahaan@demo.com | Sudah terverifikasi, punya minimal satu lowongan aktif |

Password sama untuk semua akun demo, dicatat di User Manual, bukan di dokumen publik.

## 10. Tahapan Pengembangan (Ringkas)

Delapan tahap, 280 jam kerja, 13 minggu. Detail RAB dan jadwal per minggu ada di dokumen tender terpisah.

| Tahap | Fokus |
| --- | --- |
| S1 | Analisis dan Perencanaan |
| S2 | Perancangan Antarmuka (UI/UX) |
| S3 | Perancangan Basis Data |
| S4 | Pengembangan Backend Inti |
| S5 | Pengembangan Antarmuka dan Integrasi |
| S6 | Pengembangan Modul Karier dan Algoritma (MVP dulu, baru Fase Lanjutan) |
| S7 | Pengujian dan Perbaikan Bug |
| S8 | Dokumentasi, Pelatihan, dan Serah Terima |

## 11. Rencana Pengujian

Mengacu KAK Bab 14, minimal 15 test case mencakup: Register, Login, Logout, Profile, Create Post, Edit Post, Delete Post, Like, Comment, Follow, Search, Hashtag, Job Matching, Apply Job, Responsive. Fungsi kritis (autentikasi, peran, pelamaran, pencocokan) juga ditulis sebagai automated feature test (PHPUnit), bukan cuma manual checklist.

## 12. Kriteria Keberhasilan (Acceptance Criteria)

Produk dinyatakan siap demo apabila:

- [ ] Keempat role bisa registrasi/aktivasi dan login sesuai alurnya masing-masing
- [ ] Siswa bisa melengkapi profil, keahlian, dan mengunggah portofolio
- [ ] Guru bisa memvalidasi keahlian siswa
- [ ] Perusahaan terverifikasi bisa memasang lowongan dengan syarat keahlian
- [ ] Sistem menampilkan skor kecocokan 0-100 pada detail lowongan, dengan komponen yang bisa dijelaskan
- [ ] Siswa bisa melamar (satu kali per lowongan) dan memantau status
- [ ] Perusahaan bisa melihat daftar pelamar terurut skor dan mengubah status lamaran
- [ ] Feed, like, comment, follow, tanda pagar, dan pencarian tab kategori berjalan
- [ ] Admin bisa mengelola data master, memverifikasi perusahaan, dan memoderasi konten
- [ ] Tampilan responsif di lebar mobile dan desktop
- [ ] Data demo sesuai KAK Bab 8 tersedia (minimal 20 users, 30 posts, dan seterusnya)

## 13. Daftar Istilah

| Istilah | Arti |
| --- | --- |
| NISN | Nomor Induk Siswa Nasional, dipakai sebagai kunci aktivasi akun siswa |
| Endorsement | Validasi keahlian siswa oleh guru kejuruan |
| Job Matching | Algoritma penghitung skor kecocokan siswa terhadap syarat lowongan |
| MVP | Fitur minimum yang wajib jalan sebelum fitur lanjutan dikerjakan |
| KAK | Kerangka Acuan Kerja, dokumen kebutuhan dari guru PKWU selaku client |
