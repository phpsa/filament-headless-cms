<?php

namespace Phpsa\FilamentHeadlessCms\Models;

use Spatie\Tags\HasTags;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Phpsa\FilamentHeadlessCms\FilamentHeadlessCms;
use Phpsa\FilamentHeadlessCms\Contracts\FilamentPage;

if (trait_exists(\Laravel\Scout\Searchable::class)) {
    abstract class BasePageModel extends Model
    {
        use \Laravel\Scout\Searchable;

        public function searchableAs(): string
        {
            return 'fhcms_index' . $this->template_slug;
        }

        public function toSearchableArray(): array
        {
            return $this->template::toSearchableArray($this);
        }
    }
} else {
    abstract class BasePageModel extends Model
    {
    }
}


class FhcmsContent extends BasePageModel implements FilamentPage
{
    use HasTags;

    protected $table = 'fhcms_contents';

    protected $fillable = [
        'title',
        'slug',
        'template',
        'template_slug',
        'data',
        'published_at',
        'published_until',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected $casts = [
        'data'            => 'json',
        'published_at'    => 'immutable_datetime',
        'published_until' => 'immutable_datetime',
    ];

    public function getUrlAttribute(): string
    {
        return FilamentHeadlessCms::getPlugin()->generateUrl($this->template_slug . '/' . $this->slug);
    }

    public function Seo(): HasOne
    {
        return $this->hasOne(FhcmsSeo::class, 'fhcms_contents_id', 'id');
    }

    public function scopeWherePublished(Builder $query)
    {
        return $query->where('published_at', '<=', now())
            ->where(function ($query) {
                $query->whereNull('published_until')
                    ->orWhere('published_until', '>=', now());
            });
    }

    public function scopeWithFilter(Builder $query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%');
            });
        })
        ->when($filters['tags'] ?? null, function ($query, $tags) {
                $query->withAnyTags($tags);
        });
    }
}
