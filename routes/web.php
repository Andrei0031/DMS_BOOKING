<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Bookings routes
    Route::prefix('/dashboard')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('/book', [BookingController::class, 'create'])->name('bookings.create');
        Route::post('/book', [BookingController::class, 'store'])->name('bookings.store');
        Route::delete('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    });
});

// Admin routes
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Bookings Management
    Route::prefix('/bookings')->name('bookings.')->group(function () {
        Route::get('/', [AdminController::class, 'bookings'])->name('index');
        Route::get('/{booking}/details', [AdminController::class, 'bookingDetails'])->name('details');
        Route::post('/{booking}/status', [AdminController::class, 'updateBookingStatus'])->name('update-status');
        Route::delete('/{booking}', [AdminController::class, 'deleteBooking'])->name('delete');
    });
    
    // Buses Management
    Route::prefix('/buses')->name('buses.')->group(function () {
        Route::get('/', [AdminController::class, 'buses'])->name('index');
        Route::get('/create', [AdminController::class, 'createBus'])->name('create');
        Route::post('/', [AdminController::class, 'storeBus'])->name('store');
        Route::get('/{bus}/edit', [AdminController::class, 'editBus'])->name('edit');
        Route::post('/{bus}', [AdminController::class, 'updateBus'])->name('update');
        Route::delete('/{bus}', [AdminController::class, 'deleteBus'])->name('delete');
    });
    
    // Users Management
    Route::prefix('/users')->name('users.')->group(function () {
        Route::get('/', [AdminController::class, 'users'])->name('index');
        Route::get('/{user}/details', [AdminController::class, 'userDetails'])->name('details');
        Route::delete('/{user}', [AdminController::class, 'deleteUser'])->name('delete');
        Route::post('/{user}/make-admin', [AdminController::class, 'makeAdmin'])->name('make-admin');
        Route::post('/{user}/remove-admin', [AdminController::class, 'removeAdmin'])->name('remove-admin');
    });
    
    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
