<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'nama' => 'Umum & Berita',
                'slug' => 'umum-dan-berita',
                'jenis' => 'umum',
            ],
            [
                'nama' => 'Materi Belajar',
                'slug' => 'materi-belajar',
                'jenis' => 'materi',
            ],
            [
                'nama' => 'Karya & Portofolio',
                'slug' => 'karya-dan-portofolio',
                'jenis' => 'umum',
            ],
            [
                'nama' => 'Informasi Karier',
                'slug' => 'informasi-karier',
                'jenis' => 'karier',
            ],
            [
                'nama' => 'Pengumuman Resmi',
                'slug' => 'pengumuman-resmi',
                'jenis' => 'pengumuman',
            ],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
