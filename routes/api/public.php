<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Pro\ProjectsController;

Route::prefix('professional')
    ->name('professional.')
    ->middleware([
        'auth:sanctum',
        // 'professional', // TODO
    ])
    ->group(function () {
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [ProjectsController::class, 'index'])->name('index');
            Route::get('/show/{projectId}', [ProjectsController::class, 'showOpenProject'])->name('show');
            Route::get('/released/{projectId}', [ProjectsController::class, 'showProfessionalProject'])
                ->name('released');
        });
    });
