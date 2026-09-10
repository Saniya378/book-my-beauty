<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Beautician;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\Customer;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/browse', [Customer\BrowseController::class, 'index'])->name('browse');
Route::get('/beautician/{id}', [Customer\BeauticianProfileController::class, 'show'])->name('beautician.view')->where('id', '[0-9]+');

// Chatbot & Notifications AJAX API
Route::post('/chatbot/message', [ChatbotController::class, 'sendMessage'])->name('chatbot.message');
Route::get('/notifications', [NotificationController::class, 'getNotifications'])->name('notifications.get');
Route::post('/notifications/mark-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark_read');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// OTP Phone Verification (Step 2 of registration)
Route::get('/verify-otp', [AuthController::class, 'showOtpVerification'])->name('register.otp');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('register.otp.verify');
Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('register.otp.resend');

Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dedicated Admin Login (unlisted from public nav)
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.submit');

/*
|--------------------------------------------------------------------------
| Customer Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [Customer\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/book/{beautician_id}', [Customer\BookingController::class, 'create'])->name('book');
    Route::post('/book/{beautician_id}', [Customer\BookingController::class, 'store'])->name('book.store');

    Route::get('/bookings', [Customer\BookingController::class, 'index'])->name('bookings');
    Route::post('/bookings/{id}/cancel', [Customer\BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::get('/bookings/{id}/receipt', [Customer\BookingController::class, 'downloadReceipt'])->name('bookings.receipt');

    Route::get('/favorites', [Customer\FavoriteController::class, 'index'])->name('favorites');
    Route::post('/favorites/toggle', [Customer\FavoriteController::class, 'toggle'])->name('favorites.toggle');

    Route::get('/review/{beautician_id}', [Customer\ReviewController::class, 'show'])->name('review');
    Route::post('/review/{beautician_id}', [Customer\ReviewController::class, 'store'])->name('review.store');
});

/*
|--------------------------------------------------------------------------
| Beautician Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:beautician'])->prefix('beautician')->name('beautician.')->group(function () {
    Route::get('/dashboard', [Beautician\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/bookings', [Beautician\BookingController::class, 'index'])->name('bookings');
    Route::post('/bookings/{id}/status', [Beautician\BookingController::class, 'updateStatus'])->name('bookings.status');

    Route::get('/services', [Beautician\ServiceController::class, 'index'])->name('services');
    Route::post('/services', [Beautician\ServiceController::class, 'store'])->name('services.store');
    Route::post('/services/{id}/toggle', [Beautician\ServiceController::class, 'toggleStatus'])->name('services.toggle');
    Route::delete('/services/{id}', [Beautician\ServiceController::class, 'destroy'])->name('services.destroy');

    Route::get('/availability', [Beautician\AvailabilityController::class, 'index'])->name('availability');
    Route::post('/availability', [Beautician\AvailabilityController::class, 'store'])->name('availability.store');
    Route::delete('/availability/{id}', [Beautician\AvailabilityController::class, 'destroy'])->name('availability.destroy');

    Route::get('/portfolio', [Beautician\PortfolioController::class, 'index'])->name('portfolio');
    Route::post('/portfolio', [Beautician\PortfolioController::class, 'store'])->name('portfolio.store');
    Route::delete('/portfolio/{id}', [Beautician\PortfolioController::class, 'destroy'])->name('portfolio.destroy');

    Route::get('/profile', [Beautician\ProfileController::class, 'edit'])->name('profile');
    Route::post('/profile', [Beautician\ProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Admin Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [Admin\UserController::class, 'index'])->name('users');
    Route::post('/users/{id}/toggle', [Admin\UserController::class, 'toggleStatus'])->name('users.toggle');

    Route::get('/bookings', [Admin\BookingController::class, 'index'])->name('bookings');
    Route::post('/bookings/{id}/status', [Admin\BookingController::class, 'updateStatus'])->name('bookings.status');

    Route::get('/analytics', [Admin\AnalyticsController::class, 'index'])->name('analytics');
});
