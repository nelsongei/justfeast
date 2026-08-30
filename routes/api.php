<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\RunnerController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\MpesaCallbackController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ── Public: Product browsing (required before login) ─────────────────────────
Route::get('/events/active', [EventController::class, 'index']);
Route::get('/vendors',       [EventController::class, 'vendors']);

// ── Authentication (OTP & Vendor Registration) ──────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/login',           [AuthController::class, 'login'])
        ->middleware('throttle:otp');

    Route::post('/verify',          [AuthController::class, 'verify'])
        ->middleware('throttle:otp-verify');

    Route::post('/register-vendor', [AuthController::class, 'registerVendor']);
});

// ── Sanctum & Session-protected routes ────────────────────────────────────────
Route::middleware('auth:sanctum,web')->group(function () {

    // Customer — order management
    Route::get('/orders/active',                [OrderController::class, 'active']);
    Route::post('/orders',                      [OrderController::class, 'store']);
    Route::get('/orders/{order}',               [OrderController::class, 'show']);
    Route::post('/orders/{order}/pay',          [OrderController::class, 'pay']);
    Route::get('/orders/{order}/payment-status',[OrderController::class, 'checkPaymentStatus']);

    // ── Vendor ────────────────────────────────────────────────────────────────
    Route::middleware('role:vendor')->prefix('vendor')->group(function () {
        Route::get('/orders',                         [OrderController::class,  'vendorOrders']);
        Route::patch('/orders/{order}/status',        [OrderController::class,  'updateStatus']);

        Route::post('/products',                      [EventController::class,  'storeProduct']);
        Route::put('/products/{product}',             [EventController::class,  'updateProduct']);
        Route::delete('/products/{product}',          [EventController::class,  'destroyProduct']);
        Route::patch('/products/{product}/stock',     [EventController::class,  'toggleProductStock']);
    });

    // ── Runner ────────────────────────────────────────────────────────────────
    Route::middleware('role:runner')->prefix('runner')->group(function () {
        Route::get('/deliveries',                                [RunnerController::class, 'index']);
        Route::patch('/deliveries/{delivery}/status',            [RunnerController::class, 'updateStatus']);
        Route::patch('/deliveries/{delivery}/location',          [RunnerController::class, 'updateLocation'])
            ->middleware('throttle:location');
        Route::post('/deliveries/{delivery}/verify',             [RunnerController::class, 'verifyDelivery'])
            ->middleware('throttle:delivery-pin');
    });

    // ── Admin ─────────────────────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/stats',   [AdminController::class, 'stats']);
        Route::get('/orders',  [AdminController::class, 'orders']);
        Route::get('/users',   [AdminController::class, 'users']);
        Route::post('/users',  [AdminController::class, 'createUser']);
        Route::get('/reports', [AdminController::class, 'reports']);
        Route::get('/vendors', [AdminController::class, 'vendors']);
        Route::post('/vendors', [AdminController::class, 'storeVendor']);
        Route::post('/vendors/{vendor}/products', [AdminController::class, 'storeProduct']);
        Route::delete('/products/{product}', [AdminController::class, 'deleteProduct']);
        Route::get('/events',  [AdminController::class, 'events']);
        Route::patch('/vendors/{vendor}/status', [AdminController::class, 'updateVendorStatus']);
    });
});

// ── Safaricom M-Pesa Callback (no Sanctum — Safaricom POSTs directly) ────────
Route::post('/mpesa/callback', [MpesaCallbackController::class, 'handle'])
    ->middleware('throttle:webhooks');
