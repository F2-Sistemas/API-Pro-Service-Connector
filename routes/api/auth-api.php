<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AccountRegisterController;
use App\Http\Controllers\Api\AccountLoginController;

/**
 * api.auth.*
 * All routes name here begins with 'api.auth.'
 * Example: 'api.auth.account.register'
 */
Route::prefix('account')->name('account.')->group(function () {
    Route::post('login', [AccountLoginController::class, 'login'])->name('login');
    Route::post('login/get_verification_token', [AccountLoginController::class, 'getVerificationToken'])->name('get_login_verification_token');

    Route::prefix('register')->name('register.')
        ->middleware([
            'guest',
        ])
        ->group(function () {
            Route::post('/', [AccountRegisterController::class, 'registerStepOne'])->name('register');
            Route::post('/step_one', [AccountRegisterController::class, 'registerStepOne'])->name('step_one');
            Route::post('/step_two', [AccountRegisterController::class, 'registerStepTwo'])->name('step_two');
            Route::post('/step_three', [AccountRegisterController::class, 'registerStepThree'])->name('step_three');
            Route::post('/get_verification_token', [AccountRegisterController::class, 'registerStepOne'])
                ->name('get_register_verification_token');
        });
});
