<?php

namespace Tests\Feature;

use Database\Seeders\HomeContentSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class HomeContentSeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_default_home_content_is_seeded_for_each_section_group(): void
    {
        $this->seed(HomeContentSeeder::class);

        $this->assertDatabaseCount('home_contents', 16);
        $this->assertDatabaseHas('home_contents', [
            'section_key' => 'hero_title',
            'content_value' => 'Grow Your Business With Digital Solutions That Work',
            'group' => 'hero',
        ]);
        $this->assertDatabaseHas('home_contents', [
            'section_key' => 'about_text',
            'group' => 'about',
        ]);
        $this->assertDatabaseHas('home_contents', [
            'section_key' => 'services_title',
            'group' => 'services',
        ]);
        $this->assertDatabaseHas('home_contents', [
            'section_key' => 'cta_button_text',
            'content_value' => 'Start a Conversation',
            'group' => 'cta',
        ]);
    }

    public function test_seeding_twice_does_not_create_duplicate_content(): void
    {
        $this->seed(HomeContentSeeder::class);
        $this->seed(HomeContentSeeder::class);

        $this->assertDatabaseCount('home_contents', 16);
    }
}
