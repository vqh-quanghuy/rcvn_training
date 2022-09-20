<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\UserAuthController;
use App\Http\Controllers\Api\Auth\CustomerAuthController;
use App\Http\Controllers\API\Users\UserController;

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

// Only for users

Route::prefix('user')->group(function () {
    Route::prefix('auth')->controller(UserAuthController::class)->group(function(){
        Route::post('register', 'register');
        Route::post('login', 'login');
        
    });

    Route::middleware(['auth:users'])->group(function () {
        Route::resource('/users', UserController::class)->except([
            'create'
        ]);
        Route::post('logout', [UserAuthController::class, 'logout']);
    });
});

// Only for Customer
Route::prefix('auth/customer')->controller(CustomerAuthController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
});

