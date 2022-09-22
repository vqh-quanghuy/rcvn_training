<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\UserAuthController;
use App\Http\Controllers\Api\Auth\CustomerAuthController;
use App\Http\Controllers\API\Users\UserController;
use App\Http\Controllers\API\Users\CustomerController;
use App\Http\Controllers\API\Users\ProductController;

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
        Route::post('login', 'login');
    });

    Route::middleware(['auth:users'])->group(function () {
        // Users Management
        Route::resource('/users', UserController::class)->except(['create', 'edit', 'show']);
        Route::put('/users/deactivate/{id}', [UserController::class, 'deactivate']);
        Route::get('/users/{user}', [UserController::class, 'detail']);

        // Customers Management
        Route::resource('/customers', CustomerController::class)->except(['create', 'edit', 'show', 'destroy']);
        Route::get('/customers/{customer}', [CustomerController::class, 'detail']);

        // Products Management
        Route::resource('/products', ProductController::class)->except(['create', 'edit', 'show']);
        Route::get('/products/{product}', [ProductController::class, 'detail']);


        Route::post('logout', [UserAuthController::class, 'logout']);
    });
});

// Only for Customer
Route::prefix('auth/customer')->controller(CustomerAuthController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
});

