<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\HomeContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_the_home_content_edit_page(): void
    {
        $response = $this->get(route('admin.home-content.edit'));

        $response->assertRedirect(route('login'));
    }

    public function test_guests_cannot_update_home_content(): void
    {
        $this->seed(HomeContentSeeder::class);

        $response = $this->put(route('admin.home-content.update'), [
            'contents' => ['hero_title' => 'Guest Title'],
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('home_contents', [
            'section_key' => 'hero_title',
            'content_value' => 'Grow Your Business With Digital Solutions That Work',
        ]);
    }

    public function test_authenticated_user_can_update_home_content_in_bulk(): void
    {
        $this->seed(HomeContentSeeder::class);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('admin.home-content.update'), [
            'contents' => [
                'hero_title' => 'Updated Hero Title',
                'about_text' => 'Updated about text for the company profile.',
                'cta_button_text' => 'Contact Us Today',
            ],
        ]);

        $response
            ->assertRedirect(route('admin.home-content.edit'))
            ->assertSessionHas('status', 'home-content-updated');
        $this->assertDatabaseHas('home_contents', [
            'section_key' => 'hero_title',
            'content_value' => 'Updated Hero Title',
            'group' => 'hero',
        ]);
        $this->assertDatabaseHas('home_contents', [
            'section_key' => 'about_text',
            'content_value' => 'Updated about text for the company profile.',
            'group' => 'about',
        ]);
        $this->assertDatabaseHas('home_contents', [
            'section_key' => 'cta_button_text',
            'content_value' => 'Contact Us Today',
            'group' => 'cta',
        ]);
    }

    public function test_update_ignores_unknown_section_keys(): void
    {
        $this->seed(HomeContentSeeder::class);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('admin.home-content.update'), [
            'contents' => [
                'hero_title' => 'Title With Unknown Key',
                'unknown_section_key' => 'This key must not create a row.',
            ],
        ]);

        $response->assertRedirect(route('admin.home-content.edit'));
        $this->assertDatabaseCount('home_contents', 16);
        $this->assertDatabaseMissing('home_contents', [
            'section_key' => 'unknown_section_key',
        ]);
        $this->assertDatabaseHas('home_contents', [
            'section_key' => 'hero_title',
            'content_value' => 'Title With Unknown Key',
        ]);
    }

    public function test_update_rejects_payload_without_a_contents_array(): void
    {
        $this->seed(HomeContentSeeder::class);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('admin.home-content.update'), [
            'contents' => 'Not an array',
        ]);

        $response->assertSessionHasErrors('contents');
        $this->assertDatabaseHas('home_contents', [
            'section_key' => 'hero_title',
            'content_value' => 'Grow Your Business With Digital Solutions That Work',
        ]);
    }

    public function test_update_rejects_content_values_longer_than_the_limit(): void
    {
        $this->seed(HomeContentSeeder::class);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('admin.home-content.update'), [
            'contents' => ['hero_title' => str_repeat('a', 5001)],
        ]);

        $response->assertSessionHasErrors('contents.hero_title');
        $this->assertDatabaseHas('home_contents', [
            'section_key' => 'hero_title',
            'content_value' => 'Grow Your Business With Digital Solutions That Work',
        ]);
    }

    public function test_authenticated_user_can_view_the_home_content_edit_page(): void
    {
        $this->seed(HomeContentSeeder::class);
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->get(route('admin.home-content.edit'));

        $response
            ->assertOk()
            ->assertSee('Hero Section')
            ->assertSee('About Section')
            ->assertSee('Hero Title')
            ->assertSee('contents[hero_title]', false)
            ->assertSee('Grow Your Business With Digital Solutions That Work')
            ->assertSee('Save Changes')
            ->assertSee('Home Content')
            ->assertSee(route('admin.home-content.edit'));
    }

    public function test_edit_page_shows_the_validation_error_with_the_previous_input(): void
    {
        $this->seed(HomeContentSeeder::class);
        $user = User::factory()->create();
        $tooLongValue = str_repeat('a', 5001);

        $response = $this->actingAs($user)
            ->from(route('admin.home-content.edit'))
            ->followingRedirects()
            ->put(route('admin.home-content.update'), [
                'contents' => ['hero_title' => $tooLongValue],
            ]);

        $response
            ->assertOk()
            ->assertSee('The Hero Title field must not be greater than 5000 characters.')
            ->assertSee($tooLongValue);
    }

    public function test_edit_page_confirms_a_successful_update(): void
    {
        $this->seed(HomeContentSeeder::class);
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->followingRedirects()
            ->put(route('admin.home-content.update'), [
                'contents' => ['hero_title' => 'A New Hero Title'],
            ]);

        $response
            ->assertOk()
            ->assertSee('Home page content saved successfully.')
            ->assertSee('A New Hero Title');
    }
}
