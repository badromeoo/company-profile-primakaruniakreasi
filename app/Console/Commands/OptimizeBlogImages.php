<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Support\ImageOptimizer;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('blog:optimize-images {--dry-run : Show the images that would be optimized without changing any file}')]
#[Description('Compress blog images and convert them to WebP')]
class OptimizeBlogImages extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(ImageOptimizer $optimizer): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $optimized = 0;
        $skipped = 0;

        Post::query()
            ->withTrashed()
            ->orderBy('id')
            ->chunkById(100, function ($posts) use ($optimizer, $dryRun, &$optimized, &$skipped): void {
                foreach ($posts as $post) {
                    foreach (['featured_image' => [1600, 900], 'og_image' => [1200, 630]] as $attribute => [$width, $height]) {
                        $path = $post->getAttribute($attribute);

                        if (! is_string($path) || $path === '') {
                            continue;
                        }

                        if ($dryRun) {
                            $this->line("Post #{$post->getKey()}: {$attribute} → {$path}");
                            $optimized++;

                            continue;
                        }

                        $newPath = $optimizer->toWebpFixed('public', $path, $width, $height);

                        if ($newPath === null || $newPath === $path) {
                            $skipped++;

                            continue;
                        }

                        $timestamps = $post->timestamps;
                        $post->timestamps = false;
                        $post->forceFill([$attribute => $newPath])->saveQuietly();
                        $post->timestamps = $timestamps;

                        $optimized++;
                        $this->line("Post #{$post->getKey()}: {$path} → {$newPath}");
                    }
                }
            });

        $this->info("Optimized: {$optimized}; skipped: {$skipped}");

        return self::SUCCESS;
    }
}
