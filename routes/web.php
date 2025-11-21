<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountManagementController;
use App\Http\Controllers\ArticleCategoryController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\EmergencyContactController;
use App\Http\Controllers\AssessmentCategoryController;
use App\Http\Controllers\AssessmentQuestionController;
use App\Http\Controllers\AssessmentAnswerOptionController;
use App\Http\Controllers\AssessmentRiskRuleController;
use App\Http\Controllers\AssessmentSessionController;

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

/**
 * EMERGENCY CONTACT
 *
 * - Publik (mahasiswa anonim): /emergency-contacts
 * - Internal (staff & konselor): /manage/emergency-contacts/...
 */

// PUBLIC: mahasiswa & semua orang bisa lihat daftar, klik WA
Route::get('/emergency-contacts', [EmergencyContactController::class, 'publicIndex'])
    ->name('emergency-contacts.public');

// INTERNAL: CRUD untuk Staff BK & Konselor (auth)
Route::middleware('auth')->group(function () {
    Route::get('/manage/emergency-contacts', [EmergencyContactController::class, 'index'])
        ->name('emergency-contacts.index');

    Route::get('/manage/emergency-contacts/create', [EmergencyContactController::class, 'create'])
        ->name('emergency-contacts.create');

    Route::post('/manage/emergency-contacts', [EmergencyContactController::class, 'store'])
        ->name('emergency-contacts.store');

    Route::get('/manage/emergency-contacts/{emergency_contact}/edit', [EmergencyContactController::class, 'edit'])
        ->name('emergency-contacts.edit');

    Route::put('/manage/emergency-contacts/{emergency_contact}', [EmergencyContactController::class, 'update'])
        ->name('emergency-contacts.update');

    Route::delete('/manage/emergency-contacts/{emergency_contact}', [EmergencyContactController::class, 'destroy'])
        ->name('emergency-contacts.destroy');
});

// READ kategori asesmen: staff BK & konselor
Route::middleware('auth')->group(function () {
    Route::get('/assessment/categories', [AssessmentCategoryController::class, 'index'])
        ->name('assessment-categories.index');
});

// CRUD kategori asesmen: hanya konselor
Route::middleware(['auth', 'konselor'])->group(function () {
    Route::get('/assessment/categories/create', [AssessmentCategoryController::class, 'create'])
        ->name('assessment-categories.create');

    Route::post('/assessment/categories', [AssessmentCategoryController::class, 'store'])
        ->name('assessment-categories.store');

    Route::get('/assessment/categories/{assessment_category}/edit', [AssessmentCategoryController::class, 'edit'])
        ->name('assessment-categories.edit');

    Route::put('/assessment/categories/{assessment_category}', [AssessmentCategoryController::class, 'update'])
        ->name('assessment-categories.update');

    Route::delete('/assessment/categories/{assessment_category}', [AssessmentCategoryController::class, 'destroy'])
        ->name('assessment-categories.destroy');
});

// BANK PERTANYAAN & OPSI JAWABAN (Staff BK + Konselor)
Route::middleware('auth')->group(function () {

    // Pertanyaan
    Route::get('/assessment/questions', [AssessmentQuestionController::class, 'index'])
        ->name('assessment-questions.index');

    Route::get('/assessment/questions/create', [AssessmentQuestionController::class, 'create'])
        ->name('assessment-questions.create');

    Route::post('/assessment/questions', [AssessmentQuestionController::class, 'store'])
        ->name('assessment-questions.store');

    Route::get('/assessment/questions/{assessment_question}/edit', [AssessmentQuestionController::class, 'edit'])
        ->name('assessment-questions.edit');

    Route::put('/assessment/questions/{assessment_question}', [AssessmentQuestionController::class, 'update'])
        ->name('assessment-questions.update');

    Route::delete('/assessment/questions/{assessment_question}', [AssessmentQuestionController::class, 'destroy'])
        ->name('assessment-questions.destroy');

    // Opsi jawaban (nested per-pertanyaan)
    Route::get('/assessment/questions/{question}/options', [AssessmentAnswerOptionController::class, 'index'])
        ->name('assessment-options.index');

    Route::get('/assessment/questions/{question}/options/create', [AssessmentAnswerOptionController::class, 'create'])
        ->name('assessment-options.create');

    Route::post('/assessment/questions/{question}/options', [AssessmentAnswerOptionController::class, 'store'])
        ->name('assessment-options.store');

    Route::get('/assessment/questions/{question}/options/{option}/edit', [AssessmentAnswerOptionController::class, 'edit'])
        ->name('assessment-options.edit');

    Route::put('/assessment/questions/{question}/options/{option}', [AssessmentAnswerOptionController::class, 'update'])
        ->name('assessment-options.update');

    Route::delete('/assessment/questions/{question}/options/{option}', [AssessmentAnswerOptionController::class, 'destroy'])
        ->name('assessment-options.destroy');
});

// INDEX: bisa diakses semua yang login (staff_bk & konselor)
Route::middleware('auth')->group(function () {
    Route::get('/assessment/risk-rules', [AssessmentRiskRuleController::class, 'index'])
        ->name('assessment-risk-rules.index');
});

// CRUD: hanya konselor
Route::middleware(['auth', 'konselor'])->group(function () {
    Route::get('/assessment/risk-rules/create', [AssessmentRiskRuleController::class, 'create'])
        ->name('assessment-risk-rules.create');

    Route::post('/assessment/risk-rules', [AssessmentRiskRuleController::class, 'store'])
        ->name('assessment-risk-rules.store');

    Route::get('/assessment/risk-rules/{assessment_risk_rule}/edit', [AssessmentRiskRuleController::class, 'edit'])
        ->name('assessment-risk-rules.edit');

    Route::put('/assessment/risk-rules/{assessment_risk_rule}', [AssessmentRiskRuleController::class, 'update'])
        ->name('assessment-risk-rules.update');

    Route::delete('/assessment/risk-rules/{assessment_risk_rule}', [AssessmentRiskRuleController::class, 'destroy'])
        ->name('assessment-risk-rules.destroy');
});

// Flow asesmen publik (mahasiswa anonim)
Route::get('/assessment/start', [AssessmentSessionController::class, 'start'])
    ->name('assessment.start');

Route::get('/assessment/{session}', [AssessmentSessionController::class, 'showForm'])
    ->name('assessment.form');

Route::post('/assessment/{session}', [AssessmentSessionController::class, 'submit'])
    ->name('assessment.submit');

Route::get('/assessment/{session}/result', [AssessmentSessionController::class, 'result'])
    ->name('assessment.result');