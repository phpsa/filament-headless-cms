<?php
namespace Phpsa\FilamentHeadlessCms\Http\Controller\Api;

use Livewire;
use Illuminate\Http\Request;
use Phpsa\FilamentHeadlessCms\FilamentHeadlessCms;
use Phpsa\FilamentHeadlessCms\Models\FhcmsContent;
use Phpsa\FilamentHeadlessCms\Filament\Resources\PageResource;

class PagesController
{
    public function index(Request $request, string $type)
    {

        $limit = min($request->integer('limit', 10), 100);

        $records = FilamentHeadlessCms::getPlugin()->getModel()::query()
            ->select(['title',
                'slug',
                'published_at',
                'published_until',
            ])
            ->wherePublished()
            ->whereTemplateSlug($type)
            // ->when(
            //     $request->has('filter'),
            //     fn ($query) =>$query->withFilter($request->get('filter'))
            // )
            ->paginate($limit);

        return response()->json([
            'data' => $records,
        ]);
    }

    public function show(string $type, string $slug)
    {

        $record = FilamentHeadlessCms::getPlugin()->getModel()::query()
            ->with('seo:fhcms_contents_id,title,robots,keywords,description')
            ->wherePublished()
            ->whereTemplateSlug($type)
            ->whereSlug($slug)
            ->firstOrFail();

        $data = $record->template::apiTransform($record);

        return response()->json([
            'data' => $data,
        ]);
    }
}
