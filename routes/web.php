<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\PushController;
use Illuminate\Support\Facades\Route;

// Authentication routes
require __DIR__ . '/auth.php';

// Redirect root to dashboard for authenticated users
Route::get('/', function () {
    return auth()->check() 
        ? redirect()->route('dashboard') 
        : redirect()->route('login');
});

// Dashboard
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Profile routes
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'edit'])->name('edit');
    Route::patch('/', [ProfileController::class, 'update'])->name('update');
    Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
});

// Task routes (web interface)
Route::middleware('auth')->prefix('tasks')->name('tasks.')->group(function () {
    Route::get('/', [TaskController::class, 'index'])->name('index');
    Route::post('/', [TaskController::class, 'store'])->name('store');
    Route::patch('/{task}', [TaskController::class, 'update'])->name('update');
    Route::patch('/{task}/toggle', [TaskController::class, 'toggle'])->name('toggle');
    Route::post('/{task}/reschedule', [TaskController::class, 'reschedule'])->name('reschedule');
    Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
});

// Calendar routes
Route::middleware('auth')->prefix('calendar')->name('calendar.')->group(function () {
    Route::get('/', [CalendarController::class, 'index'])->name('index');
});

// Analytics routes
Route::middleware('auth')->prefix('analytics')->name('analytics.')->group(function () {
    Route::get('/', [AnalyticsController::class, 'index'])->name('index');
});

// Settings routes
Route::middleware('auth')->prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('index');
    Route::patch('/notifications', [SettingsController::class, 'updateNotifications'])->name('notifications.update');
});

// Telegram routes
Route::prefix('telegram')->name('telegram.')->group(function () {
    Route::post('/webhook', [TelegramController::class, 'webhook'])->name('webhook');
    Route::middleware('auth')->group(function () {
        Route::post('/setup', [TelegramController::class, 'setup'])->name('setup');
        Route::post('/disconnect', [TelegramController::class, 'disconnect'])->name('disconnect');
    });
});

// Push notification routes
Route::middleware('auth')->prefix('push')->name('push.')->group(function () {
    Route::post('/subscribe', [PushController::class, 'subscribe'])->name('subscribe');
    Route::post('/unsubscribe', [PushController::class, 'unsubscribe'])->name('unsubscribe');
    Route::get('/vapid-public-key', [PushController::class, 'vapidPublicKey'])->name('vapid-public-key');
});