<?php

use Illuminate\Support\Facades\Route;
use Phpsa\FilamentHeadlessCms\Http\Controller\Api\PagesController;
use Phpsa\FilamentHeadlessCms\Http\Controller\Api\TypesController;

try {
    $plugin = \Phpsa\FilamentHeadlessCms\FilamentHeadlessCms::getPlugin();

    Route::middleware(
        $plugin->getApiMiddleware()
    )->prefix(
        $plugin->getApiPrefix()
    )->as('fhcms.')->group(function () {
        Route::get('fhcms/types', TypesController::class)->name('types');

        Route::get('fhcms/content-pages/{type}', [PagesController::class, 'index'])->name('content-pages.index');

        Route::get('fhcms/content-pages/{type}/{slug}', [PagesController::class, 'show'])->name('content-pages.show');
    });
} catch (Throwable $throwable) {
    //ignore the errors untill it is enabled.
}
