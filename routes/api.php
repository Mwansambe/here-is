<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\PublicCatalogController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\BannerController;

Route::prefix('auth')->group(function(){
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);
    Route::post('/forgot-password/send-code',[AuthController::class,'forgotPasswordSendCode']);
    Route::post('/reset-password',[AuthController::class,'resetPassword']);
    Route::middleware('auth:sanctum')->post('/logout',[AuthController::class,'logout']);
});

Route::get('/categories',[PublicCatalogController::class,'categories']);
Route::get('/products',[PublicCatalogController::class,'products']);
Route::get('/banners',[PublicCatalogController::class,'banners']);
Route::post('/contact',[ContactController::class,'store']);

Route::middleware(['auth:sanctum','admin'])->prefix('admin')->group(function(){
    Route::apiResource('categories',CategoryController::class)->except('show');
    Route::apiResource('products',ProductController::class)->except('show');
    Route::get('/banners',[BannerController::class,'index']);
    Route::post('/banners',[BannerController::class,'store']);
    Route::delete('/banners/{banner}',[BannerController::class,'destroy']);
});