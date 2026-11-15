<?php

namespace Tests\Feature\Front;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogFeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_feed_contains_only_published_posts(): void
    {
        $post = Post::factory()->published()->create([
            'title' => 'Artikel Feed',
            'slug' => 'artikel-feed',
            'excerpt' => 'Ringkasan artikel feed.',
        ]);
        Post::factory()->create(['title' => 'Draf Feed']);

        $response = $this->get(route('feeds.main'));

        $response
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml;charset=UTF-8')
            ->assertSee('<rss', false)
            ->assertSee('Artikel Feed')
            ->assertSee('Ringkasan artikel feed.')
            ->assertDontSee('Draf Feed')
            ->assertSee(route('blog.show', $post), false);
    }

    public function test_public_layout_links_to_the_feed(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<link rel="alternate" type="application/rss+xml"', false)
            ->assertSee(route('feeds.main'), false);
    }
}
