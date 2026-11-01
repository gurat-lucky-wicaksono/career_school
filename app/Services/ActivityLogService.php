<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Application;
use App\Models\Company;
use App\Models\StudentSkill;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivityLogService
{
    /**
     * Record a standardized activity log event.
     */
    public static function log(
        string $action,
        string $description,
        ?Model $subject = null,
        array $properties = [],
        ?User $user = null
    ): ActivityLog {
        $actor = $user ?? auth()->user();
        $request = request();

        return ActivityLog::create([
            'user_id' => $actor?->id,
            'role' => $actor?->role?->nama ?? ($actor ? 'user' : 'system'),
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->getKey(),
            'properties' => !empty($properties) ? $properties : null,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'created_at' => now(),
        ]);
    }

    /**
     * Record authentication event (login/logout).
     */
    public static function logAuth(
        User $user,
        string $action = 'auth.login',
        string $description = 'Pengguna berhasil autentikasi masuk ke sistem.'
    ): ActivityLog {
        return self::log(
            action: $action,
            description: $description,
            subject: $user,
            properties: [
                'email' => $user->email,
                'role' => $user->role?->nama,
            ],
            user: $user
        );
    }

    /**
     * Record company verification status update by admin.
     */
    public static function logCompanyVerification(
        Company $company,
        string $status,
        User $admin,
        ?string $catatan = null
    ): ActivityLog {
        $statusLabel = match ($status) {
            'terverifikasi' => 'Terverifikasi (Disetujui)',
            'ditolak' => 'Ditolak',
            default => $status,
        };

        return self::log(
            action: $status === 'terverifikasi' ? 'company.verified' : 'company.rejected',
            description: "Admin {$admin->name} mengubah status verifikasi mitra industri '{$company->nama}' menjadi {$statusLabel}.",
            subject: $company,
            properties: [
                'company_name' => $company->nama,
                'status_verifikasi' => $status,
                'catatan' => $catatan,
            ],
            user: $admin
        );
    }

    /**
     * Record job/internship application status transition by HRD.
     */
    public static function logApplicationStatusChange(
        Application $application,
        string $oldStatus,
        string $newStatus,
        User $actor,
        ?string $notes = null
    ): ActivityLog {
        $jobTitle = $application->job->judul ?? 'Lowongan';
        $companyName = $application->job->company->nama ?? 'Mitra Industri';
        $studentName = $application->student->user->name ?? 'Siswa';

        return self::log(
            action: 'application.status_changed',
            description: "HRD {$companyName} mengubah status lamaran {$studentName} pada '{$jobTitle}' dari {$oldStatus} ke {$newStatus}.",
            subject: $application,
            properties: [
                'application_id' => $application->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'student_name' => $studentName,
                'company_name' => $companyName,
                'catatan_hrd' => $notes,
            ],
            user: $actor
        );
    }

    /**
     * Record teacher skill validation and endorsement.
     */
    public static function logSkillValidation(
        StudentSkill $studentSkill,
        Teacher $teacher,
        int $level,
        ?string $notes = null
    ): ActivityLog {
        $teacherName = $teacher->user->name ?? 'Guru Kejuruan';
        $studentName = $studentSkill->student->user->name ?? 'Siswa';
        $skillName = $studentSkill->skill->nama ?? 'Kompetensi';

        return self::log(
            action: 'skill.validated',
            description: "Guru {$teacherName} mengesahkan kompetensi '{$skillName}' milik {$studentName} dengan tingkat kemahiran {$level}/5.",
            subject: $studentSkill,
            properties: [
                'skill_name' => $skillName,
                'student_name' => $studentName,
                'tingkat' => $level,
                'catatan' => $notes,
            ],
            user: $teacher->user
        );
    }

    /**
     * Record student application submission.
     */
    public static function logApplicationSubmitted(
        Application $application,
        User $student
    ): ActivityLog {
        $jobTitle = $application->job->judul ?? 'Lowongan';
        $companyName = $application->job->company->nama ?? 'Mitra Industri';

        return self::log(
            action: 'application.submitted',
            description: "Siswa {$student->name} mengajukan lamaran untuk formasi '{$jobTitle}' di {$companyName}.",
            subject: $application,
            properties: [
                'job_id' => $application->job_id,
                'job_title' => $jobTitle,
                'company_name' => $companyName,
                'skor_kecocokan' => $application->skor_kecocokan,
            ],
            user: $student
        );
    }

    /**
     * Record student application withdrawal.
     */
    public static function logApplicationWithdrawn(
        Application $application,
        User $student
    ): ActivityLog {
        $jobTitle = $application->job->judul ?? 'Lowongan';
        $companyName = $application->job->company->nama ?? 'Mitra Industri';

        return self::log(
            action: 'application.withdrawn',
            description: "Siswa {$student->name} membatalkan pengajuan lamaran pada formasi '{$jobTitle}' di {$companyName}.",
            subject: $application,
            properties: [
                'job_id' => $application->job_id,
                'job_title' => $jobTitle,
                'company_name' => $companyName,
            ],
            user: $student
        );
    }

    /**
     * Record report generation or export.
     */
    public static function logReportExport(
        User $user,
        string $reportType,
        array $filters = []
    ): ActivityLog {
        $typeLabel = match ($reportType) {
            'tracer_study_csv' => 'Ekspor CSV Tracer Study Alumni',
            'applications_csv' => 'Ekspor CSV Rekapitulasi Lamaran Siswa',
            'summary_print' => 'Cetak Laporan Resmi Ringkasan BKK (PDF)',
            'activity_logs_csv' => 'Ekspor CSV Audit Trail Log Aktivitas',
            default => $reportType,
        };

        return self::log(
            action: 'report.exported_' . $reportType,
            description: "Pengguna {$user->name} mengunduh/mencetak dokumen '{$typeLabel}'.",
            properties: [
                'report_type' => $reportType,
                'filters' => $filters,
            ],
            user: $user
        );
    }

    /**
     * Stream Activity Logs as RFC 4180 CSV with UTF-8 BOM.
     */
    public static function streamLogsCsv(array $filters = []): StreamedResponse
    {
        $filename = 'audit-trail-careerschool-' . date('Ymd-His') . '.csv';

        $query = ActivityLog::with('user')->latest('created_at');

        if (!empty($filters['role'])) {
            $query->filterRole($filters['role']);
        }
        if (!empty($filters['action'])) {
            $query->filterAction($filters['action']);
        }
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }
        if (!empty($filters['start_date']) || !empty($filters['end_date'])) {
            $query->dateRange($filters['start_date'] ?? null, $filters['end_date'] ?? null);
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

            // Write UTF-8 BOM for Microsoft Excel
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'No',
                'Waktu Kejadian (WIB)',
                'Nama Pengguna (Aktor)',
                'Email',
                'Peran (Role)',
                'Kategori Aksi',
                'Deskripsi Peristiwa',
                'Tipe Entitas Terkait',
                'ID Entitas',
                'Alamat IP',
                'Perangkat / User Agent',
            ]);

            $index = 1;
            $query->chunk(100, function ($batch) use ($handle, &$index) {
                foreach ($batch as $log) {
                    fputcsv($handle, [
                        $index++,
                        $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : '-',
                        $log->user->name ?? 'Sistem',
                        $log->user->email ?? '-',
                        strtoupper($log->role ?? 'SISTEM'),
                        $log->action_label,
                        $log->description,
                        $log->subject_type ? class_basename($log->subject_type) : '-',
                        $log->subject_id ?? '-',
                        $log->ip_address ?? '-',
                        $log->user_agent ?? '-',
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}
