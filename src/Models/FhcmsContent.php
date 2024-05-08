<?php

namespace Phpsa\FilamentHeadlessCms\Models;

use Spatie\Tags\HasTags;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Phpsa\FilamentHeadlessCms\FilamentHeadlessCms;
use Phpsa\FilamentHeadlessCms\Contracts\FilamentPage;

class FhcmsContent extends Model implements FilamentPage
{
    use HasTags;

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
        return FilamentHeadlessCms::getPlugin()->generateUrl($this->slug);
    }

    public function Seo(): HasOne
    {
        return $this->hasOne(FhcmsSeo::class, 'fhcms_contents_id', 'id');
    }
}
