<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountManagementController;
use App\Http\Controllers\ArticleCategoryController;
use App\Http\Controllers\ArticleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

/**
 * Halaman publik untuk mahasiswa (anonim)
 */
Route::get('/', function () {
    // Kalau belum login → tampilkan landing mahasiswa
    if (!Auth::check()) {
        return view('dashboard'); // resources/views/dashboard.blade.php
    }

    // Kalau sudah login → redirect sesuai role
    $accountType = Auth::user()->account_type;

    return match ($accountType) {
        'staff_bk' => redirect()->route('dashboard.staff'),
        'konselor' => redirect()->route('dashboard.konselor'),
        default    => redirect()->route('login'),
    };
})->name('landing.mahasiswa');


/**
 * Auth (guest only) — hanya login, tidak ada register mandiri
 */
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});


/**
 * Staff BK (harus login + role staff_bk)
 */
Route::middleware(['auth', 'staff_bk'])->group(function () {
    Route::get('/dashboard/staff', [AuthController::class, 'staffDashboard'])->name('dashboard.staff');

    // Manajemen akun (Staff BK & Konselor) oleh Staff BK
    Route::get('/accounts/create', [AccountManagementController::class, 'create'])->name('accounts.create');
    Route::post('/accounts', [AccountManagementController::class, 'store'])->name('accounts.store');
});


/**
 * Konselor (harus login + role konselor)
 */
Route::middleware(['auth', 'konselor'])->group(function () {
    Route::get('/dashboard/konselor', [AuthController::class, 'konselorDashboard'])->name('dashboard.konselor');
});


/**
 * Logout (butuh login)
 */
Route::middleware('auth')->post('/logout', [AuthController::class, 'logout'])->name('logout');


/**
 * KATEGORI ARTIKEL
 *
 * - Konselor: CRUD
 * - Staff BK: read (index + show)
 */

// CRUD kategori: hanya Konselor (HARUS ditulis duluan sebelum {category})
Route::middleware(['auth', 'konselor'])->group(function () {
    Route::get('/article-categories/create', [ArticleCategoryController::class, 'create'])
        ->name('article-categories.create');

    Route::post('/article-categories', [ArticleCategoryController::class, 'store'])
        ->name('article-categories.store');

    Route::get('/article-categories/{category}/edit', [ArticleCategoryController::class, 'edit'])
        ->name('article-categories.edit')
        ->whereNumber('category');

    Route::put('/article-categories/{category}', [ArticleCategoryController::class, 'update'])
        ->name('article-categories.update')
        ->whereNumber('category');

    Route::delete('/article-categories/{category}', [ArticleCategoryController::class, 'destroy'])
        ->name('article-categories.destroy')
        ->whereNumber('category');
});

// READ kategori: Staff BK & Konselor
Route::middleware('auth')->group(function () {
    Route::get('/article-categories', [ArticleCategoryController::class, 'index'])
        ->name('article-categories.index');

    Route::get('/article-categories/{category}', [ArticleCategoryController::class, 'show'])
        ->name('article-categories.show')
        ->whereNumber('category');
});


/**
 * ARTIKEL
 *
 * - Publik (mahasiswa anonim):
 *   - GET /articles
 *   - GET /articles/{slug}
 *
 * - Internal (staff & konselor):
 *   - /manage/articles/...
 *   - Verifikasi: hanya konselor
 */

// INDEX & SHOW untuk publik (mahasiswa anonim bisa akses)
Route::get('/articles', [ArticleController::class, 'publicIndex'])->name('articles.public');
Route::get('/articles/{article:slug}', [ArticleController::class, 'show'])->name('articles.show');

// CRUD artikel untuk staff & konselor
Route::middleware('auth')->group(function () {
    Route::get('/manage/articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/manage/articles/create', [ArticleController::class, 'create'])->name('articles.create');
    Route::post('/manage/articles', [ArticleController::class, 'store'])->name('articles.store');
    Route::get('/manage/articles/{article}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/manage/articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
    Route::delete('/manage/articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');
});

// Verifikasi khusus Konselor
Route::middleware(['auth', 'konselor'])->post(
    '/manage/articles/{article}/verify',
    [ArticleController::class, 'verify']
)->name('articles.verify');