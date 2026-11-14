<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_cms_and_home_content_shortcuts(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertSee('CMS')
            ->assertSee('Home Content')
            ->assertSee(url('/admin/cms'), false)
            ->assertSee(route('admin.home-content.edit'), false)
            ->assertSee('Buka CMS');
    }

    public function test_non_admin_dashboard_hides_admin_shortcuts_and_menu_items(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertDontSee(route('admin.home-content.edit'), false)
            ->assertDontSee(url('/admin/cms'), false)
            ->assertDontSee('Home Content')
            ->assertSee('Selamat datang di dashboard.');
    }
}
