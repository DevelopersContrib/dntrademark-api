<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Middleware\EnsureApiKeyIsValid;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\V1\UserController;

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
        });
    });
});
