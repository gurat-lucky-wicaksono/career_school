<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\InternshipEvaluation;
use App\Models\Portfolio;
use App\Models\Skill;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    /**
     * Search within teacher scope: navigation, mentored students, student skills, portfolios, evaluations, and partners.
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

        // 1. Menu & Navigasi Guru
        $pages = [
            ['title' => 'Dashboard Utama Guru', 'desc' => 'Ringkasan bimbingan siswa & validasi keahlian', 'url' => route('teacher.dashboard'), 'icon' => 'layout-grid', 'keywords' => 'dashboard beranda statistik ringkasan bimbingan'],
            ['title' => 'Siswa Bimbingan & PKL', 'desc' => 'Pemantauan keaktifan & penempatan industri binaan', 'url' => route('teacher.mentoring.index'), 'icon' => 'users', 'keywords' => 'siswa bimbingan pkl magang rombel kelas monitoring'],
            ['title' => 'Validasi Keahlian Siswa', 'desc' => 'Pengesahan kompetensi teknis & sertifikasi siswa', 'url' => route('teacher.skills.index'), 'icon' => 'check-circle-2', 'keywords' => 'validasi keahlian skill kompetensi nilai sertifikasi'],
            ['title' => 'Galeri Portofolio Siswa', 'desc' => 'Tinjau proyek aplikasi & karya vokasi siswa binaan', 'url' => route('teacher.portfolios.index'), 'icon' => 'briefcase', 'keywords' => 'portofolio karya proyek github siswa tugas'],
            ['title' => 'Evaluasi & Nilai PKL', 'desc' => 'Pengesahan dan verifikasi lembar nilai magang dari industri', 'url' => route('teacher.evaluations.index'), 'icon' => 'file-check', 'keywords' => 'evaluasi nilai pkl magang industri pengesahan skor'],
            ['title' => 'Komunitas Vokasi & Feed', 'desc' => 'Linimasa karya, info kejuruan, dan diskusi interaktif', 'url' => route('feed.index'), 'icon' => 'message-square', 'keywords' => 'komunitas feed linimasa forum diskusi postingan'],
            ['title' => 'Pusat Notifikasi', 'desc' => 'Pemberitahuan ajuan keahlian & laporan evaluasi PKL', 'url' => route('notifications.index'), 'icon' => 'bell', 'keywords' => 'notifikasi pemberitahuan pesan alert aktivitas'],
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

        // 2. Peserta Didik (Siswa Vokasi)
        $students = Student::with(['user', 'schoolClass', 'major'])
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
                    'desc' => ($s->schoolClass->nama ?? $s->major->kode ?? 'Siswa') . ($s->nisn ? ' • NISN: ' . $s->nisn : ''),
                    'url' => route('teacher.mentoring.index', ['search' => $s->user->name ?? $s->nisn]),
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

        // 3. Keahlian & Kompetensi Siswa
        $skills = Skill::where('nama', 'like', "%{$q}%")
            ->orWhere('kategori', 'like', "%{$q}%")
            ->take(3)
            ->get()
            ->map(function ($sk) {
                return [
                    'title' => $sk->nama,
                    'desc' => 'Standar Kompetensi' . ($sk->kategori ? ' • ' . ucfirst($sk->kategori) : ''),
                    'url' => route('teacher.skills.index', ['q' => $sk->nama]),
                    'icon' => 'award',
                ];
            })
            ->all();

        if (!empty($skills)) {
            $results[] = [
                'category' => 'Standar Keahlian Siswa',
                'items' => $skills,
            ];
        }

        // 4. Portofolio Proyek Siswa
        $portfolios = Portfolio::with('student.user')
            ->where('judul', 'like', "%{$q}%")
            ->orWhere('kategori', 'like', "%{$q}%")
            ->orWhere('deskripsi', 'like', "%{$q}%")
            ->orWhereHas('student.user', fn($uq) => $uq->where('name', 'like', "%{$q}%"))
            ->take(3)
            ->get()
            ->map(function ($p) {
                return [
                    'title' => $p->judul,
                    'desc' => 'Karya ' . ($p->student->user->name ?? 'Siswa') . ($p->kategori ? ' • ' . $p->kategori : ''),
                    'url' => route('teacher.portfolios.index', ['q' => $p->judul]),
                    'icon' => 'folder-git-2',
                ];
            })
            ->all();

        if (!empty($portfolios)) {
            $results[] = [
                'category' => 'Karya Portofolio Siswa',
                'items' => $portfolios,
            ];
        }

        // 5. Evaluasi & Nilai PKL
        $evaluations = InternshipEvaluation::with(['student.user', 'company', 'application.job'])
            ->where(function ($query) use ($q) {
                $query->whereHas('student.user', fn($uq) => $uq->where('name', 'like', "%{$q}%"))
                      ->orWhereHas('student', fn($sq) => $sq->where('nisn', 'like', "%{$q}%"))
                      ->orWhereHas('company', fn($cq) => $cq->where('nama', 'like', "%{$q}%"))
                      ->orWhereHas('application.job', fn($jq) => $jq->where('judul', 'like', "%{$q}%"));
            })
            ->take(3)
            ->get()
            ->map(function ($eval) {
                return [
                    'title' => ($eval->student->user->name ?? 'Siswa') . ' @ ' . ($eval->company->nama ?? 'Mitra'),
                    'desc' => 'Nilai: ' . ($eval->nilai_akhir ?? '-') . ' • Predikat: ' . ($eval->predikat ?? '-') . ' [' . ucfirst($eval->status) . ']',
                    'url' => route('teacher.evaluations.show', $eval),
                    'icon' => 'file-check-2',
                ];
            })
            ->all();

        if (!empty($evaluations)) {
            $results[] = [
                'category' => 'Evaluasi & Nilai PKL',
                'items' => $evaluations,
            ];
        }

        // 6. Mitra Industri PKL
        $companies = Company::where('nama', 'like', "%{$q}%")
            ->orWhere('bidang', 'like', "%{$q}%")
            ->orWhere('kota', 'like', "%{$q}%")
            ->take(3)
            ->get()
            ->map(function ($c) {
                return [
                    'title' => $c->nama,
                    'desc' => ($c->bidang ?: 'Mitra Industri') . ($c->kota ? ' • ' . $c->kota : ''),
                    'url' => route('teacher.mentoring.index', ['search' => $c->nama]),
                    'icon' => 'building-2',
                ];
            })
            ->all();

        if (!empty($companies)) {
            $results[] = [
                'category' => 'Mitra Industri & Penempatan',
                'items' => $companies,
            ];
        }

        return response()->json([
            'success' => true,
            'query' => $q,
            'groups' => $results,
        ]);
    }
}
