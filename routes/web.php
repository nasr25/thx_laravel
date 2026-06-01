<?php

use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\EmployeeController;
use App\Http\Controllers\Web\AppreciationController;
use App\Http\Controllers\Web\Admin\SettingController;
use App\Http\Controllers\Web\Admin\ReasonController;
use App\Http\Controllers\Web\Admin\UserController;
use Illuminate\Support\Facades\Route;

// ── Language switch ──────────────────────────────────────────────────────────
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'ar'], true)) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

// ── Guest (login) ────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:10,1');
});

// Windows auto-login entry (IIS sets LOGON_USER; Anonymous disabled on this path).
Route::get('/auth/windows', [LoginController::class, 'windows'])
    ->middleware('throttle:30,1')
    ->name('auth.windows');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ── Authenticated app ────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/employees',         [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/{user}',  [EmployeeController::class, 'show'])->name('employees.show');

    Route::post('/appreciations',    [AppreciationController::class, 'store'])->middleware('throttle:30,1')->name('appreciations.store');
    Route::get('/history',           [AppreciationController::class, 'history'])->name('history');

    // ── Admin ──────────────────────────────────────────────────────────────
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/settings',            [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings',            [SettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/logo',      [SettingController::class, 'uploadLogo'])->name('settings.logo');

        Route::get('/users',               [UserController::class, 'index'])->name('users.index');
        Route::put('/users/{user}/role',   [UserController::class, 'updateRole'])->name('users.role');
        Route::put('/users/{user}/status', [UserController::class, 'updateStatus'])->name('users.status');

        // Appreciation reasons — super-admin only.
        Route::middleware('super-admin')->group(function () {
            Route::get('/reasons',             [ReasonController::class, 'index'])->name('reasons.index');
            Route::post('/reasons',            [ReasonController::class, 'store'])->name('reasons.store');
            Route::put('/reasons/{reason}',    [ReasonController::class, 'update'])->name('reasons.update');
            Route::delete('/reasons/{reason}', [ReasonController::class, 'destroy'])->name('reasons.destroy');
        });
    });
});
