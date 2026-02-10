<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CategoryController;


// Authentication Routes 
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/user', [AuthController::class, 'user']);
    });
});


// Products
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/products/slug/{slug}', [ProductController::class, 'showBySlug']);


// Categories
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{categories}', [CategoryController::class, 'show']);


// Protected For Users Only 

Route::middleware('auth:sanctum')->group(function () {

    Route::get('protected-test', function () {
        return response()->json([
            'message' => 'This Page Is Protected You Are Logged In',
            'user' => auth()->user()
        ]);
    });

});


// Protected For Admin Only 

Route::middleware('auth:sanctum', 'admin')->group(function () {

    Route::get('admin-test', function () {
        return response()->json([
            'message' => 'This Page Is Protected You Are Logged In',
            'user' => auth()->user()
        ]);
    });

});