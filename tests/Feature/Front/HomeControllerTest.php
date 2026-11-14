<?php

namespace Tests\Feature\Front;

use App\Models\HomeContent;
use Database\Seeders\HomeContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_displays_the_company_profile_content_from_the_database(): void
    {
        $this->seed(HomeContentSeeder::class);

        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('Grow Your Business With Digital Solutions That Work')
            ->assertSee('A Trusted Partner For Your Digital Growth')
            ->assertSee('Web Development')
            ->assertSee('Brand Identity')
            ->assertSee('Digital Strategy')
            ->assertSee('Start a Conversation');
    }

    public function test_home_page_pairs_each_service_title_with_its_description(): void
    {
        HomeContent::create([
            'section_key' => 'service_seo_title',
            'content_value' => 'Search Engine Optimization',
            'group' => 'services',
        ]);
        HomeContent::create([
            'section_key' => 'service_seo_description',
            'content_value' => 'Help customers find your business in search results.',
            'group' => 'services',
        ]);

        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('Search Engine Optimization')
            ->assertSee('Help customers find your business in search results.');
    }

    public function test_home_page_falls_back_to_default_copy_when_content_is_missing(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('Welcome to Our Company')
            ->assertSee('What We Do')
            ->assertSee('Get in Touch')
            ->assertDontSee('Grow Your Business With Digital Solutions That Work');
    }

    public function test_home_page_reflects_updated_content(): void
    {
        $this->seed(HomeContentSeeder::class);

        HomeContent::query()
            ->where('section_key', 'hero_title')
            ->update(['content_value' => 'Updated Public Hero Title']);

        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('Updated Public Hero Title')
            ->assertDontSee('Grow Your Business With Digital Solutions That Work');
    }
}
