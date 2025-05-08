<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\DashboardController;// Создайте для главной страницы
use App\Http\Controllers\CategoryController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia; // Если используете Breeze с Vue/React
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\DebtPaymentController;
use App\Http\Controllers\MandatoryPaymentController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\RecurringPaymentController;
use App\Http\Controllers\ShiftController;


// Маршрут для главной страницы (Dashboard)
Route::get('/', function () {
    // Если используете Blade
    // return view('welcome', [
    //     'canLogin' => Route::has('login'),
    //     'canRegister' => Route::has('register'),
    //     'laravelVersion' => Application::VERSION,
    //     'phpVersion' => PHP_VERSION,
    // ]);

    // Если используете Inertia (Vue/React)
     return Inertia::render('Welcome', [
         'canLogin' => Route::has('login'),
         'canRegister' => Route::has('register'),
         'laravelVersion' => Application::VERSION,
         'phpVersion' => PHP_VERSION,
     ]);
});

// Группа маршрутов, требующих аутентификации
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'menu'])->name('profile.menu');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Ресурсные маршруты для CRUD
    Route::resource('accounts', AccountController::class);
    Route::resource('transactions', TransactionController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('debts', DebtController::class);
    Route::resource('mandatory-payments', MandatoryPaymentController::class);
    Route::resource('recurring-payments', RecurringPaymentController::class);

    // Возможно, понадобятся отдельные маршруты для платежей по долгам
    // Route::post('debts/{debt}/payments', [DebtPaymentController::class, 'store'])->name('debts.payments.store');
     Route::get('/analysis', [AnalysisController::class, 'index'])->name('analysis.index');
    // Маршрут для сохранения платежа по конкретному долгу
    Route::post('debts/{debt}/payments', [DebtPaymentController::class, 'store'])->name('debts.payments.store');

});

// Categories routes
Route::middleware(['auth'])->group(function () {
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

// Currencies routes
Route::middleware(['auth'])->group(function () {
    Route::get('/currencies', [CurrencyController::class, 'index'])->name('currencies.index');
    Route::post('/currencies', [CurrencyController::class, 'store'])->name('currencies.store');
    Route::put('/currencies/{currency}', [CurrencyController::class, 'update'])->name('currencies.update');
    Route::delete('/currencies/{currency}', [CurrencyController::class, 'destroy'])->name('currencies.destroy');
});

// Shifts routes
Route::middleware(['auth'])->group(function () {
    Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
    Route::post('/shifts', [ShiftController::class, 'store'])->name('shifts.store');
    Route::put('/shifts/{shift}', [ShiftController::class, 'update'])->name('shifts.update');
    Route::delete('/shifts/{shift}', [ShiftController::class, 'destroy'])->name('shifts.destroy');
    Route::get('/shifts/{shift}', [ShiftController::class, 'show'])->name('shifts.show');
});

require __DIR__.'/auth.php'; // Маршруты аутентификации из Breeze
