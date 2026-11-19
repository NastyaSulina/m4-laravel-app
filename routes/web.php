<?php

use App\Http\Controllers\Userzone\AccommodationController;
use App\Http\Controllers\Userzone\CriterionController;
use App\Http\Controllers\Userzone\DashboardController;
use App\Http\Controllers\Userzone\ProfileController;
use App\Http\Controllers\Userzone\SelectionController;
use App\Http\Controllers\WelcomeController;
use App\Http\Middleware\CheckSelectionOwnership;
use Illuminate\Support\Facades\Route;

// Public ----------------------------------------------------------------------
Route::get('/', WelcomeController::class)->name('welcome');

// Authentication --------------------------------------------------------------
require __DIR__.'/auth.php';

// Userzone --------------------------------------------------------------------
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::prefix('user')->name('userzone.')->group(function () {
        Route::resource('selections', SelectionController::class)->middlewareFor(['show', 'edit', 'update', 'destroy'], CheckSelectionOwnership::class);
        Route::resource('selections.accommodations', AccommodationController::class)->except(['index'])->scoped()->middleware(CheckSelectionOwnership::class);
        Route::resource('selections.criteria', CriterionController::class)->except(['create', 'show'])->scoped()->middleware(CheckSelectionOwnership::class);
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
