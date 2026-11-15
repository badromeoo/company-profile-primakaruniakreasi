<?php

namespace Tests\Feature\Front;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogSitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_lists_static_pages_published_posts_and_categories(): void
    {
        $category = Category::factory()->create(['slug' => 'kategori-sitemap']);
        $post = Post::factory()->published()->create(['slug' => 'posting-sitemap']);
        $draft = Post::factory()->create(['slug' => 'draf-sitemap']);

        $response = $this->get(route('sitemap'));

        $response
            ->assertOk()
            ->assertHeader('Content-Type', 'text/xml; charset=utf-8')
            ->assertSee('<urlset', false)
            ->assertSee(route('home'), false)
            ->assertSee(route('blog.index'), false)
            ->assertSee(route('blog.category', $category), false)
            ->assertSee(route('blog.show', $post), false)
            ->assertDontSee($draft->slug);
    }

    public function test_sitemap_includes_last_modified_dates(): void
    {
        $post = Post::factory()->published()->create();

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertSee('<lastmod>', false)
            ->assertSee($post->updated_at->toAtomString(), false);
    }
}
