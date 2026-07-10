<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\RunnerController;
use App\Http\Controllers\API\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/verify', [AuthController::class, 'verify']);
Route::post('/auth/login-as', [AuthController::class, 'loginAs']);

// Events & Vendors
Route::get('/events/active', [EventController::class, 'index']);
Route::get('/vendors', [EventController::class, 'vendors']);
Route::post('/products/{id}/toggle-stock', [EventController::class, 'toggleProductStock']);
Route::post('/products', [EventController::class, 'storeProduct']);
Route::put('/products/{id}', [EventController::class, 'updateProduct']);
Route::delete('/products/{id}', [EventController::class, 'destroyProduct']);

// Orders (Customer & Vendor operations)
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/active', [OrderController::class, 'active']);
Route::get('/orders/vendor', [OrderController::class, 'vendorOrders']);
Route::get('/orders/{id}', [OrderController::class, 'show']);
Route::post('/orders/{id}/pay', [OrderController::class, 'pay']);
Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus']);

// Runner Operations
Route::get('/runner/deliveries', [RunnerController::class, 'index']);
Route::post('/runner/deliveries/{id}/status', [RunnerController::class, 'updateStatus']);
Route::post('/runner/deliveries/{id}/location', [RunnerController::class, 'updateLocation']);
Route::post('/runner/deliveries/{id}/verify', [RunnerController::class, 'verifyDelivery']);

// Admin Operations
Route::get('/admin/stats', [AdminController::class, 'stats']);
Route::get('/admin/orders', [AdminController::class, 'orders']);
Route::get('/admin/users', [AdminController::class, 'users']);
Route::post('/admin/users', [AdminController::class, 'createUser']);
Route::get('/admin/reports', [AdminController::class, 'reports']);
