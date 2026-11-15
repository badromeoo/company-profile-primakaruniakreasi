<?php

namespace App\Observers;

use App\Models\Post;
use App\Support\ImageOptimizer;
use Illuminate\Support\Str;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class PostObserver
{
    /**
     * Average adult reading speed, in words per minute.
     */
    private const WORDS_PER_MINUTE = 200;

    /**
     * Upper bound for sanitized content, in bytes.
     */
    private const MAX_CONTENT_LENGTH = 200_000;

    /**
     * Sanitize the HTML content and recalculate the reading time before saving.
     */
    public function saving(Post $post): void
    {
        $post->content = $this->sanitizer()->sanitize((string) $post->content);
        $post->reading_time = $this->calculateReadingTime($post->content);
    }

    /**
     * Compress and convert uploaded images to WebP after the post has been persisted.
     */
    public function saved(Post $post): void
    {
        $this->optimizeImages($post);
    }

    private function optimizeImages(Post $post): void
    {
        $optimizer = app(ImageOptimizer::class);
        $updates = [];

        foreach (['featured_image' => 1600, 'og_image' => 1200] as $attribute => $maxDimension) {
            $path = $post->getAttribute($attribute);

            if (! is_string($path) || $path === '') {
                continue;
            }

            $optimizedPath = $optimizer->toWebp('public', $path, $maxDimension);

            if ($optimizedPath !== null && $optimizedPath !== $path) {
                $updates[$attribute] = $optimizedPath;
            }
        }

        if ($updates === []) {
            return;
        }

        $timestamps = $post->timestamps;
        $post->timestamps = false;
        $post->forceFill($updates)->saveQuietly();
        $post->timestamps = $timestamps;
    }

    private function calculateReadingTime(string $content): int
    {
        $words = Str::wordCount(strip_tags($content));

        return $words === 0 ? 0 : (int) ceil($words / self::WORDS_PER_MINUTE);
    }

    private function sanitizer(): HtmlSanitizer
    {
        $config = (new HtmlSanitizerConfig)
            ->allowSafeElements()
            ->withMaxInputLength(self::MAX_CONTENT_LENGTH)
            ->allowRelativeLinks()
            ->allowRelativeMedias()
            ->allowAttribute('target', ['a'])
            ->allowAttribute('loading', ['img'])
            ->forceAttribute('a', 'rel', 'noopener noreferrer');

        return new HtmlSanitizer($config);
    }
}
