<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubscriptionController;

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (Guest only)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.post');

    // Register
    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.post');

    // Forgot password (placeholder — bisa dihubungkan ke Laravel built-in)
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    // Root redirect to dashboard
    Route::get('/', fn() => redirect()->route('dashboard'));

    // Dashboard utama
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |------------------------------------------------------------------
    | STORAGE BUCKET
    |------------------------------------------------------------------
    */
    Route::prefix('storage')->name('storage.')->group(function () {
        Route::get('/',              [\App\Http\Controllers\StorageController::class, 'index'])->name('index');
        Route::get('/create',        [\App\Http\Controllers\StorageController::class, 'create'])->name('create');
        Route::post('/',             [\App\Http\Controllers\StorageController::class, 'store'])->name('store');
        Route::get('/{bucket}',      [\App\Http\Controllers\StorageController::class, 'show'])->name('show');
        Route::delete('/{bucket}',   [\App\Http\Controllers\StorageController::class, 'destroy'])->name('destroy');

        Route::post('/{bucket}/upload',          [\App\Http\Controllers\StorageController::class, 'upload'])->name('upload');
        Route::delete('/{bucket}/objects',       [\App\Http\Controllers\StorageController::class, 'deleteObject'])->name('delete-object');
    });

    /*
    |------------------------------------------------------------------
    | COMPUTE
    |------------------------------------------------------------------
    */
    Route::prefix('compute')->name('compute.')->group(function () {
        Route::get('/',             [\App\Http\Controllers\ComputeController::class, 'index'])->name('index');
        Route::get('/create',       [\App\Http\Controllers\ComputeController::class, 'create'])->name('create');
        Route::post('/',            [\App\Http\Controllers\ComputeController::class, 'store'])->name('store');
        Route::delete('/{instance}', [\App\Http\Controllers\ComputeController::class, 'destroy'])->name('destroy');
    });

    /*
    |------------------------------------------------------------------
    | ACCESS KEYS / CREDENTIALS
    |------------------------------------------------------------------
    */
    Route::prefix('credentials')->name('credentials.')->group(function () {
        Route::get('/',             [\App\Http\Controllers\CredentialController::class, 'index'])->name('index');
        Route::get('/create',       [\App\Http\Controllers\CredentialController::class, 'create'])->name('create');
        Route::post('/',            [\App\Http\Controllers\CredentialController::class, 'store'])->name('store');
        Route::get('/{cred}/reveal', [\App\Http\Controllers\CredentialController::class, 'reveal'])->name('reveal');
        Route::delete('/{cred}',    [\App\Http\Controllers\CredentialController::class, 'destroy'])->name('destroy');
    });

    /*
    |------------------------------------------------------------------
    | SUBSCRIPTIONS
    |------------------------------------------------------------------
    */
    Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
        Route::get('/',              [SubscriptionController::class, 'index'])->name('index');
        Route::get('/checkout/{plan}', [SubscriptionController::class, 'checkout'])->name('checkout');
        Route::post('/',             [SubscriptionController::class, 'store'])->name('store');
        Route::get('/cancel',        [SubscriptionController::class, 'cancel'])->name('cancel');
    });

    /*
    |------------------------------------------------------------------
    | ACTIVITY LOG
    |------------------------------------------------------------------
    */
    Route::prefix('activity')->name('activity.')->group(function () {
        Route::get('/',             [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('index');
    });

    /*
    |------------------------------------------------------------------
    | BILLING
    |------------------------------------------------------------------
    */
    Route::prefix('billing')->name('billing.')->group(function () {
        Route::get('/',             [\App\Http\Controllers\BillingController::class, 'index'])->name('index');
    });

    /*
    |------------------------------------------------------------------
    | PROFILE & SETTINGS
    |------------------------------------------------------------------
    */
    Route::get('/profile',          [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile',          [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    Route::get('/settings',         [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings',         [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
});
