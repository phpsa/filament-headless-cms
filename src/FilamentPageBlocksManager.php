<?php

namespace Phpsa\FilamentHeadlessCms;

use Exception;
use InvalidArgumentException;
use Illuminate\Support\Collection;
use Phpsa\FilamentHeadlessCms\Contracts\PageBlock;
use Phpsa\FilamentHeadlessCms\Exceptions\InvalidClassTypeException;

class FilamentPageBlocksManager
{
    /** @var Collection<string,string> */
    protected Collection $pageBlocks;

    public function __construct()
    {
        /** @var Collection<string,string> $pageBlocks */
        $pageBlocks = collect([]);

        $this->pageBlocks = $pageBlocks;
    }

    /**
     * @param  class-string  $class
     * @param  class-string  $baseClass
     *
     * @throws Exception
     */
    public function register(string $class, string $baseClass): void
    {
        match ($baseClass) {
            PageBlock::class => static::registerPageBlock($class),
            default => throw new InvalidClassTypeException('Invalid class type: ' . $baseClass),
        };
    }

    /** @param  class-string  $pageBlock */
    public function registerPageBlock(string $pageBlock): void
    {
        if (! is_subclass_of($pageBlock, PageBlock::class)) {
            throw new InvalidArgumentException("{$pageBlock} must extend " . PageBlock::class);
        }

        $this->pageBlocks->put($pageBlock::getName(), $pageBlock);
    }

    public function getPageBlockFromName(string $name): ?string
    {
        return $this->pageBlocks->get($name);
    }

    public function getPageBlocks(): array
    {
        return $this->pageBlocks->map(fn ($block) => $block::getBlockSchema())->toArray();
    }

    public function getPageBlocksRaw(): array
    {
        return $this->pageBlocks->toArray();
    }
}
