<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\HomeContent;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

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
