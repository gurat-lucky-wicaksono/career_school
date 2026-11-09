<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Company;
use App\Models\Job;
use App\Models\Portfolio;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    /**
     * Search within student scope: navigation, jobs, personal applications, skills, portfolios, and partners.
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim($request->query('q', ''));
        $user = auth()->user();
        $student = $user?->student;

        if (mb_strlen($q) < 2) {
            return response()->json([
                'success' => true,
                'query' => $q,
                'groups' => [],
            ]);
        }

        $results = [];

        // 1. Menu & Navigasi Portal Siswa
        $pages = [
            ['title' => 'Dashboard Utama Siswa', 'desc' => 'Ringkasan lamaran, progres PKL & portofolio', 'url' => route('student.dashboard'), 'icon' => 'layout-grid', 'keywords' => 'dashboard beranda statistik ringkasan progres'],
            ['title' => 'Eksplorasi Lowongan Industri', 'desc' => 'Peluang karier dan formasi magang PKL SMK', 'url' => route('student.jobs.index'), 'icon' => 'briefcase', 'keywords' => 'lowongan kerja loker magang pkl industri posisi karir'],
            ['title' => 'Pelacakan Lamaran Saya', 'desc' => 'Pantau status seleksi berkas dan jadwal wawancara', 'url' => route('student.applications.index'), 'icon' => 'send', 'keywords' => 'lamaran seleksi wawancara status cv berkas'],
            ['title' => 'Nilai & Sertifikat PKL', 'desc' => 'Transkrip capaian kompetensi magang & sertifikat resmi', 'url' => route('student.evaluations.index'), 'icon' => 'award', 'keywords' => 'nilai evaluasi pkl sertifikat transkrip magang'],
            ['title' => 'Galeri Karya & Portofolio', 'desc' => 'Kelola proyek aplikasi, desain, dan repositori karya', 'url' => route('student.portfolios.index'), 'icon' => 'folder-kanban', 'keywords' => 'portofolio karya proyek github aplikasi karya'],
            ['title' => 'Profil & Taksonomi Keahlian', 'desc' => 'Pengaturan biodata diri dan pengajuan validasi kompetensi', 'url' => route('student.profile'), 'icon' => 'user-check', 'keywords' => 'profil keahlian biodata skill kompetensi akun'],
            ['title' => 'Komunitas Vokasi & Feed', 'desc' => 'Linimasa karya, info kejuruan, dan jejaring alumni', 'url' => route('feed.index'), 'icon' => 'message-square', 'keywords' => 'komunitas feed linimasa forum diskusi jejaring'],
            ['title' => 'Pusat Notifikasi', 'desc' => 'Pemberitahuan panggilan wawancara & status pengesahan', 'url' => route('notifications.index'), 'icon' => 'bell', 'keywords' => 'notifikasi pemberitahuan pesan alert aktivitas'],
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

        // 2. Lowongan Kerja & Magang PKL Terbuka
        $jobs = Job::with('company')
            ->where('status', 'buka')
            ->where(function ($query) use ($q) {
                $query->where('judul', 'like', "%{$q}%")
                      ->orWhere('lokasi', 'like', "%{$q}%")
                      ->orWhere('deskripsi', 'like', "%{$q}%")
                      ->orWhereHas('company', fn($cq) => $cq->where('nama', 'like', "%{$q}%"));
            })
            ->take(3)
            ->get()
            ->map(function ($j) {
                return [
                    'title' => $j->judul,
                    'desc' => ($j->company->nama ?? 'Mitra DUDI') . ($j->jenis ? ' • ' . ucfirst($j->jenis) : '') . ($j->lokasi ? ' • ' . $j->lokasi : ''),
                    'url' => route('student.jobs.show', $job ?? $j),
                    'icon' => 'briefcase',
                ];
            })
            ->all();

        if (!empty($jobs)) {
            $results[] = [
                'category' => 'Lowongan Kerja & Magang PKL',
                'items' => $jobs,
            ];
        }

        // 3. Lamaran Saya
        if ($student) {
            $applications = Application::with(['job.company'])
                ->where('student_id', $student->id)
                ->where(function ($query) use ($q) {
                    $query->whereHas('job', function ($jq) use ($q) {
                        $jq->where('judul', 'like', "%{$q}%")
                           ->orWhereHas('company', fn($cq) => $cq->where('nama', 'like', "%{$q}%"));
                    });
                })
                ->take(3)
                ->get()
                ->map(function ($app) {
                    return [
                        'title' => $app->job->judul ?? 'Lamaran',
                        'desc' => ($app->job->company->nama ?? 'Mitra Industri') . ' • Status: ' . ucfirst($app->status),
                        'url' => route('student.applications.show', $app),
                        'icon' => 'send',
                    ];
                })
                ->all();

            if (!empty($applications)) {
                $results[] = [
                    'category' => 'Lamaran & Status Seleksi Saya',
                    'items' => $applications,
                ];
            }
        }

        // 4. Standar Kompetensi Keahlian
        $skills = Skill::where('nama', 'like', "%{$q}%")
            ->orWhere('kategori', 'like', "%{$q}%")
            ->take(3)
            ->get()
            ->map(function ($sk) {
                return [
                    'title' => $sk->nama,
                    'desc' => 'Kompetensi Kejuruan' . ($sk->kategori ? ' • ' . ucfirst($sk->kategori) : ''),
                    'url' => route('student.profile'),
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

        // 5. Karya Portofolio Saya
        if ($student) {
            $portfolios = Portfolio::where('student_id', $student->id)
                ->where(function ($query) use ($q) {
                    $query->where('judul', 'like', "%{$q}%")
                          ->orWhere('deskripsi', 'like', "%{$q}%")
                          ->orWhere('kategori', 'like', "%{$q}%");
                })
                ->take(3)
                ->get()
                ->map(function ($p) {
                    return [
                        'title' => $p->judul,
                        'desc' => 'Portofolio Saya' . ($p->kategori ? ' • ' . $p->kategori : ''),
                        'url' => route('student.portfolios.index'),
                        'icon' => 'folder-git-2',
                    ];
                })
                ->all();

            if (!empty($portfolios)) {
                $results[] = [
                    'category' => 'Karya Portofolio Saya',
                    'items' => $portfolios,
                ];
            }
        }

        // 6. Mitra Industri (DUDI)
        $companies = Company::where('nama', 'like', "%{$q}%")
            ->orWhere('bidang', 'like', "%{$q}%")
            ->orWhere('kota', 'like', "%{$q}%")
            ->take(3)
            ->get()
            ->map(function ($c) {
                return [
                    'title' => $c->nama,
                    'desc' => ($c->bidang ?: 'Mitra Industri') . ($c->kota ? ' • ' . $c->kota : ''),
                    'url' => route('student.jobs.index', ['q' => $c->nama]),
                    'icon' => 'building-2',
                ];
            })
            ->all();

        if (!empty($companies)) {
            $results[] = [
                'category' => 'Mitra Industri Rekanan DUDI',
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
