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
        /** @var list<array{name: string, slug: string, description: string, meta_title: string, meta_description: string}> $categories */
        $categories = [
            [
                'name' => 'Strategi Digital',
                'slug' => 'strategi-digital',
                'description' => 'Perencanaan kanal digital, riset audiens, dan pengukuran performa kampanye.',
                'meta_title' => 'Strategi Digital — PRIMA Karunia Kreasi',
                'meta_description' => 'Artikel tentang strategi digital, riset audiens, dan pengukuran performa kampanye dari PRIMA Karunia Kreasi.',
            ],
            [
                'name' => 'Desain & Kreatif',
                'slug' => 'desain-kreatif',
                'description' => 'Identitas visual, desain antarmuka, dan konten kreatif yang konsisten.',
                'meta_title' => 'Desain & Kreatif — PRIMA Karunia Kreasi',
                'meta_description' => 'Artikel tentang identitas visual, desain antarmuka, dan konten kreatif dari PRIMA Karunia Kreasi.',
            ],
            [
                'name' => 'Teknologi',
                'slug' => 'teknologi',
                'description' => 'Pengembangan web, performa, keamanan, dan pemeliharaan aplikasi.',
                'meta_title' => 'Teknologi — PRIMA Karunia Kreasi',
                'meta_description' => 'Artikel tentang pengembangan web, performa, keamanan, dan pemeliharaan aplikasi dari PRIMA Karunia Kreasi.',
            ],
        ];

        foreach ($categories as $category) {
            Category::query()->updateOrCreate(
                ['slug' => $category['slug']],
                $category,
            );
        }
    }
}
