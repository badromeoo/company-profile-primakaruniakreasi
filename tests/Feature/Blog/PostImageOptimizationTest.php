<?php

namespace Tests\Feature\Blog;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostImageOptimizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_featured_image_is_resized_and_converted_to_webp(): void
    {
        Storage::fake('public');
        $this->storeJpeg('posts/featured.jpg', 2400, 1200);

        $post = Post::factory()->create(['featured_image' => 'posts/featured.jpg']);

        $this->assertSame('posts/featured.webp', $post->fresh()->featured_image);
        Storage::disk('public')->assertExists('posts/featured.webp');
        Storage::disk('public')->assertMissing('posts/featured.jpg');

        $image = getimagesizefromstring(Storage::disk('public')->get('posts/featured.webp'));

        $this->assertIsArray($image);
        $this->assertSame('image/webp', $image['mime']);
        $this->assertSame(1600, $image[0]);
        $this->assertSame(800, $image[1]);
    }

    public function test_open_graph_image_is_capped_at_twelve_hundred_pixels(): void
    {
        Storage::fake('public');
        $this->storeJpeg('posts/og/cover.jpg', 2000, 1000);

        $post = Post::factory()->create(['og_image' => 'posts/og/cover.jpg']);

        $this->assertSame('posts/og/cover.webp', $post->fresh()->og_image);

        $image = getimagesizefromstring(Storage::disk('public')->get('posts/og/cover.webp'));

        $this->assertIsArray($image);
        $this->assertSame(1200, $image[0]);
        $this->assertSame(600, $image[1]);
    }

    public function test_small_webp_images_are_not_reencoded(): void
    {
        Storage::fake('public');
        $bytes = $this->webpBytes(800, 400);
        Storage::disk('public')->put('posts/small.webp', $bytes);

        $post = Post::factory()->create(['featured_image' => 'posts/small.webp']);

        $this->assertSame('posts/small.webp', $post->fresh()->featured_image);
        $this->assertSame($bytes, Storage::disk('public')->get('posts/small.webp'));
    }

    public function test_missing_or_invalid_images_are_left_untouched(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('posts/broken.jpg', 'not an image');

        $missing = Post::factory()->create(['featured_image' => 'posts/missing.jpg']);
        $broken = Post::factory()->create(['featured_image' => 'posts/broken.jpg']);

        $this->assertSame('posts/missing.jpg', $missing->fresh()->featured_image);
        $this->assertSame('posts/broken.jpg', $broken->fresh()->featured_image);
        Storage::disk('public')->assertExists('posts/broken.jpg');
    }

    public function test_command_optimizes_legacy_images(): void
    {
        Storage::fake('public');
        $this->storeJpeg('posts/legacy.jpg', 2000, 1000);

        $post = Post::withoutEvents(fn (): Post => Post::factory()->create(['featured_image' => 'posts/legacy.jpg']));

        $this->artisan('blog:optimize-images')->assertSuccessful();

        $this->assertSame('posts/legacy.webp', $post->fresh()->featured_image);
        Storage::disk('public')->assertExists('posts/legacy.webp');
        Storage::disk('public')->assertMissing('posts/legacy.jpg');
    }

    private function storeJpeg(string $path, int $width, int $height): void
    {
        $image = imagecreatetruecolor($width, $height);
        imagefilledrectangle($image, 0, 0, $width, $height, imagecolorallocate($image, 30, 120, 200));

        ob_start();
        imagejpeg($image, null, 90);
        $bytes = ob_get_clean();
        imagedestroy($image);

        Storage::disk('public')->put($path, $bytes);
    }

    private function webpBytes(int $width, int $height): string
    {
        $image = imagecreatetruecolor($width, $height);
        imagefilledrectangle($image, 0, 0, $width, $height, imagecolorallocate($image, 10, 60, 120));

        ob_start();
        imagewebp($image, null, 82);
        $bytes = ob_get_clean();
        imagedestroy($image);

        return $bytes;
    }
}
