<?php

use App\Http\Controllers\Userzone\AccommodationController;
use App\Http\Controllers\Userzone\CriterionController;
use App\Http\Controllers\Userzone\SelectionController;
use App\Http\Controllers\Userzone\DashboardController;
use App\Http\Controllers\Userzone\ProfileController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

/*
 * Public Website routes
 */
Route::get('/', WelcomeController::class)->name('welcome');


/*
 * Authentication routes
 */
require __DIR__.'/auth.php';

/*
 * Userzone routes
 */
Route::middleware('auth')->group(function () {
    // For the user's dashboard (after login)
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/user/selections', [SelectionController::class, 'index'])->name('userzone.selections.index');
    Route::get('/user/selections/create', [SelectionController::class, 'create'])->name('userzone.selections.create');
    Route::post('/user/selections', [SelectionController::class, 'store'])->name('userzone.selections.store');
    Route::get('/user/selections/{selection}/edit', [SelectionController::class, 'edit'])->name('userzone.selections.edit');
    Route::put('/user/selections/{selection}', [SelectionController::class, 'update'])->name('userzone.selections.update');
    Route::get('/user/selections/{selection}', [SelectionController::class, 'show'])->name('userzone.selections.show');
    Route::prefix('/user/selections/{selection}')->name('userzone.selections.')->scopeBindings()->group(function () {
        Route::get('/accommodations/{accommodation}', [AccommodationController::class, 'show'])->name('accommodations.show');
        Route::get('/criteria', [CriterionController::class, 'index'])->name('criteria.index');
        Route::post('/criteria', [CriterionController::class, 'store'])->name('criteria.store');
        Route::get('/criteria/{criterion}/edit', [CriterionController::class, 'edit'])->name('criteria.edit');
        Route::put('/criteria/{criterion}', [CriterionController::class, 'update'])->name('criteria.update');
        Route::delete('/criteria/{criterion}', [CriterionController::class, 'destroy'])->name('criteria.destroy');
    });

    // For the user's profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
