<?php

namespace Tests\Feature\Front;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogSlugRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_changing_a_post_slug_creates_a_permanent_redirect(): void
    {
        $post = Post::factory()->published()->create([
            'title' => 'Artikel Slug',
            'slug' => 'slug-lama',
        ]);

        $post->update(['slug' => 'slug-baru']);

        $this->assertDatabaseHas('post_slug_redirects', [
            'old_slug' => 'slug-lama',
            'post_id' => $post->id,
        ]);

        $this->get(url('/blog/slug-lama'))
            ->assertStatus(301)
            ->assertRedirect(route('blog.show', $post));

        $this->get(route('blog.show', $post))->assertOk();
    }

    public function test_latest_slug_is_used_when_a_slug_changes_more_than_once(): void
    {
        $post = Post::factory()->published()->create(['slug' => 'slug-awal']);

        $post->update(['slug' => 'slug-tengah']);
        $post->update(['slug' => 'slug-akhir']);

        $this->get(url('/blog/slug-awal'))
            ->assertStatus(301)
            ->assertRedirect(route('blog.show', $post));

        $this->assertDatabaseHas('post_slug_redirects', [
            'old_slug' => 'slug-tengah',
            'post_id' => $post->id,
        ]);
    }

    public function test_unknown_slug_returns_not_found(): void
    {
        $this->get(url('/blog/tidak-ada'))->assertNotFound();
    }

    public function test_redirect_is_not_used_when_the_target_is_not_published(): void
    {
        $post = Post::factory()->published()->create(['slug' => 'slug-publik']);
        $post->update(['slug' => 'slug-baru']);
        $post->update(['status' => PostStatus::Draft, 'published_at' => null]);

        $this->get(url('/blog/slug-publik'))->assertNotFound();
    }
}
