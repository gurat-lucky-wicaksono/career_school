<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Job;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\Skill;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    /**
     * Perform global search across all school & industry modules.
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim($request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([
                'success' => true,
                'query' => $q,
                'groups' => [],
            ]);
        }

        $results = [];

        // 1. Navigation Pages
        $pages = [
            ['title' => 'Dashboard Utama', 'desc' => 'Ringkasan statistik & metrik sekolah', 'url' => route('admin.dashboard'), 'icon' => 'layout-grid', 'keywords' => 'dashboard beranda statistik metrik ringkasan'],
            ['title' => 'Data Guru & Pendidik', 'desc' => 'Kelola direktori pendidik dan wali kelas', 'url' => route('admin.teachers.index'), 'icon' => 'user-check', 'keywords' => 'guru pendidik guru wali kelas pengajar nip'],
            ['title' => 'Data Siswa & Portofolio', 'desc' => 'Kelola direktori siswa dan status keaktifan', 'url' => route('admin.students.index'), 'icon' => 'graduation-cap', 'keywords' => 'siswa murid peserta didik alumni nisn'],
            ['title' => 'Konsentrasi Keahlian (Jurusan)', 'desc' => 'Program keahlian SMK Negeri 1 Cirebon', 'url' => route('admin.majors.index'), 'icon' => 'book-open', 'keywords' => 'jurusan keahlian pplg dkv tjkt tkro titl toi program kompetensi'],
            ['title' => 'Rombongan Belajar (Kelas)', 'desc' => 'Tingkat dan rombel per tahun ajaran', 'url' => route('admin.classes.index'), 'icon' => 'school', 'keywords' => 'kelas rombel tingkat angkatan wali kelas'],
            ['title' => 'Standar Kompetensi & Keahlian', 'desc' => 'Taksonomi keahlian teknis & soft skill', 'url' => route('admin.skills.index'), 'icon' => 'award', 'keywords' => 'skill keahlian kompetensi sertifikasi kemampuan'],
            ['title' => 'Mitra Industri (DUDI)', 'desc' => 'Direktori perusahaan rekanan sekolah', 'url' => route('admin.companies.index'), 'icon' => 'building-2', 'keywords' => 'mitra industri dudi perusahaan pkl magang rekanan'],
            ['title' => 'Verifikasi Mitra Industri', 'desc' => 'Antrean persetujuan kemitraan perusahaan baru', 'url' => route('admin.companies.verification'), 'icon' => 'shield-check', 'keywords' => 'verifikasi antrean pendaftaran persetujuan mitra pending'],
            ['title' => 'Bursa Lowongan Kerja', 'desc' => 'Peluang karier dan rekrutmen alumni', 'url' => route('admin.jobs.index'), 'icon' => 'briefcase', 'keywords' => 'lowongan kerja loker job karir pkl magang bursa'],
            ['title' => 'Pusat Notifikasi', 'desc' => 'Aktivitas sistem, kemitraan, dan siswa', 'url' => route('admin.notifications.index'), 'icon' => 'bell', 'keywords' => 'notifikasi pemberitahuan alert pesan activity'],
            ['title' => 'Pengaturan Portal', 'desc' => 'Konfigurasi akun dan preferensi sekolah', 'url' => route('admin.settings'), 'icon' => 'settings', 'keywords' => 'setting pengaturan akun profile config sistem'],
        ];

        $matchedPages = array_filter($pages, function ($p) use ($q) {
            $searchTarget = strtolower($p['title'] . ' ' . $p['desc'] . ' ' . $p['keywords']);
            return str_contains($searchTarget, strtolower($q));
        });

        if (!empty($matchedPages)) {
            $results[] = [
                'category' => 'Menu & Navigasi',
                'items' => array_values(array_slice($matchedPages, 0, 4)),
            ];
        }

        // 2. Data Siswa
        $students = Student::with(['user', 'major'])
            ->where(function ($query) use ($q) {
                $query->where('nisn', 'like', "%{$q}%")
                      ->orWhereHas('user', function ($uq) use ($q) {
                          $uq->where('name', 'like', "%{$q}%")
                             ->orWhere('email', 'like', "%{$q}%");
                      });
            })
            ->take(3)
            ->get()
            ->map(function ($s) {
                return [
                    'title' => $s->user->name ?? 'Siswa',
                    'desc' => ($s->major->nama ?? 'Siswa') . ($s->nisn ? ' • NISN: ' . $s->nisn : ''),
                    'url' => route('admin.students.index', ['search' => $s->user->name ?? $s->nisn]),
                    'icon' => 'graduation-cap',
                ];
            })
            ->all();

        if (!empty($students)) {
            $results[] = [
                'category' => 'Peserta Didik (Siswa)',
                'items' => $students,
            ];
        }

        // 3. Data Guru
        $teachers = Teacher::with('user')
            ->where(function ($query) use ($q) {
                $query->where('nip', 'like', "%{$q}%")
                      ->orWhere('mata_pelajaran', 'like', "%{$q}%")
                      ->orWhereHas('user', function ($uq) use ($q) {
                          $uq->where('name', 'like', "%{$q}%")
                             ->orWhere('email', 'like', "%{$q}%");
                      });
            })
            ->take(3)
            ->get()
            ->map(function ($t) {
                return [
                    'title' => $t->user->name ?? 'Guru',
                    'desc' => ($t->mata_pelajaran ?: 'Tenaga Pendidik') . ($t->nip ? ' • NIP: ' . $t->nip : ''),
                    'url' => route('admin.teachers.index', ['search' => $t->user->name ?? $t->nip]),
                    'icon' => 'user-check',
                ];
            })
            ->all();

        if (!empty($teachers)) {
            $results[] = [
                'category' => 'Tenaga Pendidik (Guru)',
                'items' => $teachers,
            ];
        }

        // 4. Mitra Industri
        $companies = Company::where('nama', 'like', "%{$q}%")
            ->orWhere('bidang', 'like', "%{$q}%")
            ->orWhere('kota', 'like', "%{$q}%")
            ->take(3)
            ->get()
            ->map(function ($c) {
                return [
                    'title' => $c->nama,
                    'desc' => ($c->bidang ?: 'Mitra Industri') . ($c->kota ? ' • ' . $c->kota : '') . ' [' . ucfirst($c->status_verifikasi) . ']',
                    'url' => route('admin.companies.index', ['search' => $c->nama]),
                    'icon' => 'building-2',
                ];
            })
            ->all();

        if (!empty($companies)) {
            $results[] = [
                'category' => 'Mitra Industri (DUDI)',
                'items' => $companies,
            ];
        }

        // 5. Lowongan Kerja
        $jobs = Job::with('company')
            ->where('judul', 'like', "%{$q}%")
            ->orWhere('lokasi', 'like', "%{$q}%")
            ->orWhere('deskripsi', 'like', "%{$q}%")
            ->orWhereHas('company', fn($cq) => $cq->where('nama', 'like', "%{$q}%"))
            ->take(3)
            ->get()
            ->map(function ($j) {
                return [
                    'title' => $j->judul,
                    'desc' => ($j->company->nama ?? 'Mitra') . ($j->jenis ? ' • ' . ucfirst($j->jenis) : ''),
                    'url' => route('admin.jobs.index', ['search' => $j->judul]),
                    'icon' => 'briefcase',
                ];
            })
            ->all();

        if (!empty($jobs)) {
            $results[] = [
                'category' => 'Bursa Lowongan Kerja',
                'items' => $jobs,
            ];
        }

        // 6. Konsentrasi Keahlian (Jurusan)
        $majors = Major::where('nama', 'like', "%{$q}%")
            ->orWhere('kode', 'like', "%{$q}%")
            ->take(3)
            ->get()
            ->map(function ($m) {
                return [
                    'title' => $m->kode . ' - ' . $m->nama,
                    'desc' => 'Program Keahlian' . ($m->kepala_jurusan ? ' • Kajur: ' . $m->kepala_jurusan : ''),
                    'url' => route('admin.majors.index', ['search' => $m->kode]),
                    'icon' => 'book-open',
                ];
            })
            ->all();

        if (!empty($majors)) {
            $results[] = [
                'category' => 'Jurusan / Keahlian',
                'items' => $majors,
            ];
        }

        // 7. Rombongan Belajar (Kelas)
        $classes = SchoolClass::with('major')
            ->where('nama', 'like', "%{$q}%")
            ->orWhere('tahun_ajaran', 'like', "%{$q}%")
            ->take(3)
            ->get()
            ->map(function ($cls) {
                return [
                    'title' => $cls->nama,
                    'desc' => 'Tingkat ' . $cls->tingkat . ' • TA ' . $cls->tahun_ajaran . ($cls->major ? ' • ' . $cls->major->kode : ''),
                    'url' => route('admin.classes.index', ['search' => $cls->nama]),
                    'icon' => 'school',
                ];
            })
            ->all();

        if (!empty($classes)) {
            $results[] = [
                'category' => 'Rombel / Kelas',
                'items' => $classes,
            ];
        }

        return response()->json([
            'success' => true,
            'query' => $q,
            'groups' => $results,
        ]);
    }
}
