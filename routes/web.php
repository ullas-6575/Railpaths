<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\StationMasterRequestController;
use App\Http\Controllers\Admin\TrainController;
use App\Http\Controllers\Admin\RouteController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StationMasterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\StationLogController;
use App\Http\Controllers\PassengerDashboardController;
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
    Route::get('/dashboard', [PassengerDashboardController::class, 'index'])->name('dashboard');
    Route::post('/notifications/{notification}/read', [PassengerDashboardController::class, 'markNotificationRead'])
        ->name('notifications.read');
});

/*
|--------------------------------------------------------------------------
| STATION MASTER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'station_master'])->prefix('station-master')->group(function () {
    Route::get('/', [StationMasterController::class, 'dashboard'])
        ->name('station-master.dashboard');
    Route::post('/log-train/{schedule}', [StationMasterController::class, 'logTrain'])
        ->name('station-master.log-train');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (Privileged)
| Guest → redirect to /admin/login
| Logged-in non-admin → 404
|--------------------------------------------------------------------------
*/
Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {

    // Admin Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])
        ->name('dashboard');

    // Station Master Request Management
    Route::get('/station-master-requests', [StationMasterRequestController::class, 'index'])
        ->name('station-master-requests');

    Route::patch('/station-master-requests/{stationMasterRequest}/approve', [StationMasterRequestController::class, 'approve'])
        ->name('station-master-requests.approve');

    Route::patch('/station-master-requests/{stationMasterRequest}/reject', [StationMasterRequestController::class, 'reject'])
        ->name('station-master-requests.reject');

    // Train Management
    Route::get('/trains', [TrainController::class, 'index'])
        ->name('trains.index');
    Route::get('/trains/create', [TrainController::class, 'create'])
        ->name('trains.create');
    Route::post('/trains', [TrainController::class, 'store'])
        ->name('trains.store');
    Route::get('/trains/{train}/edit', [TrainController::class, 'edit'])
        ->name('trains.edit');
    Route::put('/trains/{train}', [TrainController::class, 'update'])
        ->name('trains.update');
    Route::delete('/trains/{train}', [TrainController::class, 'destroy'])
        ->name('trains.destroy');

    // Route Builder (Create)
    Route::get('/trains/{train}/route-builder', [RouteController::class, 'create'])
        ->name('trains.routes.create');
    Route::post('/trains/{train}/route-builder', [RouteController::class, 'store'])
        ->name('trains.routes.store');

    // Route Show (View)
    Route::get('/trains/{train}/route', [RouteController::class, 'show'])
        ->name('trains.routes.show');
    Route::get('/trains/{train}/route-alias', [RouteController::class, 'show'])
        ->name('trains.routes');  // ← ALIAS for backward compatibility

    // Route Edit (Drag-Drop Reordering)
    Route::get('/trains/{train}/route-edit', [RouteController::class, 'edit'])
        ->name('trains.routes.edit');
    Route::put('/trains/{train}/route-edit', [RouteController::class, 'update'])
        ->name('trains.routes.update');
    Route::delete('/routes/{route}', [RouteController::class, 'destroy'])
        ->name('routes.destroy');

    // Train Schedule Calendar
    Route::get('/schedule', [ScheduleController::class, 'index'])
        ->name('schedule.index');
    Route::get('/schedule/api/{train}', [ScheduleController::class, 'apiSchedule'])
        ->name('schedule.api');

    // Station Logs
    Route::get('/station-logs', [StationLogController::class, 'index'])
        ->name('station-logs.index');
    Route::get('/station-logs/statistics', [StationLogController::class, 'statistics'])
        ->name('station-logs.statistics');
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

Route::middleware(['auth'])->group(function () {
    Route::post('/booking/search', [BookingController::class, 'search'])->name('booking.search');
    Route::get('/booking/seats/{train}', [BookingController::class, 'showSeats'])->name('booking.seats');
    Route::post('/booking/book', [BookingController::class, 'book'])->name('booking.book');
    Route::get('/booking/confirmation/{booking}', [BookingController::class, 'confirmation'])->name('booking.confirmation');
});
