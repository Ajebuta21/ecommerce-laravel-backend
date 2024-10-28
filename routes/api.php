<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/me', [AuthController::class, 'me']);

    //user
    Route::post('/update-account', [UserController::class, 'updateNameEmail'])->middleware('auth:api');
    Route::post('/update-contact', [UserController::class, 'updatePhoneAddress'])->middleware('auth:api');
    Route::post('/update-password', [UserController::class, 'updatePassword'])->middleware('auth:api');
    Route::get('/all-users', [UserController::class, 'index'])->middleware('auth:api');
    Route::get('/user/{id}', [UserController::class, 'show'])->middleware('auth:api');
    Route::post('/user-role/{id}', [UserController::class, 'changeRole'])->middleware('auth:api');

    //category
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/category-store', [CategoryController::class, 'store'])->middleware('auth:api');
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::post('/category-update/{id}', [CategoryController::class, 'update'])->middleware('auth:api');
    Route::delete('/category-destroy/{id}', [CategoryController::class, 'destroy'])->middleware('auth:api');

    //product
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/product-store', [ProductController::class, 'store'])->middleware('auth:api');
    Route::get('/products/{slug}', [ProductController::class, 'show']);
    Route::post('/product-update/{id}', [ProductController::class, 'update'])->middleware('auth:api');
    Route::delete('/product-destroy/{id}', [ProductController::class, 'destroy'])->middleware('auth:api');

    //region
    Route::get('/regions', [RegionController::class, 'index']);
    Route::post('/region-store', [RegionController::class, 'store'])->middleware('auth:api');
    Route::get('/region/{id}', [RegionController::class, 'show']);
    Route::post('/region-update/{id}', [RegionController::class, 'update'])->middleware('auth:api');
    Route::delete('/region-destroy/{id}', [RegionController::class, 'destroy'])->middleware('auth:api');

    //order
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/order-store', [OrderController::class, 'store'])->middleware('auth:api');
    Route::get('/order/{order_number}', [OrderController::class, 'show']);
    Route::post('/order-status-update/{id}', [OrderController::class, 'updateStatus'])->middleware('auth:api');
    Route::get('/user-order/{id}', [OrderController::class, 'userOrders'])->middleware('auth:api');
    Route::delete('/order-destroy/{id}', [OrderController::class, 'destroy'])->middleware('auth:api');
    Route::post('/check-cart', [OrderController::class, 'checkCart'])->middleware('auth:api');

    //payment
    Route::post('/paystack/initialize', [PaymentController::class, 'initializeTransaction'])->middleware('auth:api');

    //rating
    Route::post('/products/{productId}/rate', [RatingController::class, 'storeRating'])->middleware('auth:api');
    Route::get('/products/{productId}/rating', [RatingController::class, 'checkUserRating'])->middleware('auth:api');

    //Analytics
    Route::get('/get-analytics', [AnalyticsController::class, 'getAnalytics'])->middleware('auth:api');
    Route::get('/get-monthly-subtotal', [AnalyticsController::class, 'getMonthlySubtotals'])->middleware('auth:api');
    Route::get('/get-top-products', [AnalyticsController::class, 'getTopProducts'])->middleware('auth:api');

    //favourite
    Route::post('/favourites/toggle/{productId}', [FavouriteController::class, 'toggleFavourite'])->middleware('auth:api');
    Route::get('/favourites', [FavouriteController::class, 'viewFavourites'])->middleware('auth:api');
    Route::get('/favourites/check/{productId}', [FavouriteController::class, 'isFavourite'])->middleware('auth:api');
});
