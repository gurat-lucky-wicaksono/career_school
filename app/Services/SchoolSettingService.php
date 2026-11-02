<?php

namespace App\Services;

use App\Models\SchoolSetting;
use App\Models\User;

class SchoolSettingService
{
    /**
     * Get official school name.
     */
    public static function getSchoolName(): string
    {
        return (string) SchoolSetting::get('school_name', 'SMK Negeri 1 Cirebon');
    }

    /**
     * Get school NPSN.
     */
    public static function getSchoolNpsn(): string
    {
        return (string) SchoolSetting::get('school_npsn', '20222159');
    }

    /**
     * Get school official address.
     */
    public static function getSchoolAddress(): string
    {
        return (string) SchoolSetting::get('school_address', 'Jl. Perjuangan, Sunyaragi, Kec. Kesambi, Kota Cirebon, Jawa Barat 45131');
    }

    /**
     * Get school telephone number.
     */
    public static function getSchoolPhone(): string
    {
        return (string) SchoolSetting::get('school_phone', '(0231) 480202');
    }

    /**
     * Get school official email.
     */
    public static function getSchoolEmail(): string
    {
        return (string) SchoolSetting::get('school_email', 'info@smkn1-cirebon.sch.id');
    }

    /**
     * Get school official website URL.
     */
    public static function getSchoolWebsite(): string
    {
        return (string) SchoolSetting::get('school_website', 'https://smkn1-cirebon.sch.id');
    }

    /**
     * Get Principal (Kepala Sekolah) full name with title.
     */
    public static function getPrincipalName(): string
    {
        return (string) SchoolSetting::get('principal_name', 'Drs. H. Arifudin, M.Pd.');
    }

    /**
     * Get Principal NIP.
     */
    public static function getPrincipalNip(): string
    {
        return (string) SchoolSetting::get('principal_nip', '19680512 199303 1 008');
    }

    /**
     * Get BKK unit name.
     */
    public static function getBkkName(): string
    {
        return (string) SchoolSetting::get('bkk_name', 'Bursa Kerja Khusus (BKK) SMK Negeri 1 Cirebon');
    }

    /**
     * Get BKK Leader (Ketua / Koordinator BKK) name.
     */
    public static function getBkkLeaderName(): string
    {
        return (string) SchoolSetting::get('bkk_leader_name', 'Bambang Sudarsono, S.Kom.');
    }

    /**
     * Get BKK Leader NIP.
     */
    public static function getBkkLeaderNip(): string
    {
        return (string) SchoolSetting::get('bkk_leader_nip', '19820719 200902 1 003');
    }

    /**
     * Get BKK operational decree (SK) number.
     */
    public static function getBkkSkNumber(): string
    {
        return (string) SchoolSetting::get('bkk_sk_number', '560/042/BKK-Disnaker/2023');
    }

    /**
     * Get BKK contact hotline phone.
     */
    public static function getBkkContactPhone(): string
    {
        return (string) SchoolSetting::get('bkk_contact_phone', '0812-3456-7890');
    }

    /**
     * Get active academic year (e.g. 2025/2026).
     */
    public static function getActiveAcademicYear(): string
    {
        return (string) SchoolSetting::get('active_academic_year', '2025/2026');
    }

    /**
     * Get active academic semester (Ganjil or Genap).
     */
    public static function getActiveSemester(): string
    {
        return (string) SchoolSetting::get('active_semester', 'Ganjil');
    }

    /**
     * Get maximum active job/internship applications allowed per student.
     */
    public static function getMaxActiveApplications(): int
    {
        return (int) SchoolSetting::get('max_active_applications_per_student', 3);
    }

    /**
     * Check if self-registration for new students is allowed.
     */
    public static function isStudentRegistrationAllowed(): bool
    {
        $val = SchoolSetting::get('allow_student_registration', '1');
        return $val === '1' || $val === 'true' || $val === true || $val === 1;
    }

    /**
     * Get standardized signatories data structure for report and proof sheet generation.
     */
    public static function getSignatories(): array
    {
        return [
            'kepala_sekolah' => [
                'nama' => self::getPrincipalName(),
                'nip' => self::getPrincipalNip(),
                'jabatan' => 'Kepala ' . self::getSchoolName(),
            ],
            'koordinator_bkk' => [
                'nama' => self::getBkkLeaderName(),
                'nip' => self::getBkkLeaderNip(),
                'jabatan' => 'Ketua ' . self::getBkkName(),
            ],
        ];
    }

    /**
     * Batch update settings and trigger activity logging.
     */
    public static function updateBatch(array $data, ?User $actor = null): void
    {
        $changedKeys = [];

        foreach ($data as $key => $value) {
            $existing = SchoolSetting::where('key', $key)->first();
            $oldValue = $existing?->value;
            $newValue = is_bool($value) ? ($value ? '1' : '0') : (string) $value;

            if ($oldValue !== $newValue) {
                $changedKeys[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
                SchoolSetting::set($key, $newValue);
            }
        }

        SchoolSetting::flushCache();

        if (!empty($changedKeys)) {
            ActivityLogService::log(
                action: 'system.settings_updated',
                description: 'Konfigurasi pengaturan lembaga dan sistem diperbarui oleh ' . ($actor?->name ?? 'Administrator') . '.',
                properties: [
                    'changed_count' => count($changedKeys),
                    'changes' => $changedKeys,
                ],
                user: $actor
            );
        }
    }
}
