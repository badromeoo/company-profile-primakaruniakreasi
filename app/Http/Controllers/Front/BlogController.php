<?php

namespace App\Http\Controllers\Front;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HomeContent;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\View\View;

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

        return view('blog.index', [
            'title' => $contents['blog_title'] ?? 'Blog & Insight',
            'description' => $contents['blog_text'] ?? 'Insight, tips, dan kabar terbaru seputar desain, teknologi, dan pertumbuhan bisnis digital.',
            'posts' => $posts,
            'categories' => $categories,
        ]);
    }

    public function show(Post $post): View
    {
        abort_unless($this->isPublished($post), 404);

        $post->load(['category', 'author']);

        return view('blog.show', [
            'post' => $post,
            'related' => $this->relatedPosts($post),
            'isPreview' => false,
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
        ]);
    }

    private function isPublished(Post $post): bool
    {
        return $post->status === PostStatus::Published
            && $post->published_at !== null
            && $post->published_at->isPast();
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
