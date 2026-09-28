# Design Guide — CareerSchool

Panduan desain untuk Paket 15 CareerSchool. Dokumen ini jadi acuan tunggal soal warna, tipografi, ikon, layout, komponen, animasi, dan gaya tulisan, supaya tiap halaman (siswa, guru, admin, perusahaan) konsisten walau dikerjakan satu orang dalam banyak sesi berbeda.

## 1. Arah Desain

Tiga kata kunci yang jadi pegangan tiap keputusan visual:

- **Rounded** — semua sudut membulat: card, tombol, input, badge, bahkan ikon. Tidak ada sudut tajam 90°.
- **Minimalist** — satu warna aksen per konteks, whitespace lega, elemen dekoratif seminimal mungkin.
- **Approachable** — target pengguna siswa SMK, jadi tampilannya harus terasa ringan dan tidak kaku seperti sistem informasi sekolah pada umumnya, tapi tetap kredibel untuk sisi Perusahaan dan Admin.

Panel Admin dan Perusahaan memakai sistem yang sama, tapi elemen playful (ilustrasi tangan, badge pil besar) dikurangi. Fokusnya kejelasan data, bukan promosi.

## 2. Warna

### 2.1 Primitives

Satu daftar warna mentah. Tidak pernah dipakai langsung di komponen — selalu lewat token di bagian 2.2.

| Name | Value |
| --- | --- |
| white | `#FFFFFF` |
| black | `#000000` |
| neutral/50 | `#F7F8F3` |
| neutral/100 | `#EDEEE8` |
| neutral/300 | `#C4C5BE` |
| neutral/400 | `#A0A0A0` |
| neutral/600 | `#6B6B6B` |
| neutral/700 | `#4A4A4A` |
| neutral/800 | `#333333` |
| neutral/900 | `#1E1E1E` |
| neutral/950 | `#121212` |
| lime/100 | `#F0FBD4` |
| lime/500 | `#C2F970` |
| lime/700 | `#7DA82E` |
| lime/900 | `#5A7A2B` |
| forest/100 | `#D1EAE0` |
| forest/300 | `#3FA07D` |
| forest/500 | `#166E4F` |
| forest/900 | `#083D2D` |
| success/100 | `#DCFCE7` |
| success/700 | `#15803D` |
| success/900 | `#14532D` |
| danger/100 | `#FEE2E2` |
| danger/700 | `#B91C1C` |
| danger/900 | `#7F1D1D` |
| warning/100 | `#FEF3C7` |
| warning/700 | `#B45309` |
| warning/900 | `#78350F` |
| info/100 | `#DBEAFE` |
| info/700 | `#1D4ED8` |
| info/900 | `#1E3A8A` |

### 2.2 Token — Light & Dark

| Token | Light | Dark |
| --- | --- | --- |
| surface/bg | neutral/50 | neutral/950 |
| surface/card | white | neutral/900 |
| surface/elevated | white | neutral/800 |
| text/primary | neutral/900 | neutral/50 |
| text/secondary | neutral/600 | neutral/400 |
| text/brand | lime/700 | lime/500 |
| text/on-brand-primary | neutral/900 | neutral/900 |
| text/on-brand-secondary | white | neutral/900 |
| border/default | neutral/400 | neutral/700 |
| border/subtle | neutral/100 | neutral/800 |
| brand/primary | lime/500 | lime/500 |
| brand/secondary | forest/500 | forest/300 |
| state/success-bg | success/100 | success/900 |
| state/success-text | success/900 | success/100 |
| state/danger-bg | danger/100 | danger/900 |
| state/danger-text | danger/900 | danger/100 |
| state/warning-bg | warning/100 | warning/900 |
| state/warning-text | warning/900 | warning/100 |
| state/info-bg | info/100 | info/900 |
| state/info-text | info/900 | info/100 |

### 2.3 Aturan pakai warna

- Satu warna, satu arti. `brand/primary` selalu berarti "bisa diklik/aksi utama". Jangan pakai lime di teks statis yang bukan aksi, karena itu menyesatkan.
- Satu aksi utama per layar. Tombol dengan fill `brand/primary` cuma satu per halaman/section. Tombol lain jadi outline atau ghost.
- Tombol dengan background `brand/primary` (lime/500) wajib pakai teks `text/on-brand-primary` (gelap). Jangan pernah teks putih di atas lime — kontrasnya gagal.
- Badge status pakai pasangan `state/*-bg` + `state/*-text` dari grup yang sama. Jangan campur, misal `success-bg` dengan `danger-text`.
- Mode gelap dibuat manual per token (bukan filter invert), makanya value dark di atas sudah dihitung ulang per token, bukan sekadar dibalik.

## 3. Tipografi

| Peran | Font | Sumber |
| --- | --- | --- |
| Judul / Heading | Sora | Google Fonts |
| Body / UI text | Plus Jakarta Sans | Google Fonts |

### 3.1 Skala ukuran

| Nama | Ukuran | Line-height | Weight | Pakai untuk |
| --- | --- | --- | --- | --- |
| display | 36px | 44px | Sora 700 | Hero landing page |
| h1 | 28px | 36px | Sora 700 | Judul halaman |
| h2 | 22px | 30px | Sora 600 | Judul section |
| h3 | 18px | 26px | Sora 600 | Judul card/komponen |
| body-lg | 16px | 24px | Jakarta 400 | Paragraf utama |
| body | 14px | 20px | Jakarta 400 | Teks standar UI |
| caption | 12px | 16px | Jakarta 500 | Label, timestamp, metadata |
| button | 14px | 20px | Jakarta 600 | Label tombol |

### 3.2 Aturan

- Jangan pakai lebih dari 2 font family. Semua variasi datang dari weight, bukan font baru.
- Body text minimal 14px. Di bawah itu keterbacaan mobile turun drastis.
- Satu kebijakan kapitalisasi: **sentence case** untuk semua judul, tombol, dan label ("Buat postingan", bukan "Buat Postingan"). Konsisten di semua halaman.
- Angka penting (skor kecocokan, statistik) pakai tabular figures kalau tersedia di font, supaya angka sejajar rapi di tabel.

## 4. Ikon

**Library: Lucide Icons** — satu library dipakai di semua halaman, jangan campur dengan icon set lain.

| Aturan | Nilai |
| --- | --- |
| Stroke width di samping teks regular (400) | 1.5px |
| Stroke width di samping teks semibold (600), misal tombol | 2px |
| Ukuran default UI | 20px |
| Ukuran ikon navigasi/sidebar | 24px |
| Warna | `currentColor`, ambil dari token teks sekitarnya |

- Icon pakai satu SVG yang di-recolor lewat CSS, bukan file terpisah untuk tiap state (hover/aktif/disabled).
- Varian outline jadi default. Varian filled dipakai khusus untuk menandai state aktif (misal ikon Home terisi saat sedang di halaman Beranda).
- Ikon harus align optik, bukan cuma geometris — ikon asimetris (misal ikon play/kirim) sering perlu digeser manual 1-2px biar terasa center.

## 5. Layout & Spacing

### 5.1 Skala spacing

Pakai kelipatan 4px: `4, 8, 12, 16, 24, 32, 48, 64`.

| Konteks | Nilai |
| --- | --- |
| Jarak dalam satu grup (misal ikon ke label) | 8px |
| Jarak antar grup berbeda | minimal 16px (2× jarak intra-grup) |
| Jarak antar kontrol berbatas/berwarna (tombol-ke-tombol) | 12px |
| Jarak antar kontrol tanpa border (ikon-only, link) | 24px |
| Margin inline konten mobile | 16px |
| Margin inline konten desktop | 24-32px |

### 5.2 Prinsip

- Kelompokkan dengan jarak dulu, baru background shape, baru garis pembatas — garis cuma dipakai kalau jarak saja belum cukup jelas.
- Urutan tampil ikuti urutan pentingnya konten: dari atas ke bawah, dari sisi mulai (leading) ke sisi akhir (trailing). Jangan asumsikan "kiri-kanan" karena harus tetap benar kalau suatu saat butuh dukungan RTL.
- Breakpoint mengikuti konten, bukan angka baku 768/1024. Pecah layout persis di titik kontennya mulai terlihat sempit, dites langsung di device/browser, bukan ditebak dari template.
- Konten dan gambar boleh melebar penuh sampai tepi layar. Tombol dan teks tetap punya margin aman, tidak pernah menempel ke tepi.
- Tombol utama pada form/flow penting (misal tombol Apply) tidak boleh diletakkan di posisi yang gampang ke-scroll atau ke-clip; gunakan sticky footer kalau perlu.

## 6. Border Radius

Aturan **concentric radius**: radius luar = radius dalam + padding, supaya sudut nested elements terasa "senada", bukan asal beda-beda.

| Elemen | Radius |
| --- | --- |
| Card besar (post, job listing) | 16px |
| Card kecil / input field | 10px |
| Tombol | 10px |
| Badge / pill (status, hashtag) | full (9999px) |
| Avatar | full (lingkaran) |
| Modal | 20px |

Kalau sebuah card berisi tombol dengan padding 8px, dan radius card 16px, maka radius tombol di dalamnya idealnya 16 - 8 = 8px, bukan asal 10px flat semua. Sesuaikan per kasus, tabel di atas adalah default umum.

## 7. Komponen Inti

### 7.1 Tombol

| Varian | Fill | Teks | Kapan dipakai |
| --- | --- | --- | --- |
| Primary | `brand/primary` | `text/on-brand-primary` | Satu aksi utama per layar (Apply, Simpan, Posting) |
| Secondary | `brand/secondary` | `text/on-brand-secondary` | Aksi kedua yang tetap penting (misal "Lihat Profil") |
| Outline | transparan, border `border/default` | `text/primary` | Aksi netral (Batal, Kembali) |
| Ghost | transparan, tanpa border | `text/secondary` | Aksi minor (Lihat Semua, Skip) |
| Danger | `danger/700` | white | Aksi merusak (Hapus, Tolak Lamaran) |

Semua tombol pakai state tekan `scale(0.96)` — lihat bagian Motion.

### 7.2 Card

- Background `surface/card`, radius 16px, tanpa border kalau di atas `surface/bg` yang beda tone (kontras sudah cukup dari warna).
- Kalau card ditumpuk di atas card lain (misal card di dalam modal), pakai `surface/elevated` supaya beda level kelihatan.
- Shadow dipakai untuk efek elevasi, border dipakai untuk struktur (misal pembatas antar item list). Jangan pakai keduanya sekaligus di elemen yang sama tanpa alasan.

### 7.3 Badge status

Dipetakan dari algoritma/status di database, supaya development gampang mapping-nya:

| Status | Token |
| --- | --- |
| Job `open`, Lamaran `accepted`, Company `approved` | `state/success-*` |
| Job `closed`, Lamaran `rejected` | `state/danger-*` |
| Lamaran `pending`, Company `pending` | `state/warning-*` |
| Notifikasi umum | `state/info-*` |

### 7.4 Input & Form

- Border radius 10px, border 1px `border/default`, jadi `brand/primary` saat fokus.
- Label selalu terlihat di atas field, placeholder cuma jadi contoh format (misal `08xxxxxxxxxx`), tidak pernah jadi satu-satunya label.
- Pesan error muncul tepat di bawah field yang gagal, bukan di atas atau di popup terpisah, dan isinya instruksi cara benerin — lihat bagian 9.

## 8. Motion & Animasi

| Interaksi | Spesifikasi |
| --- | --- |
| Tekan tombol | `scale(0.96)`, transisi 150ms |
| Hover warna/opacity | transisi 150ms atau kurang |
| Toggle ikon (misal like solid/outline) | fade + scale 0.25→1, opacity 0→1, 150-200ms |
| Buka modal/dropdown | fade + translateY kecil (8-12px) saat masuk |
| Tutup modal/dropdown | translateY lebih kecil dari saat masuk, terasa lebih halus |
| Ganti tema (light/dark) | matikan semua transisi sesaat saat toggle, supaya tidak terjadi efek "smear" di seluruh layar |

Aturan umum: transisi selalu sebut properti spesifik (`transition: transform, opacity`), jangan `transition: all`. Setiap perubahan state yang dianimasikan tetap harus punya penanda statis (warna/ikon/label) — animasi tidak boleh jadi satu-satunya sinyal.

## 9. Copywriting / Gaya Tulisan

Semua teks antarmuka berbahasa Indonesia, sentence case, langsung ke tujuan.

- **Tombol pakai kata kerja**: "Simpan Perubahan", bukan "OK" atau "Lanjut" yang ambigu. Satu istilah dipakai konsisten di seluruh alur — kalau di satu tempat disebut "Lamar", jangan di tempat lain disebut "Ajukan".
- **Sapa pengguna langsung**: "Kamu belum punya portofolio" lebih baik dari "Pengguna belum memiliki portofolio".
- **Error kasih solusi, bukan cuma nyalahin**: "Ukuran file terlalu besar. Maksimal 2MB" lebih baik dari "Upload gagal".
- **Empty state kasih arah**: tampilkan apa itu, kenapa kosong, dan satu tombol aksi. Contoh: "Belum ada lamaran" + "Lamaran yang kamu kirim akan muncul di sini" + tombol "Cari Lowongan".
- **Toggle dijelaskan lewat kondisi ON**: "Terima notifikasi email", bukan "Jangan kirim email".
- Tidak ada tanda seru berlebihan, tidak ada "Oops!", terutama di pesan error atau konfirmasi hapus data.

## 10. Dark Mode & Light Mode

- Switching pakai satu mekanisme saja: class `dark` di elemen root (`<html class="dark">`), diatur lewat toggle yang disimpan preferensinya (localStorage per browser, bukan disinkronkan ke akun).
- Semua styling warna wajib lewat token di bagian 2.2, tidak ada hex hardcode di komponen. Ini supaya ganti tema tidak perlu sentuh tiap file.
- Saat pertama load, cek preferensi tersimpan dulu; kalau belum ada, ikuti `prefers-color-scheme` sistem sebagai default.

## 11. Checklist sebelum anggap satu halaman "selesai"

- [ ] Semua warna dari token, bukan hex manual
- [ ] Satu tombol primary per layar/section
- [ ] Radius nested konsisten (card, tombol, badge di dalamnya senada)
- [ ] Icon dari Lucide, stroke width sesuai teks di sampingnya
- [ ] Spacing ikut skala 4px, jarak antar grup ≥ 2× jarak dalam grup
- [ ] Teks sentence case, tombol pakai kata kerja
- [ ] Sudah dicek tampilan light dan dark
- [ ] Sudah dicek di lebar mobile sempit dan desktop lebar
