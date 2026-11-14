<?php

namespace Database\Seeders;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $author = User::query()->where('is_admin', true)->orderBy('id')->first()
            ?? User::query()->orderBy('id')->first()
            ?? User::factory()->admin()->create();

        /** @var array<string, int> $categoryIds */
        $categoryIds = Category::query()->pluck('id', 'slug')->all();

        /** @var list<array<string, mixed>> $posts */
        $posts = [
            [
                'title' => 'Membangun Identitas Digital yang Konsisten',
                'slug' => 'membangun-identitas-digital-yang-konsisten',
                'excerpt' => 'Identitas digital yang konsisten membantu audiens mengenali bisnis Anda dalam hitungan detik.',
                'content' => '<p>Identitas digital bukan hanya soal logo. Warna, tipografi, nada komunikasi, dan kualitas visual bersama-sama membentuk kesan pertama yang diterima audiens.</p>'
                    .'<h2>Mulai dari pedoman yang sederhana</h2>'
                    .'<p>Mulailah dengan menetapkan palet warna utama, keluarga font, serta aturan penggunaan logo. Pedoman yang ringkas justru lebih mudah dijalankan oleh tim internal maupun mitra.</p>'
                    .'<ul><li>Tentukan dua warna utama dan satu warna aksen.</li><li>Gunakan maksimal dua keluarga font.</li><li>Simpan aset logo dalam format vektor.</li></ul>'
                    .'<p>Setelah pedoman tersedia, tinjau setiap materi promosi terhadap pedoman tersebut agar konsistensi terjaga.</p>',
                'category' => 'desain-kreatif',
                'status' => PostStatus::Published,
                'published_at' => now()->subDays(21),
                'is_featured' => true,
                'meta_title' => 'Membangun Identitas Digital yang Konsisten — PRIMA Karunia Kreasi',
                'meta_description' => 'Panduan praktis membangun identitas digital yang konsisten melalui pedoman warna, tipografi, dan penggunaan logo.',
            ],
            [
                'title' => 'Riset Audiens Sebelum Menyusun Kampanye Digital',
                'slug' => 'riset-audiens-sebelum-menyusun-kampanye-digital',
                'excerpt' => 'Kampanye yang tepat sasaran selalu dimulai dari pemahaman yang jelas tentang siapa audiensnya.',
                'content' => '<p>Sebelum mengalokasikan anggaran iklan, luangkan waktu untuk memahami siapa yang ingin Anda jangkau. Riset audiens membantu menentukan pesan, kanal, dan waktu tayang yang tepat.</p>'
                    .'<h2>Pertanyaan yang perlu dijawab</h2>'
                    .'<p>Mulai dari masalah yang ingin diselesaikan audiens, bukan dari fitur yang ingin Anda tonjolkan. Kumpulkan data dari wawancara pelanggan, analitik situs, dan interaksi di media sosial.</p>'
                    .'<p>Hasil riset kemudian diterjemahkan menjadi persona sederhana yang memandu penulisan konten dan pemilihan kanal.</p>',
                'category' => 'strategi-digital',
                'status' => PostStatus::Published,
                'published_at' => now()->subDays(14),
                'is_featured' => false,
                'meta_title' => 'Riset Audiens Sebelum Menyusun Kampanye Digital — PRIMA Karunia Kreasi',
                'meta_description' => 'Cara memulai riset audiens sebelum menyusun kampanye digital agar pesan dan kanal lebih tepat sasaran.',
            ],
            [
                'title' => 'Optimasi Performa Website untuk Pengalaman Pengguna',
                'slug' => 'optimasi-performa-website-untuk-pengalaman-pengguna',
                'excerpt' => 'Website yang cepat terasa lebih dipercaya dan membantu pengunjung menemukan informasi dengan mudah.',
                'content' => '<p>Kecepatan website memengaruhi keputusan pengunjung untuk bertahan. Setiap detik tambahan pada waktu muat meningkatkan risiko pengunjung meninggalkan halaman.</p>'
                    .'<h2>Langkah awal yang berdampak besar</h2>'
                    .'<p>Kompres gambar, aktifkan caching, dan kurangi skrip pihak ketiga yang tidak esensial. Ukur hasilnya menggunakan data lapangan, bukan hanya pengujian di jaringan lokal.</p>'
                    .'<ul><li>Gunakan format gambar modern seperti WebP.</li><li>Muat aset non-kritis secara bertahap.</li><li>Pantau Core Web Vitals setiap bulan.</li></ul>',
                'category' => 'teknologi',
                'status' => PostStatus::Published,
                'published_at' => now()->subDays(9),
                'is_featured' => false,
                'meta_title' => 'Optimasi Performa Website untuk Pengalaman Pengguna — PRIMA Karunia Kreasi',
                'meta_description' => 'Langkah awal mengoptimalkan performa website: kompresi gambar, caching, dan pemantauan Core Web Vitals.',
            ],
            [
                'title' => 'Menulis Konten yang Mudah Dibaca dan Dicari',
                'slug' => 'menulis-konten-yang-mudah-dibaca-dan-dicari',
                'excerpt' => 'Konten yang baik membantu pembaca memahami informasi sekaligus mudah ditemukan mesin pencari.',
                'content' => '<p>Konten yang mudah dibaca menggunakan struktur jelas: judul informatif, paragraf pendek, dan subjudul yang memandu alur baca.</p>'
                    .'<h2>Seimbangkan kebutuhan pembaca dan mesin pencari</h2>'
                    .'<p>Gunakan kata kunci secara alami, lengkapi deskripsi meta, dan tautkan halaman terkait. Fokus utama tetap pada manfaat yang diterima pembaca.</p>'
                    .'<p>Setelah dipublikasikan, tinjau performa halaman dan perbarui bagian yang kurang relevan agar konten tetap berguna.</p>',
                'category' => 'strategi-digital',
                'status' => PostStatus::Published,
                'published_at' => now()->subDays(4),
                'is_featured' => false,
                'meta_title' => 'Menulis Konten yang Mudah Dibaca dan Dicari — PRIMA Karunia Kreasi',
                'meta_description' => 'Panduan menulis konten yang mudah dibaca sekaligus ramah mesin pencari tanpa mengorbankan relevansi.',
            ],
            [
                'title' => 'Menyiapkan Website untuk Peluncuran Produk',
                'slug' => 'menyiapkan-website-untuk-peluncuran-produk',
                'excerpt' => 'Peluncuran produk membutuhkan halaman yang siap menampung lonjakan pengunjung.',
                'content' => '<p>Peluncuran produk sering diikuti lonjakan trafik dalam waktu singkat. Pastikan halaman utama, halaman produk, dan alur pendaftaran sudah diuji sebelum tanggal peluncuran.</p>'
                    .'<h2>Daftar periksa menjelang peluncuran</h2>'
                    .'<p>Uji formulir, tautan pembayaran, dan kecepatan halaman. Siapkan juga halaman cadangan bila terjadi gangguan layanan.</p>'
                    .'<ul><li>Uji alur utama di perangkat seluler.</li><li>Pantau log kesalahan selama peluncuran.</li><li>Siapkan kanal bantuan yang mudah dihubungi.</li></ul>',
                'category' => 'teknologi',
                'status' => PostStatus::Scheduled,
                'published_at' => now()->addDays(7),
                'is_featured' => false,
                'meta_title' => null,
                'meta_description' => null,
            ],
            [
                'title' => 'Rencana Konten Bulanan yang Realistis',
                'slug' => 'rencana-konten-bulanan-yang-realistis',
                'excerpt' => 'Rencana konten yang realistis membantu tim menjaga konsistensi tanpa kelelahan.',
                'content' => '<p>Rencana konten sebaiknya disusun berdasarkan kapasitas tim, bukan sekadar target jumlah unggahan.</p>'
                    .'<h2>Tentukan tema utama setiap bulan</h2>'
                    .'<p>Pilih satu tema besar, lalu pecah menjadi beberapa topik mingguan. Siapkan cadangan konten agar jadwal tidak terhenti saat ada kendala.</p>',
                'category' => 'desain-kreatif',
                'status' => PostStatus::Draft,
                'published_at' => null,
                'is_featured' => false,
                'meta_title' => null,
                'meta_description' => null,
            ],
        ];

        foreach ($posts as $post) {
            Post::query()->updateOrCreate(
                ['slug' => $post['slug']],
                [
                    'title' => $post['title'],
                    'excerpt' => $post['excerpt'],
                    'content' => $post['content'],
                    'status' => $post['status'],
                    'published_at' => $post['published_at'],
                    'author_id' => $author->id,
                    'category_id' => $categoryIds[$post['category']] ?? null,
                    'is_featured' => $post['is_featured'],
                    'meta_title' => $post['meta_title'],
                    'meta_description' => $post['meta_description'],
                ],
            );
        }
    }
}
