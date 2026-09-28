<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            MajorSeeder::class,
            SchoolProfileSeeder::class,
            CategorySeeder::class,
            SkillSeeder::class,
            ClassSeeder::class,
            UserSeeder::class,
        ]);
    }
}
