<?php

namespace App\Http\Middleware;

use App\Models\Post;
use App\Models\PostSlugRedirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectLegacyPostSlug
{
    /**
     * Redirect requests for an old post slug before implicit route model binding runs.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('GET') || ! $request->routeIs('blog.show')) {
            return $next($request);
        }

        $slug = $request->route()?->parameter('post');

        if (! is_string($slug) || $slug === '') {
            return $next($request);
        }

        if (Post::query()->where('slug', $slug)->exists()) {
            return $next($request);
        }

        $redirect = PostSlugRedirect::query()
            ->with('post')
            ->where('old_slug', $slug)
            ->first();

        if ($redirect?->post?->isPublished()) {
            return redirect()->route('blog.show', $redirect->post, 301);
        }

        return $next($request);
    }
}
