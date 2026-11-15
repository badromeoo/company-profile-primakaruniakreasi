<?php

namespace Tests\Feature\Blog;

use App\Models\Post;
use App\Support\ImageOptimizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostImageOptimizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_optimizer_crops_and_resizes_featured_images_to_1600_by_900(): void
    {
        Storage::fake('public');
        $this->storeJpeg('posts/featured.jpg', 2400, 1200);

        $newPath = $this->optimizer()->toWebpFixed('public', 'posts/featured.jpg', 1600, 900);

        $this->assertSame('posts/featured.webp', $newPath);
        Storage::disk('public')->assertExists('posts/featured.webp');
        Storage::disk('public')->assertMissing('posts/featured.jpg');
        $this->assertImageSize('posts/featured.webp', 1600, 900);
    }

    public function test_optimizer_crops_open_graph_images_to_1200_by_630(): void
    {
        Storage::fake('public');
        $this->storeJpeg('posts/og/cover.jpg', 2000, 1000);

        $newPath = $this->optimizer()->toWebpFixed('public', 'posts/og/cover.jpg', 1200, 630);

        $this->assertSame('posts/og/cover.webp', $newPath);
        Storage::disk('public')->assertMissing('posts/og/cover.jpg');
        $this->assertImageSize('posts/og/cover.webp', 1200, 630);
    }

    public function test_optimizer_upscales_smaller_images_to_the_target_size(): void
    {
        Storage::fake('public');
        $this->storeJpeg('posts/small.jpg', 800, 400);

        $newPath = $this->optimizer()->toWebpFixed('public', 'posts/small.jpg', 1600, 900);

        $this->assertSame('posts/small.webp', $newPath);
        $this->assertImageSize('posts/small.webp', 1600, 900);
    }

    public function test_optimizer_skips_webp_images_that_already_match_the_target_size(): void
    {
        Storage::fake('public');
        $bytes = $this->webpBytes(1600, 900);
        Storage::disk('public')->put('posts/fixed.webp', $bytes);

        $newPath = $this->optimizer()->toWebpFixed('public', 'posts/fixed.webp', 1600, 900);

        $this->assertNull($newPath);
        $this->assertSame($bytes, Storage::disk('public')->get('posts/fixed.webp'));
    }

    public function test_optimizer_leaves_missing_or_invalid_images_untouched(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('posts/broken.jpg', 'not an image');

        $this->assertNull($this->optimizer()->toWebpFixed('public', 'posts/missing.jpg', 1600, 900));
        $this->assertNull($this->optimizer()->toWebpFixed('public', 'posts/broken.jpg', 1600, 900));
        Storage::disk('public')->assertExists('posts/broken.jpg');
    }

    public function test_saving_a_post_does_not_rename_the_featured_image(): void
    {
        Storage::fake('public');
        $this->storeJpeg('posts/legacy.jpg', 800, 400);

        $post = Post::factory()->create(['featured_image' => 'posts/legacy.jpg']);

        $this->assertSame('posts/legacy.jpg', $post->fresh()->featured_image);
        Storage::disk('public')->assertExists('posts/legacy.jpg');
    }

    public function test_command_optimizes_legacy_images_to_fixed_sizes(): void
    {
        Storage::fake('public');
        $this->storeJpeg('posts/legacy.jpg', 2400, 1200);

        $post = Post::withoutEvents(fn (): Post => Post::factory()->create(['featured_image' => 'posts/legacy.jpg']));

        $this->artisan('blog:optimize-images')->assertSuccessful();

        $this->assertSame('posts/legacy.webp', $post->fresh()->featured_image);
        Storage::disk('public')->assertMissing('posts/legacy.jpg');
        $this->assertImageSize('posts/legacy.webp', 1600, 900);
    }

    private function optimizer(): ImageOptimizer
    {
        return app(ImageOptimizer::class);
    }

    private function assertImageSize(string $path, int $width, int $height): void
    {
        $image = getimagesizefromstring(Storage::disk('public')->get($path));

        $this->assertIsArray($image);
        $this->assertSame('image/webp', $image['mime']);
        $this->assertSame($width, $image[0]);
        $this->assertSame($height, $image[1]);
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
