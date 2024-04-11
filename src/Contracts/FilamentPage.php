<?php
namespace Phpsa\FilamentHeadlessCms\Contracts;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $template
 * @property array $data
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $published_until
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read string $url
 *
 * @phpstan-require-extends \Illuminate\Database\Eloquent\Model
 */
interface FilamentPage
{

    public function getUrlAttribute(): string;
}
