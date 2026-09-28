# Coding Standards — CareerSchool

Aturan main development supaya kecepatan menulis kode tidak mengorbankan kecepatan dan pengalaman pengguna. Dokumen ini melengkapi `prd.md` (apa yang dibangun), `database.md` (skema data), dan `design.md` (tampilan) — dokumen ini soal *cara* membangunnya.

## 1. Tech Stack dan Alasan Efektivitasnya

| Lapisan | Teknologi | Kecepatan development | Kecepatan bagi pengguna |
| --- | --- | --- | --- |
| Backend | Laravel 12 (PHP 8) | Eloquent ORM hilangkan SQL manual, Artisan generate boilerplate, Form Request pisahkan validasi dari controller | Routing dan cache konfigurasi Laravel sudah teroptimasi bawaan |
| Interaktivitas | Livewire | Like/comment/notifikasi tanpa nulis endpoint API + JS terpisah, satu bahasa (PHP) dari server sampai interaksi | Update sebagian halaman (partial render), tidak reload penuh, terasa instan |
| Styling | Tailwind CSS | Tidak menulis CSS custom berulang, styling langsung di markup, tidak perlu context-switch ke file lain | CSS akhir kecil karena dipangkas otomatis lewat build (purge unused class) |
| Micro-interaksi | Alpine.js | Dropdown/modal/toggle tanpa build step tambahan | Skrip kecil (<10kb), tidak menambah beban muat halaman |
| Basis data | MySQL + indeks yang direncanakan (`database.md`) | Struktur tabel sudah final di awal, tidak bongkar pasang skema di tengah jalan | Query yang sering dipakai (feed, search, job list) sudah punya indeks dari migration pertama |
| Ikon | Lucide (lewat Blade Icons) | Satu command install, panggil sebagai komponen Blade | SVG inline, tidak ada request gambar terpisah |

Prinsip besarnya: **pilih yang menghilangkan pekerjaan berulang, bukan yang menambah lapisan baru.** Livewire dipilih justru karena mengurangi kerja (tidak perlu API), bukan menambah kerja.

## 2. Prinsip Umum — Berlaku di Semua Lapisan

Empat pertanyaan sebelum menulis kode baru, urut dari atas:

1. **Perlu dibangun sekarang?** Kalau bukan bagian MVP di `prd.md`, tunda. Fitur yang dibangun "siapa tahu nanti kepake" adalah waktu yang hilang.
2. **Sudah ada di project ini?** Cek dulu ada Service, komponen Blade, atau helper yang mirip sebelum bikin baru. Development paling lambat adalah menulis ulang sesuatu yang sudah ada tiga file sebelumnya.
3. **Bisa pakai fitur bawaan Laravel/Livewire/Tailwind?** Jangan pasang package baru untuk hal yang sudah bisa dikerjakan fitur bawaan (contoh: soft delete pakai trait bawaan Eloquent, bukan kolom manual buatan sendiri).
4. **Baru kalau tiga di atas tidak cukup**, tulis kode baru — seminimal mungkin, tanpa abstraksi buat kasus yang belum tentu kepake.

Kode yang jelas lebih penting dari kode yang pendek. Kalau harus pilih antara satu baris yang butuh mikir dua kali dan lima baris yang langsung kebaca, pilih yang lima baris.

## 3. Backend — Laravel

### 3.1 Struktur tanggung jawab (jangan tumpuk semua di Controller)

```
Request masuk
  → Form Request       (validasi input)
  → Controller         (orkestrasi: panggil Service, kembalikan response)
  → Service class       (logika bisnis/algoritma)
  → Model / Eloquent    (akses data)
```

**Controller cuma orkestrasi, bukan tempat logika.** Kalau ada method controller lebih dari kira-kira 20 baris berisi if/else atau perhitungan, itu sinyal logikanya harus pindah ke Service class.

```php
// Hindari: logika Job Matching numpuk di controller
public function show(Job $job)
{
    $student = auth()->user()->student;
    $totalBobot = 0; $bobotTerpenuhi = 0;
    foreach ($job->jobSkills as $js) { /* ...20 baris hitungan... */ }
    return view('siswa.detail-lowongan', compact('job', 'skor'));
}

// Lebih baik: controller memanggil Service
public function show(Job $job, JobMatchingService $matcher)
{
    $skor = $matcher->hitung(auth()->user()->student, $job);
    return view('siswa.detail-lowongan', compact('job', 'skor'));
}
```

### 3.2 Form Request untuk semua input dari pengguna

Jangan validasi manual dengan `if` di controller. Satu Form Request per aksi tulis data (`StorePostRequest`, `ApplyJobRequest`, dst — pola ini sudah ada di struktur folder `database.md`/dokumen tender). Keuntungannya: validasi bisa dites terpisah, controller tetap pendek, dan aturan yang sama tidak ditulis dua kali di tempat berbeda.

### 3.3 Query — cegah N+1 sejak awal

Ini penyebab paling umum halaman kerasa lambat di Laravel pemula. Selalu `with()` relasi yang dipakai di view/loop.

```php
// Lambat: satu query tambahan tiap iterasi (N+1)
$jobs = Job::all();
foreach ($jobs as $job) {
    echo $job->company->nama; // query baru tiap loop
}

// Benar: satu query tambahan untuk semua company sekaligus
$jobs = Job::with('company', 'jobSkills.skill')->paginate(20);
```

Aturan simpel: kalau sebuah tampilan me-loop data dan memanggil relasi di dalam loop, `with()` relasi itu di query awal.

### 3.4 Paginasi wajib untuk semua listing

Feed, daftar lowongan, daftar pelamar, hasil pencarian — semua pakai `paginate()`, tidak pernah `get()`/`all()` untuk data yang bisa tumbuh (posts, applications, jobs). Data demo mungkin cuma puluhan baris, tapi kebiasaan ini yang bikin kode tidak perlu ditulis ulang nanti.

### 3.5 Mass assignment dan keamanan dasar

- Tiap model isi `$fillable` eksplisit, jangan `$guarded = []`. Ini mencegah field seperti `status_verifikasi` atau `role_id` ke-inject dari form yang tidak seharusnya bisa mengubahnya.
- Password selalu lewat `Hash::make()`, tidak pernah disimpan mentah — sudah ditulis di `database.md`, tapi ini pengingat di sisi kode: jangan sampai ada `DB::table('users')->insert()` manual di seeder yang lupa hash password.
- Middleware peran (`RoleMiddleware`, `VerifiedCompanyMiddleware` — sudah ada di struktur folder) dipasang di route, bukan dicek manual `if (auth()->user()->role !== 'admin')` berulang di tiap controller.
- Query yang menerima input pengguna selalu lewat Eloquent/Query Builder (parameter ter-bind otomatis), tidak pernah menyambung string SQL manual.

### 3.6 Service class untuk algoritma

Sesuai `prd.md`, `JobMatchingService` dan `SearchService` dibuat dari fase MVP; `EngagementRankingService`, `RecommendationService`, `TrendingService` menyusul. Tiap Service:
- Satu method publik utama yang jelas namanya (`hitungSkor()`, bukan `process()`).
- Menerima Model sebagai parameter (`Student $student, Job $job`), bukan array mentah — supaya IDE dan pembaca kode tahu bentuk datanya tanpa buka dokumentasi lain.
- Tidak menyentuh `Request` atau `Response` — Service tidak tahu soal HTTP, itu urusan Controller.

## 4. Database dan Migration

### 4.1 Satu migration, satu tabel, dibuat sekali di awal per kelompok

Karena struktur 34 tabel di `database.md` sudah final hasil perencanaan, migration dibuat **sekali per tabel saat pertama kali dibutuhkan**, bukan di-edit bolak-balik. Kalau butuh ubah struktur tabel yang sudah pernah di-migrate dan sudah dipakai (ada data), buat migration baru (`php artisan make:migration tambah_kolom_x_ke_jobs`), jangan edit file migration lama — supaya riwayat migration konsisten dengan riwayat Git dan tidak ada anggota tim (atau kamu sendiri, sesi lain) yang migration-nya "beda versi".

### 4.2 Urutan migration ikut kelompok data

Sesuai `database.md` bagian 8: **A → B → C → D → E → search_logs**, baru empat tabel Kelompok F sisanya belakangan di Fase Lanjutan. Ini bukan sekadar rapi-rapi — migration Laravel dijalankan berurutan sesuai nama file (`2024_01_01_..._create_roles_table.php` sebelum `..._create_users_table.php`), jadi kalau `users` (yang punya FK ke `roles`) filenya lebih dulu, migration **gagal** karena tabel yang dirujuk belum ada. Susun timestamp file migration mengikuti urutan ketergantungan foreign key.

### 4.3 Migration selalu bawa index yang sudah direncanakan

Jangan tambah index belakangan "kalau lambat baru dioptimasi" — index yang sudah dipetakan di `database.md` (misal `INDEX(status, created_at)` di `jobs` dan `posts`) ditulis langsung di migration awal. Menambah index di tabel yang sudah berisi jutaan baris itu mahal; menambahnya sejak baris pertama itu gratis.

### 4.4 Seeder terpisah dari factory

- **Seeder** = data tetap yang memang harus ada (`RoleSeeder` untuk 4 role, `MajorSeeder` untuk daftar jurusan, `SkillSeeder` untuk daftar keahlian).
- **Factory** = data acak untuk simulasi/demo (`PostFactory`, `ApplicationFactory`).

Jangan campur keduanya dalam satu file — kalau nanti perlu reset data demo tapi role/jurusan tetap ada, pemisahan ini yang bikin `php artisan migrate:fresh --seed` aman dijalankan ulang tanpa kehilangan data acuan.

## 5. Frontend — Blade, Livewire, Komponen

### 5.1 Komponen kecil, satu tanggung jawab

Pola composition, bukan satu file besar dengan banyak parameter kondisional:

```blade
{{-- Hindari: satu komponen mengurus semua kondisi lewat prop --}}
<x-card title="Lowongan" variant="job" showScore="true" showApplyButton="true" .../>

{{-- Lebih baik: susun dari komponen kecil yang jelas isinya --}}
<x-card>
    <x-card.header>{{ $job->judul }}</x-card.header>
    <x-badge-score :skor="$skor" />
    <x-card.footer>
        <x-button wire:click="lamar">Lamar</x-button>
    </x-card.footer>
</x-card>
```

Komponen yang sudah direncanakan di struktur folder (`card-post`, `card-job`, `badge-skill`, `badge-score`, `alert`, `modal`, `pagination`) dipakai ulang di semua halaman siswa/guru/admin/perusahaan yang butuh, tidak ditulis ulang per role.

### 5.2 Livewire component fokus satu potongan interaktif

Satu Livewire component menangani satu unit interaksi (tombol like, form komentar, badge notifikasi), bukan satu component raksasa untuk seluruh halaman feed. Ini bikin re-render Livewire tetap kecil dan cepat, dan lebih gampang dites terpisah.

```php
// LikeButton.php — fokus, kecil, jelas tanggung jawabnya
class LikeButton extends Component
{
    public Post $post;
    public bool $sudahDisukai;

    public function mount(Post $post)
    {
        $this->post = $post;
        $this->sudahDisukai = $post->likes()->where('user_id', auth()->id())->exists();
    }

    public function toggle()
    {
        $this->sudahDisukai
            ? $this->post->likes()->where('user_id', auth()->id())->delete()
            : $this->post->likes()->create(['user_id' => auth()->id()]);

        $this->sudahDisukai = !$this->sudahDisukai;
    }
}
```

### 5.3 Selalu tangani tiga kondisi: loading, kosong, error

Jangan biarkan layar putih kosong. Tiap listing (feed, hasil pencarian, daftar pelamar) punya tiga kondisi ini secara eksplisit di Blade:

```blade
@if ($jobs->isEmpty())
    <x-empty-state
        judul="Belum ada lowongan"
        pesan="Lowongan yang cocok dengan keahlianmu akan muncul di sini."
    />
@else
    @foreach ($jobs as $job)
        <x-card-job :job="$job" />
    @endforeach
    {{ $jobs->links() }}
@endif
```

Livewire punya `wire:loading` bawaan untuk state loading — pakai itu, jangan bikin spinner manual dengan JavaScript tambahan.

### 5.4 Ikuti design.md, jangan improvisasi warna/spacing baru

Semua kelas warna dan ukuran pakai token yang sudah ditentukan (`brand-primary`, `text-secondary`, skala spacing 4px), bukan hex atau angka pixel baru yang dikarang di tengah coding. Kalau butuh warna yang belum ada di `design.md`, tambahkan dulu di sana sebagai token, baru dipakai di kode — supaya `design.md` selalu jadi sumber kebenaran tunggal, tidak ada warna "liar" yang cuma ada di satu file Blade.

### 5.5 Aksesibilitas dasar (jangan dilewat karena terburu-buru)

- Elemen yang bisa diklik pakai `<button>` atau `<a>`, bukan `<div onclick>` — supaya otomatis bisa dijangkau keyboard dan terbaca screen reader.
- Ikon tanpa teks (tombol like, tombol hapus) selalu punya `aria-label`.
- Input form selalu punya `<label>` yang terhubung lewat `for`/`id`, placeholder bukan pengganti label.
- Kontras warna ikuti aturan di `design.md` (teks di atas lime harus gelap) — ini bukan cuma soal estetika, tapi soal keterbacaan.

## 6. Git dan Kontrol Versi

- Commit kecil dan fokus satu perubahan. Commit message berbentuk perintah singkat: `Tambah validasi form lamaran`, bukan `update` atau `fix`.
- Satu fitur = satu branch (`fitur/job-matching`, `fitur/verifikasi-perusahaan`), digabung ke `main` setelah fitur itu jalan dan diuji manual, supaya `main` selalu dalam kondisi bisa didemokan.
- Migration, seeder, dan perubahan struktur tabel di-commit terpisah dari perubahan tampilan, supaya kalau ada masalah gampang dilacak commit mana penyebabnya.
- Jangan commit file `.env` (kunci, password database) — sudah ada `.env.example` di struktur folder sebagai contoh, isi sebenarnya biarkan lokal.

## 7. Checklist Sebelum Anggap Satu Fitur "Selesai"

Dicek sendiri sebelum lanjut ke fitur berikutnya — bukan formalitas, tapi supaya bug tidak menumpuk di akhir mendekati deadline.

**Kebenaran**
- [ ] Berjalan sesuai kebutuhan fungsional di `prd.md` untuk role terkait
- [ ] Kasus kosong ditangani (data belum ada, hasil pencarian nihil)
- [ ] Kasus tidak valid ditangani (form disubmit kosong, angka negatif, lamar dua kali ke job yang sama)

**Struktur**
- [ ] Tidak ada logika bisnis yang nyasar di Controller atau Blade — sudah di Service/Model
- [ ] Tidak ada query yang sama disalin-tempel di lebih dari satu tempat — sudah jadi method/scope di Model
- [ ] Nama variabel dan method jelas, bukan `$data`, `$temp`, `$result` tanpa konteks

**Keamanan**
- [ ] Input tervalidasi lewat Form Request
- [ ] Route dibungkus middleware peran yang benar
- [ ] Tidak ada query SQL mentah dari input pengguna langsung

**Performa**
- [ ] Relasi yang dipakai di loop sudah di-`with()`
- [ ] Listing pakai `paginate()`, bukan `all()`/`get()` tanpa batas

**Tampilan**
- [ ] Sudah dicek di lebar mobile dan desktop
- [ ] Loading, kosong, dan error state ada semua
- [ ] Warna dan spacing dari token `design.md`, bukan hex/angka baru

## 8. Ringkasan Kecepatan

| Fokus | Cara mencapainya di proyek ini |
| --- | --- |
| Development cepat | Reuse komponen dan Service yang sudah ada, Form Request bawaan, migration sekali jalan per kelompok tabel, tidak menulis abstraksi buat kasus yang belum tentu kepake |
| Pengguna cepat | Livewire partial update (bukan reload penuh), index database sesuai pola akses (`database.md`), pagination di semua listing, N+1 dicegah sejak query pertama ditulis |
| Kode gampang dilanjut sesi lain | Struktur folder konsisten (Controller/Service/Model terpisah jelas), commit kecil dan bertahap, semua keputusan desain/data/fitur tercatat di tiga dokumen pendamping (`prd.md`, `database.md`, `design.md`) |
