<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Comment;
use App\Models\InternshipEvaluation;
use App\Models\Notification;
use App\Models\Post;
use App\Models\StudentSkill;
use App\Models\Teacher;
use App\Models\User;

class NotificationService
{
    /**
     * Create a notification record.
     */
    public static function create(
        int|User $user,
        string $jenis,
        string $judul,
        ?string $referensiTipe = null,
        ?int $referensiId = null
    ): Notification {
        $userId = $user instanceof User ? $user->id : $user;

        return Notification::create([
            'user_id' => $userId,
            'jenis' => $jenis,
            'judul' => $judul,
            'referensi_tipe' => $referensiTipe,
            'referensi_id' => $referensiId,
            'dibaca' => false,
            'created_at' => now(),
        ]);
    }

    /**
     * Notify company when a student submits an application.
     */
    public static function notifyNewApplicant(Application $application): ?Notification
    {
        $company = $application->job?->company;
        if (! $company || ! $company->user_id) {
            return null;
        }

        $studentName = $application->student?->user?->name ?? 'Siswa';
        $jobTitle = $application->job?->posisi ?? 'Lowongan';

        return self::create(
            $company->user_id,
            'lamaran',
            "Pelamar Baru: {$studentName} telah melamar pada posisi '{$jobTitle}'.",
            'application',
            $application->id
        );
    }

    /**
     * Notify student when their application status is updated by company.
     */
    public static function notifyApplicationStatus(Application $application, string $newStatus): ?Notification
    {
        $studentUser = $application->student?->user;
        if (! $studentUser) {
            return null;
        }

        $companyName = $application->job?->company?->nama ?? 'Mitra Industri';
        $jobTitle = $application->job?->posisi ?? 'Lowongan';

        $statusText = match ($newStatus) {
            'wawancara' => 'Dipanggil Wawancara / Uji Teknis',
            'diterima' => 'DITERIMA Bekerja / PKL',
            'ditolak' => 'Belum Lolos Seleksi',
            'ditinjau' => 'Sedang Ditinjau oleh HRD',
            default => ucfirst($newStatus),
        };

        return self::create(
            $studentUser->id,
            'lamaran',
            "Pembaruan Lamaran: {$companyName} memperbarui status posisi '{$jobTitle}' menjadi '{$statusText}'.",
            'application',
            $application->id
        );
    }

    /**
     * Notify student when a teacher endorses their skill.
     */
    public static function notifySkillEndorsed(StudentSkill $studentSkill, ?Teacher $teacher = null): ?Notification
    {
        $studentUser = $studentSkill->student?->user;
        if (! $studentUser) {
            return null;
        }

        $skillName = $studentSkill->skill?->nama ?? 'Keahlian';
        $teacherName = $teacher?->user?->name ?? 'Guru Kejuruan';

        return self::create(
            $studentUser->id,
            'keahlian',
            "Validasi Keahlian: Guru {$teacherName} telah mengesahkan keahlian '{$skillName}' Anda.",
            'student_skill',
            $studentSkill->id
        );
    }

    /**
     * Notify homeroom/assigned teacher when a student submits a skill for validation.
     */
    public static function notifyNewSkillSubmission(StudentSkill $studentSkill): ?Notification
    {
        $student = $studentSkill->student;
        $class = $student?->schoolClass;
        $teacherUser = $class?->teacher?->user;

        // If no homeroom teacher, find any teacher
        if (! $teacherUser) {
            $teacher = Teacher::with('user')->first();
            $teacherUser = $teacher?->user;
        }

        if (! $teacherUser) {
            return null;
        }

        $studentName = $student?->user?->name ?? 'Siswa';
        $skillName = $studentSkill->skill?->nama ?? 'Keahlian';

        return self::create(
            $teacherUser->id,
            'keahlian',
            "Pengajuan Validasi: {$studentName} mengajukan validasi keahlian '{$skillName}'.",
            'student_skill',
            $studentSkill->id
        );
    }

    /**
     * Notify user for community interactions (like, comment, follow).
     */
    public static function notifyCommunityInteraction(
        User $recipient,
        User $actor,
        string $action,
        Post|Comment|null $target = null
    ): ?Notification {
        // Prevent notifying self
        if ($recipient->id === $actor->id) {
            return null;
        }

        $actorName = $actor->name;

        return match ($action) {
            'like' => self::create(
                $recipient->id,
                'komunitas',
                "{$actorName} menyukai kiriman Anda di linimasa komunitas.",
                'post',
                $target instanceof Post ? $target->id : null
            ),
            'comment' => self::create(
                $recipient->id,
                'komunitas',
                "{$actorName} mengomentari kiriman Anda: \"" . mb_strimwidth($target?->konten ?? '', 0, 45, '...') . "\"",
                'post',
                $target instanceof Comment ? $target->post_id : ($target instanceof Post ? $target->id : null)
            ),
            'follow' => self::create(
                $recipient->id,
                'komunitas',
                "{$actorName} mulai mengikuti profil Anda di jaringan vokasi.",
                'user',
                $actor->id
            ),
            default => null,
        };
    }

    /**
     * Notify student and teachers when evaluation is submitted by company.
     */
    public static function notifyEvaluationSubmitted(InternshipEvaluation $evaluation): void
    {
        $studentUser = $evaluation->student?->user;
        $companyName = $evaluation->company?->nama ?? 'Mitra Industri';

        if ($studentUser) {
            self::create(
                $studentUser->id,
                'evaluasi',
                "Penilaian PKL Anda telah dikirimkan oleh {$companyName} dan sedang menunggu validasi pembimbing sekolah.",
                'evaluation',
                $evaluation->id
            );
        }

        // Notify teachers with role 'guru'
        $teachers = User::whereHas('role', fn($q) => $q->where('nama', 'guru'))->get();
        $studentName = $studentUser?->name ?? 'Siswa';
        foreach ($teachers as $teacherUser) {
            self::create(
                $teacherUser->id,
                'evaluasi',
                "Nilai PKL baru untuk siswa {$studentName} dari {$companyName} siap untuk diverifikasi.",
                'evaluation',
                $evaluation->id
            );
        }
    }

    /**
     * Notify student and company when evaluation is verified by teacher.
     */
    public static function notifyEvaluationVerified(InternshipEvaluation $evaluation): void
    {
        $studentUser = $evaluation->student?->user;
        $companyUser = $evaluation->company?->user;
        $studentName = $studentUser?->name ?? 'Siswa';
        $companyName = $evaluation->company?->nama ?? 'Mitra Industri';

        if ($studentUser) {
            self::create(
                $studentUser->id,
                'evaluasi',
                "Selamat! Penilaian PKL Anda di {$companyName} telah diverifikasi sekolah dengan predikat {$evaluation->predikat} ({$evaluation->predicate_label}).",
                'evaluation',
                $evaluation->id
            );
        }

        if ($companyUser) {
            self::create(
                $companyUser->id,
                'evaluasi',
                "Penilaian PKL untuk siswa {$studentName} telah diverifikasi dan disahkan oleh pihak sekolah.",
                'evaluation',
                $evaluation->id
            );
        }
    }
}
