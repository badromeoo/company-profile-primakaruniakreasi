<?php

namespace Tests\Feature\Blog;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_scope_only_includes_posts_that_are_published_and_due(): void
    {
        $published = Post::factory()->published()->create();
        $draft = Post::factory()->create();
        $scheduled = Post::factory()->scheduled()->create();
        $future = Post::factory()->published()->create(['published_at' => now()->addDay()]);
        $missingDate = Post::factory()->create([
            'status' => PostStatus::Published,
            'published_at' => null,
        ]);

        $ids = Post::query()->published()->pluck('id');

        $this->assertCount(1, $ids);
        $this->assertTrue($ids->contains($published->id));
        $this->assertFalse($ids->contains($draft->id));
        $this->assertFalse($ids->contains($scheduled->id));
        $this->assertFalse($ids->contains($future->id));
        $this->assertFalse($ids->contains($missingDate->id));
    }

    public function test_slug_must_be_unique(): void
    {
        Post::factory()->create(['slug' => 'duplicate-slug']);

        $this->expectException(QueryException::class);

        Post::factory()->create(['slug' => 'duplicate-slug']);
    }

    public function test_reading_time_is_calculated_from_the_content(): void
    {
        $content = '<p>'.implode(' ', array_fill(0, 400, 'kata')).'</p>';

        $post = Post::factory()->create(['content' => $content]);

        $this->assertSame(2, $post->reading_time);
    }

    public function test_reading_time_is_zero_for_empty_content(): void
    {
        $post = Post::factory()->create(['content' => '']);

        $this->assertSame(0, $post->reading_time);
    }

    public function test_content_is_sanitized_before_saving(): void
    {
        $post = Post::factory()->create([
            'content' => '<p>Safe <strong>content</strong></p><script>alert("xss")</script>'
                .'<a href="javascript:alert(1)" onclick="alert(2)" target="_blank">link</a>',
        ]);

        $this->assertStringNotContainsString('<script', $post->content);
        $this->assertStringNotContainsString('onclick', $post->content);
        $this->assertStringNotContainsString('javascript:', $post->content);
        $this->assertStringContainsString('<strong>content</strong>', $post->content);
        $this->assertStringContainsString('rel="noopener noreferrer"', $post->content);
    }

    public function test_post_belongs_to_an_author_and_a_category(): void
    {
        $author = User::factory()->admin()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create([
            'author_id' => $author->id,
            'category_id' => $category->id,
        ]);

        $this->assertTrue($post->author->is($author));
        $this->assertTrue($post->category->is($category));
        $this->assertTrue($category->posts->contains($post));
    }

    public function test_admin_factory_state_marks_the_user_as_admin(): void
    {
        $user = User::factory()->admin()->create();

        $this->assertTrue($user->fresh()->is_admin);
    }
}
