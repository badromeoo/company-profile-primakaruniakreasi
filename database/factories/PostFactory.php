<?php

namespace Database\Factories;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = Str::title(fake()->unique()->sentence(6));

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->paragraph(),
            'content' => collect(range(1, 4))
                ->map(fn (): string => '<p>'.fake()->paragraph(4).'</p>')
                ->implode(''),
            'featured_image' => null,
            'featured_image_alt' => null,
            'status' => PostStatus::Draft,
            'published_at' => null,
            'author_id' => User::factory(),
            'category_id' => Category::factory(),
            'is_featured' => false,
            'reading_time' => 0,
            'meta_title' => null,
            'meta_description' => null,
            'og_image' => null,
            'canonical_url' => null,
            'noindex' => false,
        ];
    }

    /**
     * Indicate that the post is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);
    }

    /**
     * Indicate that the post is scheduled for the future.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::Scheduled,
            'published_at' => now()->addWeek(),
        ]);
    }
}
