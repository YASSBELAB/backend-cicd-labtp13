
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Api\AuthController;

// =====================================
// API KEY PROTECTED PRODUCT READ ROUTES
// =====================================
Route::apiResource('products', ProductController::class)
    ->only(['index', 'show'])
    ->middleware('api.key');

// =====================================
// PROTECTED PRODUCT ROUTES - ADMIN ONLY
// =====================================
Route::middleware([
    'auth:sanctum',
    'can:create,App\Models\Product'
])->post('/products', [ProductController::class, 'store']);

Route::middleware([
    'auth:sanctum',
    'can:update,product'
])->put('/products/{product}', [ProductController::class, 'update']);

Route::middleware([
    'auth:sanctum',
    'can:update,product'
])->patch('/products/{product}', [ProductController::class, 'update']);

Route::middleware([
    'auth:sanctum',
    'can:delete,product'
])->delete('/products/{product}', [ProductController::class, 'destroy']);

// =====================================
// API KEY PROTECTED CATEGORY AND CLIENT ROUTES
// =====================================
Route::apiResource('categories', CategoryController::class)
    ->middleware('api.key');
Route::apiResource('clients', ClientController::class)
    ->middleware('api.key');

// =====================================
// PROTECTED ORDER ROUTES
// =====================================
Route::middleware('auth:sanctum')->group(function () {

    // USER and ADMIN can create orders
    Route::post('/orders', [OrderController::class, 'store'])
        ->middleware('can:create,App\Models\Order');

    // Authenticated users can list orders (filtered by role in controller)
    Route::get('/orders', [OrderController::class, 'index'])
        ->middleware('can:viewAny,App\Models\Order');

    // ADMIN or order owner can view an order
    Route::get('/orders/{order}', [OrderController::class, 'show'])
        ->middleware('can:view,order');

    // ADMIN only can update an order
    Route::put('/orders/{order}', [OrderController::class, 'update'])
        ->middleware('can:update,order');

    // ADMIN only can partially update an order
    Route::patch('/orders/{order}', [OrderController::class, 'update'])
        ->middleware('can:update,order');

    // ADMIN only can delete an order
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])
        ->middleware('can:delete,order');
});

// =====================================
// PUBLIC AUTHENTICATION ROUTES (Rate-limited)
// =====================================
Route::post('/auth/register', [AuthController::class, 'register'])
    ->middleware('throttle:10,1');
Route::post('/auth/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1');

// =====================================
// PROTECTED AUTHENTICATION ROUTES
// =====================================
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::post('/auth/logout', [AuthController::class, 'logout']);
});
