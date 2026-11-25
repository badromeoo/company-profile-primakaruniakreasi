<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\ManageHomeContent;
use App\Models\HomeContent;
use App\Models\User;
use Database\Seeders\HomeContentSeeder;
use Filament\Notifications\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentHomeContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_filament_login_page(): void
    {
        $this->seed(HomeContentSeeder::class);

        $response = $this->get(ManageHomeContent::getUrl());

        $response->assertRedirect(route('filament.cms.auth.login'));
    }

    public function test_non_admin_users_cannot_open_the_home_content_page(): void
    {
        $this->seed(HomeContentSeeder::class);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(ManageHomeContent::getUrl());

        $response->assertForbidden();
    }

    public function test_admin_can_view_the_home_content_page(): void
    {
        $this->seed(HomeContentSeeder::class);
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(ManageHomeContent::getUrl());

        $response
            ->assertOk()
            ->assertSee('Hero Section')
            ->assertSee('About Section')
            ->assertSee('Hero Title')
            ->assertSee('Grow Your Business With Digital Solutions That Work')
            ->assertSee('Save Changes');
    }

    public function test_admin_can_update_home_content_in_bulk(): void
    {
        $this->seed(HomeContentSeeder::class);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        Livewire::test(ManageHomeContent::class)
            ->fillForm([
                'hero_title' => 'Updated Hero Title',
                'about_text' => 'Updated about text for the company profile.',
                'cta_button_text' => 'Contact Us Today',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        Notification::assertNotified('Home page content saved successfully.');

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

    public function test_saving_ignores_unknown_section_keys(): void
    {
        $this->seed(HomeContentSeeder::class);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        Livewire::test(ManageHomeContent::class)
            ->set('data.unknown_section_key', 'This key must not create a row.')
            ->fillForm(['hero_title' => 'Title With Unknown Key'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseCount('home_contents', 16);
        $this->assertDatabaseMissing('home_contents', [
            'section_key' => 'unknown_section_key',
        ]);
        $this->assertDatabaseHas('home_contents', [
            'section_key' => 'hero_title',
            'content_value' => 'Title With Unknown Key',
        ]);
    }

    public function test_home_content_values_are_limited_to_5000_characters(): void
    {
        $this->seed(HomeContentSeeder::class);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        Livewire::test(ManageHomeContent::class)
            ->fillForm(['hero_title' => str_repeat('a', 5001)])
            ->call('save')
            ->assertHasFormErrors(['hero_title' => 'max']);

        $this->assertSame(
            'Grow Your Business With Digital Solutions That Work',
            HomeContent::query()->where('section_key', 'hero_title')->value('content_value'),
        );
    }
}
