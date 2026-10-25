<?php

namespace App\Services;

use App\Models\Job;
use App\Models\Student;

class JobMatchingService
{
    /**
     * Menghitung persentase kecocokan antara profil siswa dan lowongan industri
     * Berdasarkan Formula KAK Paket 15:
     * Total Skor = (0.60 * K) + (0.15 * J) + (0.10 * T) + (0.10 * P) + (0.05 * L)
     *
     * @param Student $student
     * @param Job $job
     * @return array
     */
    public function calculate(Student $student, Job $job): array
    {
        // 1. Keahlian (K - Bobot 60%)
        $jobSkills = $job->skills;
        $studentSkills = $student->skills;
        $studentSkillIds = $studentSkills->pluck('id')->toArray();

        $kScore = 100.0;
        $matchedSkills = [];
        $unmatchedSkills = [];

        if ($jobSkills->isNotEmpty()) {
            $totalJobWeight = 0;
            $achievedWeight = 0;
            $missingRequired = false;

            foreach ($jobSkills as $jSkill) {
                $weight = $jSkill->pivot->bobot ?? 1;
                $isRequired = (bool) ($jSkill->pivot->wajib ?? false);
                $totalJobWeight += $weight;

                if (in_array($jSkill->id, $studentSkillIds)) {
                    $achievedWeight += $weight;
                    $matchedSkills[] = $jSkill->nama;
                } else {
                    $unmatchedSkills[] = $jSkill->nama;
                    if ($isRequired) {
                        $missingRequired = true;
                    }
                }
            }

            $rawK = $totalJobWeight > 0 ? ($achievedWeight / $totalJobWeight) * 100 : 100.0;
            if ($missingRequired && $rawK > 70) {
                $rawK *= 0.85;
            }
            $kScore = min(100.0, max(0.0, $rawK));
        }

        // 2. Kesesuaian Jurusan (J - Bobot 15%)
        $jScore = 100.0; // Terfokus pada PPLG SMKN 1 Cirebon

        // 3. Tingkat Kemahiran (T - Bobot 10%)
        $tScore = 70.0;
        if ($studentSkills->isNotEmpty()) {
            $relevantLevels = [];
            foreach ($studentSkills as $sSkill) {
                if ($jobSkills->contains('id', $sSkill->id)) {
                    $relevantLevels[] = (($sSkill->pivot->tingkat ?? 3) / 5) * 100;
                }
            }
            if (!empty($relevantLevels)) {
                $tScore = array_sum($relevantLevels) / count($relevantLevels);
            } else {
                $avgOverall = $studentSkills->avg(fn($s) => $s->pivot->tingkat ?? 3);
                $tScore = ($avgOverall / 5) * 80;
            }
        }

        // 4. Portofolio Terkait (P - Bobot 10%)
        $portfolioCount = $student->portfolios()->count();
        if ($portfolioCount >= 3) {
            $pScore = 100.0;
        } elseif ($portfolioCount === 2) {
            $pScore = 85.0;
        } elseif ($portfolioCount === 1) {
            $pScore = 70.0;
        } else {
            $pScore = 50.0;
        }

        // 5. Kesediaan Lokasi (L - Bobot 5%)
        $studentCity = strtolower($student->user->profile->kota ?? 'cirebon');
        $jobLocation = strtolower($job->lokasi ?? '');
        $lScore = 80.0;
        if (str_contains($jobLocation, 'remote') || str_contains($jobLocation, 'wfh')) {
            $lScore = 100.0;
        } elseif (str_contains($jobLocation, 'cirebon') && str_contains($studentCity, 'cirebon')) {
            $lScore = 100.0;
        }

        $totalScore = (0.60 * $kScore) + (0.15 * $jScore) + (0.10 * $tScore) + (0.10 * $pScore) + (0.05 * $lScore);
        $totalScore = round(min(100.0, max(0.0, $totalScore)), 2);

        return [
            'total' => $totalScore,
            'percentage' => round($totalScore) . '%',
            'components' => [
                'k' => round($kScore, 1),
                'j' => round($jScore, 1),
                't' => round($tScore, 1),
                'p' => round($pScore, 1),
                'l' => round($lScore, 1),
            ],
            'matched_skills' => $matchedSkills,
            'unmatched_skills' => $unmatchedSkills,
        ];
    }
}
