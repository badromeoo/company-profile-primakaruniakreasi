<?php

namespace Tests\Feature\Front;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class BlogControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_only_lists_published_posts(): void
    {
        $published = Post::factory()->published()->create(['title' => 'Artikel Tayang']);
        Post::factory()->create(['title' => 'Artikel Draf']);
        Post::factory()->scheduled()->create(['title' => 'Artikel Terjadwal']);

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee('Artikel Tayang')
            ->assertDontSee('Artikel Draf')
            ->assertDontSee('Artikel Terjadwal')
            ->assertSee(route('blog.show', $published), false);
    }

    public function test_index_paginates_nine_posts(): void
    {
        for ($index = 1; $index <= 10; $index++) {
            Post::factory()->published()->create([
                'title' => sprintf('Artikel %02d', $index),
                'published_at' => now()->subMinutes($index),
            ]);
        }

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee('Artikel 01')
            ->assertDontSee('Artikel 10');

        $this->get(route('blog.index', ['page' => 2]))
            ->assertOk()
            ->assertSee('Artikel 10');
    }

    public function test_show_renders_a_published_post(): void
    {
        $category = Category::factory()->create(['name' => 'Kategori Tayang']);
        $post = Post::factory()->published()->create([
            'title' => 'Judul Artikel Tayang',
            'excerpt' => 'Ringkasan artikel tayang.',
            'content' => '<p>Konten <strong>aman</strong> untuk dibaca.</p>',
            'category_id' => $category->id,
        ]);

        $this->get(route('blog.show', $post))
            ->assertOk()
            ->assertSee('Judul Artikel Tayang')
            ->assertSee('Kategori Tayang')
            ->assertSee('Konten <strong>aman</strong> untuk dibaca.', false)
            ->assertSee('menit baca');
    }

    public function test_show_returns_not_found_for_unpublished_posts(): void
    {
        $draft = Post::factory()->create();
        $scheduled = Post::factory()->scheduled()->create();
        $future = Post::factory()->published()->create(['published_at' => now()->addDay()]);

        $this->get(route('blog.show', $draft))->assertNotFound();
        $this->get(route('blog.show', $scheduled))->assertNotFound();
        $this->get(route('blog.show', $future))->assertNotFound();
    }

    public function test_category_page_only_lists_published_posts_from_that_category(): void
    {
        $category = Category::factory()->create(['name' => 'Kategori Uji', 'slug' => 'kategori-uji']);
        $otherCategory = Category::factory()->create(['name' => 'Kategori Lain', 'slug' => 'kategori-lain']);

        Post::factory()->published()->create(['title' => 'Artikel Kategori Uji', 'category_id' => $category->id]);
        Post::factory()->create(['title' => 'Draf Kategori Uji', 'category_id' => $category->id]);
        Post::factory()->published()->create(['title' => 'Artikel Kategori Lain', 'category_id' => $otherCategory->id]);

        $this->get(route('blog.category', $category))
            ->assertOk()
            ->assertSee('Artikel Kategori Uji')
            ->assertDontSee('Draf Kategori Uji')
            ->assertDontSee('Artikel Kategori Lain');
    }

    public function test_preview_requires_authentication_a_valid_signature_and_an_admin_user(): void
    {
        $draft = Post::factory()->create(['title' => 'Draf Preview']);
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $signedUrl = URL::signedRoute('blog.preview', ['post' => $draft]);

        $this->get($signedUrl)->assertRedirect(route('login'));
        $this->actingAs($user)->get($signedUrl)->assertForbidden();
        $this->actingAs($admin)->get(route('blog.preview', $draft))->assertForbidden();
        $this->actingAs($admin)->get($signedUrl)->assertOk()->assertSee('Draf Preview');
    }

    public function test_preview_page_is_marked_as_noindex(): void
    {
        $draft = Post::factory()->create();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(URL::signedRoute('blog.preview', ['post' => $draft]))
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    public function test_public_navigation_links_to_the_blog_index(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('blog.index'), false);
    }
}
