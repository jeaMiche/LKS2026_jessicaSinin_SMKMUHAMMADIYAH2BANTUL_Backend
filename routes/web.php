<?php

use App\Http\Controllers\FinancingApplicationController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:applicant')->group(function () {
        Route::post('/financing', [FinancingApplicationController::class, 'store'])->name('financing.store');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/financing/create', [FinancingApplicationController::class, 'create'])->name('financing.create');
        Route::get('financing/{financing}/pay', [FinancingApplicationController::class, 'paymentForm'])->name('financing.payment.create');
        Route::post('financing/{financing}/pay', [FinancingApplicationController::class, 'pay'])->name('financing.payment.store');
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('financing/trashed', [FinancingApplicationController::class, 'trashed'])->name('financing.trashed');
        Route::patch('financing/{id}/restore', [FinancingApplicationController::class, 'restore'])->name('financing.restore');

        Route::patch('financing/{financing}/draft', [FinancingApplicationController::class, 'draft'])->name('financing.draft');
        Route::patch('financing/{financing}/submitted', [FinancingApplicationController::class, 'submitted'])->name('financing.submitted');
        Route::patch('financing/{financing}/verified', [FinancingApplicationController::class, 'verified'])->name('financing.verified');
        Route::patch('financing/{financing}/reject', [FinancingApplicationController::class, 'reject'])->name('financing.reject');
    });

    Route::resource('financing', FinancingApplicationController::class)
        ->middleware('role:applicant,admin');

    Route::delete('financing/{financing}', [FinancingApplicationController::class, 'destroy'])
        ->name('financing.destroy')
        ->middleware('role:applicant,admin');
});

require __DIR__ . '/auth.php';
