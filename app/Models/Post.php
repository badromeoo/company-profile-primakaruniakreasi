<?php

namespace App\Models;

use App\Enums\PostStatus;
use App\Observers\PostObserver;
use App\Support\SeoTitle;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use RalphJSmit\Laravel\SEO\Schema\BreadcrumbListSchema;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

#[Fillable([
    'title',
    'slug',
    'excerpt',
    'content',
    'featured_image',
    'featured_image_alt',
    'status',
    'published_at',
    'author_id',
    'category_id',
    'is_featured',
    'reading_time',
    'meta_title',
    'meta_description',
    'og_image',
    'canonical_url',
    'noindex',
])]
#[ObservedBy([PostObserver::class])]
class Post extends Model implements Feedable
{
    /** @use HasFactory<PostFactory> */
    use HasFactory, HasSEO, SoftDeletes;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => PostStatus::Draft->value,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PostStatus::class,
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'noindex' => 'boolean',
        ];
    }

    /**
     * Limit the query to posts that are published and whose publish date has passed.
     *
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', PostStatus::Published->value)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function isPublished(): bool
    {
        return $this->status === PostStatus::Published
            && $this->published_at !== null
            && $this->published_at->isPast();
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getDynamicSEOData(): SEOData
    {
        $url = route('blog.show', $this);
        $image = $this->absoluteImageUrl($this->og_image ?: $this->featured_image);
        $title = SeoTitle::withoutSuffix($this->meta_title ?: $this->title) ?: $this->title;

        $seoData = new SEOData(
            title: $title,
            description: $this->meta_description ?: $this->excerpt,
            author: $this->author?->name,
            image: $image,
            url: $url,
            canonical_url: $this->canonical_url ?: $url,
            published_time: $this->published_at,
            modified_time: $this->updated_at,
            articleBody: $this->excerpt,
            section: $this->category?->name,
            type: 'article',
            schema: SchemaCollection::initialize()
                ->add(function (SEOData $data) use ($url, $title): array {
                    $markup = [
                        '@context' => 'https://schema.org',
                        '@type' => 'BlogPosting',
                        'headline' => $title,
                        'description' => $data->description,
                        'datePublished' => $this->published_at?->toIso8601String(),
                        'dateModified' => ($this->updated_at ?? $this->published_at)?->toIso8601String(),
                        'author' => [
                            '@type' => 'Person',
                            'name' => $data->author,
                        ],
                        'publisher' => [
                            '@type' => 'Organization',
                            'name' => config('seo.site_name'),
                            'logo' => [
                                '@type' => 'ImageObject',
                                'url' => asset('images/logo.webp'),
                            ],
                        ],
                        'mainEntityOfPage' => [
                            '@type' => 'WebPage',
                            '@id' => $url,
                        ],
                        'url' => $url,
                        'articleSection' => $this->category?->name,
                        'inLanguage' => app()->getLocale(),
                    ];

                    if ($data->image) {
                        $markup['image'] = [$data->image];
                    }

                    return $markup;
                })
                ->addBreadcrumbs(function (BreadcrumbListSchema $breadcrumbs, SEOData $data): BreadcrumbListSchema {
                    $items = [
                        'Home' => route('home'),
                        'Blog' => route('blog.index'),
                    ];

                    if ($this->category) {
                        $items[$this->category->name] = route('blog.category', $this->category);
                    }

                    $items[$this->title] = $data->url;

                    $breadcrumbs->breadcrumbs = collect();

                    return $breadcrumbs->prependBreadcrumbs($items);
                }),
        );

        if ($this->noindex) {
            $seoData->markAsNoindex();
        }

        return $seoData;
    }

    public function toFeedItem(): FeedItem
    {
        $item = FeedItem::create()
            ->id($this->getKey())
            ->title($this->title)
            ->summary((string) $this->excerpt)
            ->updated($this->published_at ?? $this->updated_at ?? now())
            ->link(route('blog.show', $this))
            ->authorName($this->author?->name ?? (string) config('seo.author.fallback'));

        if ($image = $this->absoluteImageUrl($this->featured_image)) {
            $item->image($image);
        }

        return $item;
    }

    /**
     * @return Collection<int, Post>
     */
    public static function getFeedItems(): Collection
    {
        return static::query()
            ->published()
            ->with('author')
            ->latest('published_at')
            ->limit(20)
            ->get();
    }

    public function absoluteImageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return url(Storage::disk('public')->url($path));
    }
}
