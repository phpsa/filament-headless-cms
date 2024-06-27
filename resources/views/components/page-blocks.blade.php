@props(['blocks' => []])

@if(is_array($blocks))
    @foreach ($blocks as $blockData)
        @php
            $pageBlock = Phpsa\FilamentHeadlessCms\Facades\FilamentCmsPageBlocks::getPageBlockFromName($blockData['type'])
        @endphp

        @isset($pageBlock)
            <x-dynamic-component
                :component="$pageBlock::getComponent()"
                :attributes="new \Illuminate\View\ComponentAttributeBag($pageBlock::mutateData($blockData['data']))"
            />
        @endisset
    @endforeach
@endif
