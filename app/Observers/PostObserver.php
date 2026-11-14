<?php

namespace App\Observers;

use App\Models\Post;
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
