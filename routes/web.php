<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// ─── Auth ────────────────────────────────────────────────────────────────────
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');
// ─────────────────────────────────────────────────────────────────────────────

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('client');
});

Route::get('/vendor', function () {
    return view('vendor');
});

Route::get('/runner', function () {
    return view('runner');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/', function () { return view('admin.dashboard'); })->name('admin.dashboard');
    Route::get('/dashboard', function () { return view('admin.dashboard'); });
    Route::get('/orders', function () { return view('admin.orders'); })->name('admin.orders');
    Route::get('/users', function () { return view('admin.users'); })->name('admin.users');
    Route::get('/reports', function () { return view('admin.reports'); })->name('admin.reports');
    Route::get('/vendors', function () { return view('admin.vendors'); })->name('admin.vendors');
    Route::get('/heatmap', function () { return view('admin.heatmap'); })->name('admin.heatmap');
});

Route::get('/simulator', function () {
    return view('dashboard');
});

