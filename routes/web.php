<?php

use App\Enums\UserRole;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StationMasterController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/admin', function () {
    if (auth()->check() && auth()->user()->role === UserRole::ADMIN) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('admin.login');
})->name('admin.root');

Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
});


Route::prefix('station-master')->name('station-master.')->middleware('station_master')->group(function () {
    Route::get('/dashboard', [StationMasterController::class, 'dashboard'])->name('dashboard');
});

require __DIR__.'/auth.php';
