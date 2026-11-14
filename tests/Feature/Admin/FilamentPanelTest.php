<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_filament_login_page(): void
    {
        $response = $this->get('/admin/cms');

        $response->assertRedirect(route('filament.cms.auth.login'));
    }

    public function test_filament_login_page_is_reachable(): void
    {
        $response = $this->get(route('filament.cms.auth.login'));

        $response->assertOk();
    }

    public function test_authenticated_non_admin_users_cannot_access_the_cms_panel(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/cms');

        $response->assertForbidden();
    }

    public function test_admin_users_can_access_the_cms_panel(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin/cms');

        $response->assertOk();
    }
}
