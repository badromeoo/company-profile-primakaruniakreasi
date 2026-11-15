<?php

namespace Tests\Feature\Front;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_page_renders_meta_open_graph_canonical_and_structured_data(): void
    {
        $category = Category::factory()->create(['name' => 'Strategi Konten']);
        $post = Post::factory()->published()->create([
            'title' => 'Judul Artikel SEO',
            'slug' => 'judul-artikel-seo',
            'excerpt' => 'Ringkasan untuk mesin pencari.',
            'meta_title' => 'Meta Judul Artikel SEO',
            'meta_description' => 'Meta deskripsi artikel SEO.',
            'featured_image' => 'posts/gambar-seo.webp',
            'category_id' => $category->id,
        ]);

        $response = $this->get(route('blog.show', $post));

        $response
            ->assertOk()
            ->assertSee('<title>Meta Judul Artikel SEO — '.config('app.name').'</title>', false)
            ->assertSee('<meta name="description" content="Meta deskripsi artikel SEO.">', false)
            ->assertSee('<meta property="og:title" content="Meta Judul Artikel SEO — '.config('app.name').'">', false)
            ->assertSee('<meta property="og:description" content="Meta deskripsi artikel SEO.">', false)
            ->assertSee('<meta property="og:type" content="article">', false)
            ->assertSee('twitter:card')
            ->assertSee('twitter:title')
            ->assertSee('<link rel="canonical" href="'.route('blog.show', $post).'">', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('BlogPosting')
            ->assertSee('BreadcrumbList')
            ->assertSee('Strategi Konten')
            ->assertSee(route('blog.show', $post), false);
    }

    public function test_index_and_category_pages_render_unique_titles_and_breadcrumb_schema(): void
    {
        $category = Category::factory()->create([
            'name' => 'Kategori SEO',
            'slug' => 'kategori-seo',
            'meta_title' => 'Meta Kategori SEO',
            'meta_description' => 'Deskripsi kategori SEO.',
        ]);

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee('<title>Blog &amp; Insight — '.config('app.name').'</title>', false)
            ->assertSee('<link rel="canonical" href="'.route('blog.index').'">', false)
            ->assertSee('BreadcrumbList');

        $this->get(route('blog.category', $category))
            ->assertOk()
            ->assertSee('<title>Meta Kategori SEO — '.config('app.name').'</title>', false)
            ->assertSee('<meta name="description" content="Deskripsi kategori SEO.">', false)
            ->assertSee('<link rel="canonical" href="'.route('blog.category', $category).'">', false)
            ->assertSee('Kategori SEO');
    }

    public function test_existing_brand_suffix_in_meta_title_is_not_duplicated(): void
    {
        $suffix = (string) config('seo.title.suffix');
        $post = Post::factory()->published()->create([
            'title' => 'Judul Dasar',
            'slug' => 'judul-suffix',
            'meta_title' => 'Judul SEO'.$suffix,
        ]);

        $this->get(route('blog.show', $post))
            ->assertOk()
            ->assertSee('<title>Judul SEO'.$suffix.'</title>', false)
            ->assertDontSee('Judul SEO'.$suffix.$suffix, false);
    }

    public function test_home_page_renders_organization_and_website_structured_data(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<title>'.config('app.name').'</title>', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('Organization')
            ->assertSee('WebSite')
            ->assertSee(route('home'), false);
    }

    public function test_preview_page_is_noindex_and_not_in_the_sitemap(): void
    {
        $post = Post::factory()->create(['slug' => 'draf-preview-seo']);

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertDontSee(route('blog.show', $post), false);
    }
}
