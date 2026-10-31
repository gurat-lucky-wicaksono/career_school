<?php

namespace App\Services;

use App\Models\Alumni;
use App\Models\Application;
use App\Models\Company;
use App\Models\Job;
use App\Models\Major;
use App\Models\SchoolProfile;
use App\Models\Student;
use App\Models\StudentSkill;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportService
{
    /**
     * Compile statistical summary data for BKK SMKN 1 Cirebon.
     */
    public function getBkkSummaryData(array $filters = []): array
    {
        $majorId = $filters['major_id'] ?? null;
        $tahun = $filters['tahun'] ?? null;

        // School Profile
        $schoolProfile = SchoolProfile::first() ?? new SchoolProfile([
            'nama_sekolah' => 'SMKN 1 Cirebon',
            'alamat' => 'Jl. Perjuangan, Sunyaragi, Kec. Kesambi, Kota Cirebon, Jawa Barat 45132',
            'telepon' => '0231-480202',
            'email' => 'info@smkn1-cirebon.sch.id',
        ]);

        // Majors query
        $majors = Major::orderBy('nama')->get();

        // 1. Partner Companies & Vacancies
        $totalCompanies = Company::count();
        $verifiedCompanies = Company::where('status_verifikasi', 'terverifikasi')->count();
        $pendingCompanies = Company::where('status_verifikasi', 'menunggu')->count();

        $jobsQuery = Job::query();
        if ($majorId) {
            $jobsQuery->whereHas('skills', function ($q) use ($majorId) {
                // optionally filter jobs requiring major skills
            });
        }
        $totalJobs = (clone $jobsQuery)->count();
        $internshipJobs = (clone $jobsQuery)->where('jenis', 'magang')->count();
        $fulltimeJobs = (clone $jobsQuery)->where('jenis', 'penuh_waktu')->count();
        $activeJobs = (clone $jobsQuery)->where('status', 'dibuka')->count();

        // 2. Students & Active Cohorts
        $studentsQuery = Student::query();
        if ($majorId) {
            $studentsQuery->where('major_id', $majorId);
        }
        if ($tahun) {
            $studentsQuery->where('angkatan', $tahun);
        }
        $totalStudents = (clone $studentsQuery)->count();

        // 3. Applications & Absorption
        $applicationsQuery = Application::with(['job.company', 'student.user', 'student.major']);
        if ($majorId) {
            $applicationsQuery->whereHas('student', fn($q) => $q->where('major_id', $majorId));
        }
        if ($tahun) {
            $applicationsQuery->whereHas('student', fn($q) => $q->where('angkatan', $tahun));
        }

        $totalApplications = (clone $applicationsQuery)->count();
        $acceptedApplications = (clone $applicationsQuery)->where('status', 'diterima')->count();
        $interviewApplications = (clone $applicationsQuery)->where('status', 'wawancara')->count();
        $reviewApplications = (clone $applicationsQuery)->where('status', 'ditinjau')->count();
        $pendingApplications = (clone $applicationsQuery)->where('status', 'tertunda')->count();
        $rejectedApplications = (clone $applicationsQuery)->where('status', 'ditolak')->count();

        $absorptionRate = $totalApplications > 0
            ? round(($acceptedApplications / $totalApplications) * 100, 1)
            : 0;

        // Top Partner Companies by Placements
        $topCompanies = Company::withCount(['jobs', 'jobs as accepted_count' => function ($q) {
            $q->whereHas('applications', fn($a) => $a->where('status', 'diterima'));
        }])
        ->orderByDesc('accepted_count')
        ->take(5)
        ->get();

        // 4. Tracer Study (BMW) Summary
        $alumniQuery = Alumni::with(['user.profile', 'major']);
        if ($majorId) {
            $alumniQuery->where('major_id', $majorId);
        }
        if ($tahun) {
            $alumniQuery->where('tahun_lulus', $tahun);
        }

        $totalAlumni = (clone $alumniQuery)->count();
        $bekerjaCount = (clone $alumniQuery)->where('status_bmw', 'bekerja')->count();
        $kuliahCount = (clone $alumniQuery)->where('status_bmw', 'kuliah')->count();
        $wirausahaCount = (clone $alumniQuery)->where('status_bmw', 'wirausaha')->count();
        $mencariKerjaCount = (clone $alumniQuery)->where('status_bmw', 'mencari_kerja')->count();

        $bmwStats = [
            'total' => $totalAlumni,
            'bekerja' => [
                'count' => $bekerjaCount,
                'pct' => $totalAlumni > 0 ? round(($bekerjaCount / $totalAlumni) * 100, 1) : 0,
            ],
            'kuliah' => [
                'count' => $kuliahCount,
                'pct' => $totalAlumni > 0 ? round(($kuliahCount / $totalAlumni) * 100, 1) : 0,
            ],
            'wirausaha' => [
                'count' => $wirausahaCount,
                'pct' => $totalAlumni > 0 ? round(($wirausahaCount / $totalAlumni) * 100, 1) : 0,
            ],
            'mencari_kerja' => [
                'count' => $mencariKerjaCount,
                'pct' => $totalAlumni > 0 ? round(($mencariKerjaCount / $totalAlumni) * 100, 1) : 0,
            ],
        ];

        // Keselarasan Kurikulum Stats
        $keselarasanStats = [
            'sangat_sesuai' => (clone $alumniQuery)->where('keselarasan', 'sangat_sesuai')->count(),
            'sesuai' => (clone $alumniQuery)->where('keselarasan', 'sesuai')->count(),
            'kurang_sesuai' => (clone $alumniQuery)->where('keselarasan', 'kurang_sesuai')->count(),
            'tidak_sesuai' => (clone $alumniQuery)->where('keselarasan', 'tidak_sesuai')->count(),
        ];

        // Average wait time (in months) for working alumni
        $avgWaitTime = (clone $alumniQuery)
            ->whereNotNull('waktu_tunggu_bulan')
            ->where('waktu_tunggu_bulan', '>', 0)
            ->avg('waktu_tunggu_bulan');

        // Top Validated Skills
        $topSkills = StudentSkill::with('skill')
            ->where('tervalidasi', true)
            ->selectRaw('skill_id, count(*) as total_students, avg(tingkat) as avg_rating')
            ->groupBy('skill_id')
            ->orderByDesc('total_students')
            ->take(6)
            ->get();

        return [
            'school_profile' => $schoolProfile,
            'majors' => $majors,
            'selected_major' => $majorId ? Major::find($majorId) : null,
            'selected_year' => $tahun,
            'metrics' => [
                'total_companies' => $totalCompanies,
                'verified_companies' => $verifiedCompanies,
                'pending_companies' => $pendingCompanies,
                'total_jobs' => $totalJobs,
                'internship_jobs' => $internshipJobs,
                'fulltime_jobs' => $fulltimeJobs,
                'active_jobs' => $activeJobs,
                'total_students' => $totalStudents,
                'total_applications' => $totalApplications,
                'accepted_applications' => $acceptedApplications,
                'interview_applications' => $interviewApplications,
                'review_applications' => $reviewApplications,
                'pending_applications' => $pendingApplications,
                'rejected_applications' => $rejectedApplications,
                'absorption_rate' => $absorptionRate,
                'avg_wait_time' => $avgWaitTime ? round($avgWaitTime, 1) : 0,
            ],
            'top_companies' => $topCompanies,
            'bmw_stats' => $bmwStats,
            'keselarasan_stats' => $keselarasanStats,
            'top_skills' => $topSkills,
            'signatories' => [
                'kepala_sekolah' => [
                    'nama' => 'Drs. H. Arifudin, M.Pd.',
                    'nip' => '19680512 199303 1 008',
                    'jabatan' => 'Kepala SMK Negeri 1 Cirebon',
                ],
                'koordinator_bkk' => [
                    'nama' => 'Bambang Sudarsono, S.Kom.',
                    'nip' => '19820719 200902 1 003',
                    'jabatan' => 'Ketua Bursa Kerja Khusus (BKK)',
                ],
            ],
            'generated_at' => now(),
        ];
    }

    /**
     * Stream Tracer Study data as RFC 4180 CSV with UTF-8 BOM for Microsoft Excel.
     */
    public function streamTracerCsv(array $filters = []): StreamedResponse
    {
        $filename = 'tracer-study-smkn1-cirebon-' . date('Ymd-His') . '.csv';

        $query = Alumni::with(['user.profile', 'major'])->latest();

        if (!empty($filters['major_id'])) {
            $query->where('major_id', $filters['major_id']);
        }
        if (!empty($filters['status_bmw'])) {
            $query->where('status_bmw', $filters['status_bmw']);
        }
        if (!empty($filters['tahun_lulus'])) {
            $query->where('tahun_lulus', $filters['tahun_lulus']);
        }

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // Write UTF-8 BOM so Excel opens indonesian characters properly
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // CSV Column Headers
            fputcsv($handle, [
                'No',
                'Nama Alumni',
                'Username',
                'Email',
                'Nomor Telepon',
                'Konsentrasi Keahlian / Jurusan',
                'Tahun Kelulusan',
                'Status BMW',
                'Nama Instansi / Kampus / Usaha',
                'Posisi / Prodi / Bidang',
                'Rentang Pendapatan Bulanan',
                'Keselarasan Kejuruan',
                'Masa Tunggu Kerja (Bulan)',
                'Status Tampil Publik',
                'Tanggal Pengisian',
            ]);

            $index = 1;
            $query->chunk(100, function ($alumniBatch) use ($handle, &$index) {
                foreach ($alumniBatch as $alumni) {
                    $bmwLabel = match ($alumni->status_bmw) {
                        'bekerja' => 'Bekerja',
                        'kuliah' => 'Melanjutkan Pendidikan / Kuliah',
                        'wirausaha' => 'Wirausaha / Mandiri',
                        'mencari_kerja' => 'Mencari Kerja',
                        default => $alumni->status_bmw ?? '-',
                    };

                    $keselarasanLabel = match ($alumni->keselarasan) {
                        'sangat_sesuai' => 'Sangat Sesuai',
                        'sesuai' => 'Sesuai',
                        'kurang_sesuai' => 'Kurang Sesuai',
                        'tidak_sesuai' => 'Tidak Sesuai',
                        default => '-',
                    };

                    fputcsv($handle, [
                        $index++,
                        $alumni->user->name ?? '-',
                        $alumni->user->username ?? '-',
                        $alumni->user->email ?? '-',
                        $alumni->user->profile->telepon ?? '-',
                        $alumni->major->nama ?? '-',
                        $alumni->tahun_lulus ?? '-',
                        $bmwLabel,
                        $alumni->nama_instansi ?? '-',
                        $alumni->posisi_studi ?? '-',
                        $alumni->pendapatan_bulanan ?? '-',
                        $keselarasanLabel,
                        $alumni->waktu_tunggu_bulan !== null ? $alumni->waktu_tunggu_bulan . ' Bulan' : '-',
                        $alumni->is_public ? 'Publik' : 'Privat',
                        $alumni->created_at ? $alumni->created_at->format('Y-m-d H:i') : '-',
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Stream Job & Internship Applications data as RFC 4180 CSV.
     */
    public function streamApplicationsCsv(array $filters = []): StreamedResponse
    {
        $filename = 'rekap-lamaran-bkk-smkn1-cirebon-' . date('Ymd-His') . '.csv';

        $query = Application::with(['job.company', 'student.user', 'student.major', 'student.schoolClass'])->latest();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['job_id'])) {
            $query->where('job_id', $filters['job_id']);
        }

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'No',
                'Kode Registrasi',
                'Nama Siswa',
                'NISN',
                'Jurusan',
                'Kelas',
                'Angkatan',
                'Judul Lowongan',
                'Tipe Lowongan',
                'Nama Perusahaan Mitra',
                'Lokasi Penempatan',
                'Skor Kecocokan (KAK 15)',
                'Status Lamaran',
                'Catatan HRD',
                'Tanggal Mendaftar',
            ]);

            $index = 1;
            $query->chunk(100, function ($batch) use ($handle, &$index) {
                foreach ($batch as $app) {
                    $tipe = match ($app->job->jenis ?? '') {
                        'magang' => 'Magang / PKL',
                        'penuh_waktu' => 'Penuh Waktu (Kerja)',
                        default => $app->job->jenis ?? '-',
                    };

                    fputcsv($handle, [
                        $index++,
                        'APP-' . str_pad($app->id, 5, '0', STR_PAD_LEFT),
                        $app->student->user->name ?? '-',
                        $app->student->nisn ?? '-',
                        $app->student->major->nama ?? '-',
                        $app->student->schoolClass->nama ?? '-',
                        $app->student->angkatan ?? '-',
                        $app->job->judul ?? '-',
                        $tipe,
                        $app->job->company->nama ?? '-',
                        $app->job->lokasi ?? '-',
                        number_format($app->skor_kecocokan, 1) . '%',
                        strtoupper($app->status ?? 'TERTUNDA'),
                        $app->catatan_hrd ?? '-',
                        $app->created_at ? $app->created_at->format('Y-m-d H:i') : '-',
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}
