<?php 

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\OrderController; 
use App\Http\Controllers\API\ProductController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;



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
Route::get('/categories/{category}', [CategoryController::class, 'show']);

// Cart Routes 
Route::middleware('auth:sanctum')->prefix('cart')->group(function() {

    Route::get('/', [CartController::class, 'index']);
    Route::get('/count', [CartController::class, 'count']);
    Route::post('/add', [CartController::class, 'add']);
    Route::put('/update/{productId}', [CartController::class, 'update']);
    Route::delete('/remove/{productId}', [CartController::class, 'remove']);
    Route::delete('/clear', [CartController::class, 'clear']);
    
});

// Orders Routes 
Route::middleware('auth:sanctum')->prefix('orders')->group(function() {

    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'store']);
    Route::get('/{orderNumber}', [OrderController::class, 'show']);
    Route::post('/{orderNumber}/cancel', [OrderController::class, 'cancel']);
    Route::post('/{orderNumber}/reorder', [OrderController::class, 'reorder']);
    
});

Route::get('track-order/{OrderNumber}', [OrderController::class, 'track']);


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

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/analytics', [DashboardController::class, 'analytics']);

    // Products 
    Route::prefix('products')->group(function () {

        Route::get('/', [AdminProductController::class, 'index']);
        Route::post('/', [AdminProductController::class, 'store']);
        Route::get('/{id}', [AdminProductController::class, 'show']);
        Route::put('/{id}', [AdminProductController::class, 'update']);
        Route::delete('/{id}', [AdminProductController::class, 'destroy']);
        Route::patch('/{id}/toggle-status', [AdminProductController::class, 'toggleStatus']);
        Route::post('/{id}/update-stock', [AdminProductController::class, 'updateStock']);

    });

    // Categories 
    Route::prefix('categories')->group(function () {

        Route::get('/', [AdminCategoryController::class, 'index']);
        Route::post('/', [AdminCategoryController::class, 'store']);
        Route::get('/{id}', [AdminCategoryController::class, 'show']);
        Route::put('/{id}', [AdminCategoryController::class, 'update']);
        Route::delete('/{id}', [AdminCategoryController::class, 'destroy']);

    });

    // Orders 
    Route::prefix('orders')->group(function () {

        Route::get('/', [AdminOrderController::class, 'index']);
        Route::get('/{id}', [AdminOrderController::class, 'show']);
        Route::put('/{id}/status', [AdminOrderController::class, 'updateStatus']);
        Route::put('/{id}/payment-status', [AdminOrderController::class, 'updatePaymentStatus']);
        Route::delete('/{id}', [AdminOrderController::class, 'destroy']);

    });

});