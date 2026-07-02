<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\PublicCatalogController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\BannerController;

// Public
Route::get('/categories', [PublicCatalogController::class, 'categories']);
Route::get('/products', [PublicCatalogController::class, 'products']);
Route::get('/banners', [PublicCatalogController::class, 'banners']);
Route::post('/contact', [ContactController::class, 'store']);

// Auth
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password/send-code', [AuthController::class, 'forgotPasswordSendCode']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
});

// Authenticated user
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserProfileController::class, 'show']);
    Route::put('/user', [UserProfileController::class, 'update']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
});

// Admin
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::apiResource('categories', CategoryController::class)->except('show');
    Route::apiResource('products', ProductController::class)->except('show');
    Route::get('/banners', [BannerController::class, 'index']);
    Route::post('/banners', [BannerController::class, 'store']);
    Route::delete('/banners/{banner}', [BannerController::class, 'destroy']);

    // Admin order management
    Route::get('/orders', [OrderController::class, 'index']);
});
