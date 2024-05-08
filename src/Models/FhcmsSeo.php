<?php

namespace Phpsa\FilamentHeadlessCms\Models;

use Illuminate\Database\Eloquent\Model;

class FhcmsSeo extends Model
{

    protected $fillable = [
        'fhcms_contents_id',
        'title',
        'keywords',
        'description',
        'image_id',
        'robots'
        //'image', // should use media library
        //'image_alt', // should use media library
        //
    ];

    protected $casts = [
        'keywords' => 'array',
    ];
}
