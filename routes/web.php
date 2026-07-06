<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\StationMasterRequestController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StationMasterController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/station-master/pending', function () {
    return view('auth.station-master-pending');
})->name('station-master.pending');

/*
|--------------------------------------------------------------------------
| BREEZE AUTH ROUTES
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| CUSTOM ROLE LOGIN PAGES
|--------------------------------------------------------------------------
*/

Route::get('/station-master/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('station-master.login');

Route::get('/admin/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('admin.login');

/*
|--------------------------------------------------------------------------
| PASSENGER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| STATION MASTER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'station_master'])->prefix('station-master')->group(function () {
    Route::get('/', [StationMasterController::class, 'dashboard'])
        ->name('station-master.dashboard');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (Privileged)
| Guest → redirect to /admin/login
| Logged-in non-admin → 404
|--------------------------------------------------------------------------
*/
Route::middleware(['admin'])->prefix('admin')->group(function () {
    
    // Admin Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])
        ->name('admin.dashboard');

    // Station Master Request Management
    Route::get('/station-master-requests', [StationMasterRequestController::class, 'index'])
        ->name('admin.station-master-requests.index');
    
    Route::patch('/station-master-requests/{stationMasterRequest}/approve', [StationMasterRequestController::class, 'approve'])
        ->name('admin.station-master-requests.approve');
    
    Route::patch('/station-master-requests/{stationMasterRequest}/reject', [StationMasterRequestController::class, 'reject'])
        ->name('admin.station-master-requests.reject');
});

/*
|--------------------------------------------------------------------------
| PROFILE ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});