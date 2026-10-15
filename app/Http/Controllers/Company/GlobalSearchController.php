<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use App\Models\Major;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    /**
     * Search within company scope: navigation, company jobs, applicants, and school skills.
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim($request->query('q', ''));
        $user = auth()->user();
        $company = $user?->company;

        if (mb_strlen($q) < 2 || !$company) {
            return response()->json([
                'success' => true,
                'query' => $q,
                'groups' => [],
            ]);
        }

        $results = [];

        // 1. Menu & Navigasi Portal Perusahaan
        $pages = [
            ['title' => 'Dashboard Mitra Industri', 'desc' => 'Ringkasan performa rekrutmen & pelamar', 'url' => route('company.dashboard'), 'icon' => 'layout-grid', 'keywords' => 'dashboard beranda statistik ringkasan'],
            ['title' => 'Kelola Lowongan Kerja', 'desc' => 'Daftar semua lowongan kerja & magang aktif', 'url' => route('company.jobs.index'), 'icon' => 'briefcase', 'keywords' => 'lowongan loker pkl magang pekerjaan posisi'],
            ['title' => 'Pasang Lowongan Baru', 'desc' => 'Publikasikan peluang karier atau magang baru', 'url' => route('company.jobs.index', ['create' => 1]), 'icon' => 'plus-circle', 'keywords' => 'tambah buat pasang loker baru'],
            ['title' => 'Kelola Pelamar Masuk', 'desc' => 'Daftar berkas & skor kecocokan pelamar siswa', 'url' => route('company.applications.index'), 'icon' => 'users', 'keywords' => 'pelamar kandidat siswa berkas lamaran seleksi cv'],
            ['title' => 'Profil Perusahaan Mitra', 'desc' => 'Kelola identitas, alamat kantor, dan kontak HRD', 'url' => route('company.profile'), 'icon' => 'building-2', 'keywords' => 'profil identitas perusahaan alamat hrd telepon'],
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

        // 2. Lowongan Pekerjaan Perusahaan Ini
        $jobs = Job::where('company_id', $company->id)
            ->where(function ($query) use ($q) {
                $query->where('judul', 'like', "%{$q}%")
                      ->orWhere('lokasi', 'like', "%{$q}%")
                      ->orWhere('deskripsi', 'like', "%{$q}%");
            })
            ->take(4)
            ->get()
            ->map(function ($j) {
                return [
                    'title' => $j->judul,
                    'desc' => ucfirst(str_replace('_', ' ', $j->jenis)) . ' • Kuota: ' . $j->kuota . ' [' . ucfirst($j->status) . ']',
                    'url' => route('company.jobs.index', ['search' => $j->judul]),
                    'icon' => 'briefcase',
                ];
            })
            ->all();

        if (!empty($jobs)) {
            $results[] = [
                'category' => 'Lowongan Pekerjaan Anda',
                'items' => $jobs,
            ];
        }

        // 3. Pelamar Masuk (Kandidat Siswa)
        $applications = Application::with(['student.user', 'student.major', 'job'])
            ->whereHas('job', function ($jq) use ($company) {
                $jq->where('company_id', $company->id);
            })
            ->where(function ($query) use ($q) {
                $query->whereHas('student.user', function ($uq) use ($q) {
                    $uq->where('name', 'like', "%{$q}%");
                })->orWhereHas('student', function ($sq) use ($q) {
                    $sq->where('nisn', 'like', "%{$q}%");
                })->orWhereHas('job', function ($jq) use ($q) {
                    $jq->where('judul', 'like', "%{$q}%");
                });
            })
            ->take(4)
            ->get()
            ->map(function ($app) {
                $candidateName = $app->student->user->name ?? 'Kandidat Siswa';
                $jobTitle = $app->job->judul ?? 'Lowongan';
                $score = $app->skor_kecocokan ? round($app->skor_kecocokan) . '%' : 'N/A';

                return [
                    'title' => $candidateName . ' (' . $jobTitle . ')',
                    'desc' => 'Skor Kecocokan: ' . $score . ' • Status: ' . ucfirst($app->status),
                    'url' => route('company.applications.show', $app->id),
                    'icon' => 'user-check',
                ];
            })
            ->all();

        if (!empty($applications)) {
            $results[] = [
                'category' => 'Kandidat Pelamar Masuk',
                'items' => $applications,
            ];
        }

        // 4. Standar Kompetensi & Keahlian SMK
        $skills = Skill::where('nama', 'like', "%{$q}%")
            ->take(3)
            ->get()
            ->map(function ($s) {
                return [
                    'title' => $s->nama,
                    'desc' => 'Kompetensi Siswa SMKN 1 Cirebon (' . ucfirst($s->kategori) . ')',
                    'url' => route('company.jobs.index', ['create' => 1]),
                    'icon' => 'award',
                ];
            })
            ->all();

        if (!empty($skills)) {
            $results[] = [
                'category' => 'Standar Kompetensi Sekolah',
                'items' => $skills,
            ];
        }

        return response()->json([
            'success' => true,
            'query' => $q,
            'groups' => $results,
        ]);
    }
}
