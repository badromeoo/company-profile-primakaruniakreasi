<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Posts\PostResource;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FilamentResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_browse_posts_and_categories(): void
    {
        $admin = User::factory()->admin()->create();
        Post::factory()->published()->create(['title' => 'Judul Post Uji']);
        Category::factory()->create(['name' => 'Kategori Uji']);

        $this->actingAs($admin)
            ->get(PostResource::getUrl('index'))
            ->assertOk()
            ->assertSee('Judul Post Uji');

        $this->actingAs($admin)
            ->get(CategoryResource::getUrl('index'))
            ->assertOk()
            ->assertSee('Kategori Uji');
    }

    public function test_admin_can_open_the_create_post_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(PostResource::getUrl('create'))
            ->assertOk()
            ->assertSee('Content')
            ->assertSee('Media')
            ->assertSee('SEO')
            ->assertSee('Publish');
    }

    public function test_blog_image_fields_mark_the_uploader_and_load_the_idle_label_fix(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create();

        $this->actingAs($admin)
            ->get(PostResource::getUrl('edit', ['record' => $post]))
            ->assertOk()
            ->assertSee('blog-media-upload', false)
            ->assertSee('.filepond--root:has(.filepond--item) .filepond--drop-label', false);
    }

    public function test_non_admin_users_cannot_browse_filament_resources(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(PostResource::getUrl('index'))->assertForbidden();
        $this->actingAs($user)->get(CategoryResource::getUrl('index'))->assertForbidden();
    }

    public function test_public_storage_urls_are_relative_to_the_current_host(): void
    {
        $this->assertSame('/storage/posts/example.webp', Storage::disk('public')->url('posts/example.webp'));
    }

    public function test_categories_used_by_posts_cannot_be_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        Post::factory()->create(['category_id' => $category->id]);

        $this->assertTrue(Gate::forUser($admin)->allows('update', $category));
        $this->assertFalse(Gate::forUser($admin)->allows('delete', $category));
        $this->assertFalse(Gate::forUser($admin)->allows('forceDelete', $category));
    }

    public function test_unused_categories_can_only_be_deleted_by_admins(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $this->assertTrue(Gate::forUser($admin)->allows('delete', $category));
        $this->assertFalse(Gate::forUser($user)->allows('delete', $category));
    }
}
