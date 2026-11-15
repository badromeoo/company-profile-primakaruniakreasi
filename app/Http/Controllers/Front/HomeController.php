<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\HomeContent;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class HomeController extends Controller
{
    /**
     * Display the public company profile page.
     */
    public function __invoke(): View
    {
        $contents = HomeContent::query()
            ->orderBy('id')
            ->pluck('content_value', 'section_key');

        return view('home', [
            'contents' => $contents,
            'services' => $this->serviceCards($contents),
            'seoData' => new SEOData(
                title: config('app.name', 'Laravel'),
                description: $contents['hero_subtitle'] ?? 'PRIMA Karunia Kreasi membantu bisnis membangun kehadiran digital melalui strategi, desain, dan teknologi.',
                image: asset('images/background-web.webp'),
                url: route('home'),
                enableTitleSuffix: false,
                type: 'website',
                schema: SchemaCollection::initialize()
                    ->add(fn (SEOData $data): array => [
                        '@context' => 'https://schema.org',
                        '@type' => 'Organization',
                        'name' => config('seo.site_name'),
                        'url' => route('home'),
                        'description' => $data->description,
                        'logo' => [
                            '@type' => 'ImageObject',
                            'url' => asset('images/logo.webp'),
                        ],
                    ])
                    ->add(fn (): array => [
                        '@context' => 'https://schema.org',
                        '@type' => 'WebSite',
                        'name' => config('seo.site_name'),
                        'url' => route('home'),
                        'inLanguage' => app()->getLocale(),
                    ]),
            ),
        ]);
    }

    /**
     * Build the service cards from the keyed content map.
     *
     * @param  Collection<string, string>  $contents
     * @return Collection<int, array{title: string, description: string}>
     */
    private function serviceCards(Collection $contents): Collection
    {
        return $contents
            ->filter(fn (string $value, string $key): bool => Str::startsWith($key, 'service_')
                && Str::endsWith($key, '_title'))
            ->map(fn (string $title, string $key): array => [
                'title' => $title,
                'description' => (string) $contents->get(Str::replaceLast('_title', '_description', $key), ''),
            ])
            ->values();
    }
}
