<?php

namespace App\Models;

use App\Enums\PostStatus;
use App\Observers\PostObserver;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory, SoftDeletes;

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
}
