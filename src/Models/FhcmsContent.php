<?php

namespace Phpsa\FilamentHeadlessCms\Models;

use Illuminate\Database\Eloquent\Model;
use Phpsa\FilamentHeadlessCms\FilamentHeadlessCms;
use Phpsa\FilamentHeadlessCms\Contracts\FilamentPage;

class FhcmsContent extends Model implements FilamentPage
{
    protected $fillable = [
        'title',
        'slug',
        'template',
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
}
