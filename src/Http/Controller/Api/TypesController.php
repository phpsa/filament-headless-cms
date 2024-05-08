<?php
namespace Phpsa\FilamentHeadlessCms\Http\Controller\Api;

use Illuminate\Http\Request;
use Phpsa\FilamentHeadlessCms\Filament\Resources\PageResource;
use Phpsa\FilamentHeadlessCms\Models\FhcmsContent;

class TypesController
{
    public function __invoke(Request $request)
    {

        $counts = null;
        if ($request->has('with_counts')) {
            $counts = FhcmsContent::query()
             ->wherePublished()
                ->select('template_slug', \DB::raw('count(*) as count'))
                ->groupBy('template_slug')
                ->get()
                ->pluck('count', 'template_slug');
        }

        $types = PageResource::getTemplates()
            ->map(fn($label, $class) => [
                'label' => $label,
                'slug'  => $class::getTemplateSlug()
            ])
            ->when($counts, fn($types) => $types->map(fn($type) => array_merge($type, ['count' => $counts->get($type['slug'], 0)])))
            ->values();

        return response()->json([
            'data' => $types,
        ]);
    }
}
