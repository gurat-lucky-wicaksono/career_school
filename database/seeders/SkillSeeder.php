<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            ['nama' => 'Laravel', 'kategori' => 'Pemrograman Web'],
            ['nama' => 'PHP', 'kategori' => 'Pemrograman Web'],
            ['nama' => 'Tailwind CSS', 'kategori' => 'Pemrograman Web'],
            ['nama' => 'MySQL', 'kategori' => 'Basis Data'],
            ['nama' => 'JavaScript', 'kategori' => 'Pemrograman Web'],
            ['nama' => 'Alpine.js', 'kategori' => 'Pemrograman Web'],
            ['nama' => 'REST API', 'kategori' => 'Pengembangan Backend'],
            ['nama' => 'Git & GitHub', 'kategori' => 'Version Control'],
            ['nama' => 'UI/UX Design', 'kategori' => 'Desain'],
            ['nama' => 'Figma', 'kategori' => 'Desain'],
            ['nama' => 'Linux & Server', 'kategori' => 'Infrastruktur Jaringan'],
            ['nama' => 'MikroTik', 'kategori' => 'Infrastruktur Jaringan'],
        ];

        foreach ($skills as $skill) {
            Skill::firstOrCreate(['nama' => $skill['nama']], $skill);
        }
    }
}
