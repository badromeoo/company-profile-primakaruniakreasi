<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Posts\Pages\EditPost;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentPostUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_uploaded_featured_image_is_converted_and_kept_in_the_form_state(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create();

        $this->actingAs($admin);

        $component = Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
            ->fillForm([
                'featured_image' => UploadedFile::fake()->image('featured.jpg', 2400, 1350),
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $post->refresh();

        $this->assertIsString($post->featured_image);
        $this->assertStringEndsWith('.webp', $post->featured_image);
        Storage::disk('public')->assertExists($post->featured_image);

        $image = getimagesizefromstring(Storage::disk('public')->get($post->featured_image));

        $this->assertIsArray($image);
        $this->assertSame(1600, $image[0]);
        $this->assertSame(900, $image[1]);

        // The form state must reference the same stored file, otherwise the upload box goes blank after save.
        $component->assertFormSet([
            'featured_image' => $post->featured_image,
        ]);
    }

    public function test_non_16_9_uploads_are_cropped_instead_of_rejected(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create();

        $this->actingAs($admin);

        $component = Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
            ->fillForm([
                'featured_image' => UploadedFile::fake()->image('featured.jpg', 2000, 1000),
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $post->refresh();

        $this->assertIsString($post->featured_image);
        $this->assertStringEndsWith('.webp', $post->featured_image);

        $image = getimagesizefromstring(Storage::disk('public')->get($post->featured_image));

        $this->assertIsArray($image);
        $this->assertSame(1600, $image[0]);
        $this->assertSame(900, $image[1]);

        $component->assertFormSet([
            'featured_image' => $post->featured_image,
        ]);
    }
}
