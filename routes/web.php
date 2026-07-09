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

Route::get('/admin', function () {
    return view('admin');
})->middleware(['auth', 'role:admin']);

Route::get('/simulator', function () {
    return view('dashboard');
});

