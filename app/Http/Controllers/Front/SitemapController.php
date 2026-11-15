<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HomeContent;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class SitemapController extends Controller
{
    /**
     * Render the public XML sitemap for static pages, categories, and published posts.
     */
    public function __invoke(Request $request): SymfonyResponse
    {
        $homeLastModified = HomeContent::query()->max('updated_at');
        $blogLastModified = Post::query()->published()->max('updated_at');

        $sitemap = Sitemap::create()
            ->add(
                Url::create(route('home'))
                    ->setLastModificationDate($this->toCarbon($homeLastModified))
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(1.0)
            )
            ->add(
                Url::create(route('blog.index'))
                    ->setLastModificationDate($this->toCarbon($blogLastModified))
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                    ->setPriority(0.9)
            );

        Category::query()
            ->orderBy('name')
            ->get(['id', 'slug', 'updated_at'])
            ->each(function (Category $category) use ($sitemap): void {
                $sitemap->add(
                    Url::create(route('blog.category', $category))
                        ->setLastModificationDate($this->toCarbon($category->updated_at))
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.7)
                );
            });

        Post::query()
            ->published()
            ->latest('published_at')
            ->get(['slug', 'updated_at'])
            ->each(function (Post $post) use ($sitemap): void {
                $sitemap->add(
                    Url::create(route('blog.show', $post))
                        ->setLastModificationDate($this->toCarbon($post->updated_at))
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                        ->setPriority(0.8)
                );
            });

        return $sitemap->toResponse($request);
    }

    private function toCarbon(mixed $date): Carbon
    {
        return $date ? Carbon::parse($date) : now();
    }
}
