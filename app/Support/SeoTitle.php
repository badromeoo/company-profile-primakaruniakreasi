<?php

namespace App\Support;

use Illuminate\Support\Str;

class SeoTitle
{
    /**
     * Remove the configured title suffix so the SEO package can append it exactly once.
     */
    public static function withoutSuffix(?string $title): string
    {
        $title = trim((string) $title);
        $suffix = (string) config('seo.title.suffix');

        if ($title === '' || $suffix === '') {
            return $title;
        }

        if (str_ends_with($title, $suffix)) {
            return trim(Str::beforeLast($title, $suffix));
        }

        return $title;
    }
}
