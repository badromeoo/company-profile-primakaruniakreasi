<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HomeContent;
use App\Models\Post;
use App\Support\SeoTitle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RalphJSmit\Laravel\SEO\Schema\BreadcrumbListSchema;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class BlogController extends Controller
{
    public function index(): View
    {
        $contents = HomeContent::query()
            ->whereIn('section_key', ['blog_title', 'blog_text'])
            ->pluck('content_value', 'section_key');

        $posts = Post::query()
            ->published()
            ->with(['category', 'author'])
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        $categories = Category::query()
            ->withCount(['posts' => fn ($query) => $query->published()])
            ->orderBy('name')
            ->get();

        $title = $contents['blog_title'] ?? 'Blog & Insight';
        $description = $contents['blog_text'] ?? 'Insight, tips, dan kabar terbaru seputar desain, teknologi, dan pertumbuhan bisnis digital.';
        $seoTitle = SeoTitle::withoutSuffix($title) ?: $title;

        return view('blog.index', [
            'title' => $title,
            'description' => $description,
            'posts' => $posts,
            'categories' => $categories,
            'seoData' => new SEOData(
                title: $seoTitle,
                description: $description,
                url: route('blog.index'),
                type: 'website',
                schema: $this->breadcrumbSchema([
                    'Home' => route('home'),
                    'Blog' => route('blog.index'),
                    $seoTitle => route('blog.index'),
                ]),
            ),
        ]);
    }

    public function show(Post $post): View
    {
        abort_unless($post->isPublished(), 404);

        $post->load(['category', 'author']);

        return view('blog.show', [
            'post' => $post,
            'related' => $this->relatedPosts($post),
            'isPreview' => false,
            'seoData' => $post->getDynamicSEOData(),
        ]);
    }

    public function category(Category $category): View
    {
        $posts = $category->posts()
            ->published()
            ->with(['category', 'author'])
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('blog.category', [
            'category' => $category,
            'posts' => $posts,
            'seoData' => new SEOData(
                title: SeoTitle::withoutSuffix($category->meta_title ?: $category->name) ?: $category->name,
                description: $category->meta_description ?: $category->description,
                url: route('blog.category', $category),
                type: 'website',
                schema: $this->breadcrumbSchema([
                    'Home' => route('home'),
                    'Blog' => route('blog.index'),
                    $category->name => route('blog.category', $category),
                ]),
            ),
        ]);
    }

    public function preview(Request $request, Post $post): View
    {
        abort_unless($request->user()?->is_admin, 403);

        $post->load(['category', 'author']);

        return view('blog.show', [
            'post' => $post,
            'related' => $this->relatedPosts($post),
            'isPreview' => true,
            'seoData' => $post->getDynamicSEOData()->markAsNoindex(),
        ]);
    }

    /**
     * @param  array<string, string>  $items
     */
    private function breadcrumbSchema(array $items): SchemaCollection
    {
        return SchemaCollection::initialize()->addBreadcrumbs(
            function (BreadcrumbListSchema $breadcrumbs) use ($items): BreadcrumbListSchema {
                $breadcrumbs->breadcrumbs = collect();

                return $breadcrumbs->prependBreadcrumbs($items);
            }
        );
    }

    /**
     * @return Collection<int, Post>
     */
    private function relatedPosts(Post $post): Collection
    {
        return Post::query()
            ->published()
            ->whereKeyNot($post->getKey())
            ->when($post->category_id, fn ($query) => $query->where('category_id', $post->category_id))
            ->latest('published_at')
            ->limit(3)
            ->get();
    }
}
