<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Pro\ProjectsController;
use App\Http\Controllers\Api\Pro\WalletsController;

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

            // Route::get('/release/{projectId}', [ProjectsController::class, 'releaseProject'])
            //     ->name('release');

            Route::get('/release/{projectId}/{coinPrice?}', [ProjectsController::class, 'projectRelease'])
                ->name('release');
        });

        Route::prefix('wallets')->name('wallets.')->group(function () {
            Route::get('/', [WalletsController::class, 'index'])->name('index');
            Route::get('/show/{walletUuid}', [WalletsController::class, 'show'])->name('show');
        });
        Route::prefix('category')->name('category.')->group(function () {
            Route::get('/', [ProjectsController::class, 'categoryIndex'])->name('index');
        });
    });
