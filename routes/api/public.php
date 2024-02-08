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
        });

        Route::prefix('wallets')->name('wallets.')->group(function () {
            Route::get('/', [WalletsController::class, 'index'])->name('index');
            Route::get('/show/{walletUuid}', [WalletsController::class, 'show'])->name('show');
        });
    });
