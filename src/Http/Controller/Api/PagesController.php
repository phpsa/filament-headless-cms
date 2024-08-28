<?php
namespace Phpsa\FilamentHeadlessCms\Http\Controller\Api;

use Livewire;
use Illuminate\Http\Request;
use Phpsa\FilamentHeadlessCms\FilamentHeadlessCms;
use Phpsa\FilamentHeadlessCms\Models\FhcmsContent;
use Phpsa\FilamentHeadlessCms\Filament\Resources\PageResource;

class PagesController
{

    protected FilamentHeadlessCms $cms;


    public function __construct()
    {
        $this->cms = FilamentHeadlessCms::getPlugin();
    }


    public function index(Request $request, string $type)
    {

        $template = $this->cms->getTemplates()
        ->mapWithKeys(fn($class) => [$class::getTemplateSlug() => $class])
        ->get($type);

        $query = $template::getQueryBuilder()->select($template::indexFields());
        if ($template::$paginate) {
            $limit = min($request->integer('limit', 10), 100);
            /** @var \Illuminate\Pagination\LengthAwarePaginator $results */
            $results = $query->paginate($limit)->appends($request->query())->toArray();

            return response()->json([ ...$results,
                'path' => $template::getPublicPath(),
            ]);
        } else {
            $results = $query->get();
            return response()->json([
                'data' => $results,
                'path' => $template::getPublicPath(),
            ]);
        }
    }

    public function show(string $type, string $slug)
    {

        $template = $this->cms->getTemplates()
        ->mapWithKeys(fn($class) => [$class::getTemplateSlug() => $class])
        ->get($type);

        $record = $template::getQueryBuilder()
            ->whereSlug($slug)
            ->firstOrFail();

        $data = $record->template::apiTransform($record);

        return response()->json([
            'data' => $data,
        ]);
    }
}
