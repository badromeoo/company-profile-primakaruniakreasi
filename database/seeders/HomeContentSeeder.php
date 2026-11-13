<?php

namespace Database\Seeders;

use App\Models\HomeContent;
use Illuminate\Database\Seeder;

class HomeContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var list<array{section_key: string, content_value: string, group: string}> $contents */
        $contents = [
            [
                'section_key' => 'hero_title',
                'content_value' => 'Grow Your Business With Digital Solutions That Work',
                'group' => 'hero',
            ],
            [
                'section_key' => 'hero_subtitle',
                'content_value' => 'We help companies build a stronger online presence through thoughtful strategy, reliable technology, and meaningful design.',
                'group' => 'hero',
            ],
            [
                'section_key' => 'hero_button_text',
                'content_value' => 'Explore Our Services',
                'group' => 'hero',
            ],
            [
                'section_key' => 'about_title',
                'content_value' => 'A Trusted Partner For Your Digital Growth',
                'group' => 'about',
            ],
            [
                'section_key' => 'about_text',
                'content_value' => 'We are a collaborative team that turns business goals into useful digital experiences. By combining clear communication with careful execution, we create solutions that support long-term growth.',
                'group' => 'about',
            ],
            [
                'section_key' => 'services_title',
                'content_value' => 'Solutions Designed Around Your Goals',
                'group' => 'services',
            ],
            [
                'section_key' => 'services_subtitle',
                'content_value' => 'Choose the expertise you need to move from an early idea to a digital product that serves your customers well.',
                'group' => 'services',
            ],
            [
                'section_key' => 'service_web_title',
                'content_value' => 'Web Development',
                'group' => 'services',
            ],
            [
                'section_key' => 'service_web_description',
                'content_value' => 'Fast, secure, and scalable websites built around your business processes and your customer needs.',
                'group' => 'services',
            ],
            [
                'section_key' => 'service_branding_title',
                'content_value' => 'Brand Identity',
                'group' => 'services',
            ],
            [
                'section_key' => 'service_branding_description',
                'content_value' => 'Clear and consistent visual identities that make your business recognizable across every customer touchpoint.',
                'group' => 'services',
            ],
            [
                'section_key' => 'service_consulting_title',
                'content_value' => 'Digital Strategy',
                'group' => 'services',
            ],
            [
                'section_key' => 'service_consulting_description',
                'content_value' => 'Practical technology guidance to help you prioritize investments, improve workflows, and plan sustainable growth.',
                'group' => 'services',
            ],
            [
                'section_key' => 'cta_title',
                'content_value' => 'Ready To Take Your Business Further?',
                'group' => 'cta',
            ],
            [
                'section_key' => 'cta_text',
                'content_value' => 'Tell us about your goals and challenges. We will help you identify a practical next step for your digital journey.',
                'group' => 'cta',
            ],
            [
                'section_key' => 'cta_button_text',
                'content_value' => 'Start a Conversation',
                'group' => 'cta',
            ],
        ];

        foreach ($contents as $content) {
            HomeContent::updateOrCreate(
                ['section_key' => $content['section_key']],
                [
                    'content_value' => $content['content_value'],
                    'group' => $content['group'],
                ],
            );
        }
    }
}
