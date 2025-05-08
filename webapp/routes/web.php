<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\SettingController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

// 管理者用ルート
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['guest:admin'])->group(function () {
        Route::get('login', [AuthenticatedSessionController::class, 'create'])
            ->name('login');
        Route::post('login', [AuthenticatedSessionController::class, 'store']);
    });

    Route::middleware(['auth:admin', config('jetstream.auth_session'), 'verified'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

        // プロフィール関連
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // パスワード関連
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');

        // 2要素認証関連
        Route::post('/two-factor-authentication', [ProfileController::class, 'enableTwoFactorAuthentication'])
            ->name('two-factor.enable');
        Route::delete('/two-factor-authentication', [ProfileController::class, 'disableTwoFactorAuthentication'])
            ->name('two-factor.disable');
        Route::get('/two-factor-recovery-codes', [ProfileController::class, 'showRecoveryCodes'])
            ->name('two-factor.recovery-codes');

        // ブラウザセッション関連
        Route::delete('/other-browser-sessions', [ProfileController::class, 'logoutOtherBrowserSessions'])
            ->name('other-browser-sessions.destroy');

        // ユーザー管理
        Route::resource('users', UserController::class)->names([
            'index' => 'users.index',
            'create' => 'users.create',
            'store' => 'users.store',
            'edit' => 'users.edit',
            'update' => 'users.update',
            'destroy' => 'users.destroy',
        ]);

        // ログ管理
        Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
        Route::get('/logs/{filename}', [LogController::class, 'show'])->name('logs.show');
        Route::get('/logs/{filename}/download', [LogController::class, 'download'])->name('logs.download');
        Route::delete('/logs/{filename}', [LogController::class, 'destroy'])->name('logs.destroy');

        // 設定管理
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });
});
