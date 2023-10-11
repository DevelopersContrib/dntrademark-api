<?php

use App\Http\Controllers\Api\V1\PackageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Middleware\EnsureApiKeyIsValid;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\v1\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(EnsureApiKeyIsValid::class)->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        //Logout
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

        //V1
        Route::prefix('v1')->group(function () {
            //Users
            Route::prefix('users')->group(function () {
                Route::put('/{user}', [UserController::class, 'update']);
            });

            //Payment
            Route::prefix('payment')->group(function () {
                Route::put('create-charge', [PaymentController::class, 'update']);
                Route::put('checkout', [PaymentController::class, 'update']);
            });
        });
    });

    //Public api endpoints
    Route::prefix('v1')->group(function () {
        Route::prefix('packages')->group(function () {
            Route::get('/', [PackageController::class, 'index']);
            Route::get('/{package}', [PackageController::class, 'show']);
        });
    });
});
