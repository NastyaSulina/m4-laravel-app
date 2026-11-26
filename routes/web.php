<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\Userzone\AccommodationController;
use App\Http\Controllers\Userzone\AccommodationCriterionController;
use App\Http\Controllers\Userzone\CriterionController;
use App\Http\Controllers\Userzone\ProfileController;
use App\Http\Controllers\Userzone\SelectionController;
use App\Http\Controllers\Userzone\SelectionUserController;
use App\Http\Controllers\WelcomeController;
use App\Http\Middleware\CheckSelectionAccess;
use App\Http\Middleware\CheckSelectionOwner;
use Illuminate\Support\Facades\Route;

// Public ----------------------------------------------------------------------
Route::get('/', WelcomeController::class)->name('welcome');
Route::get('/privacy-policy', PrivacyPolicyController::class)->name('privacy-policy');
Route::get('/contacts', ContactController::class)->name('contacts');

// Authentication --------------------------------------------------------------
require __DIR__.'/auth.php';

// Userzone --------------------------------------------------------------------
Route::middleware('auth')->group(function () {
    Route::prefix('user')->name('userzone.')->group(function () {
        Route::resource('selections', SelectionController::class)->middlewareFor(['show', 'edit', 'update'], CheckSelectionAccess::class)
            ->middlewareFor('destroy', CheckSelectionOwner::class);
        Route::resource('selections.accommodations', AccommodationController::class)->except(['index'])->scoped()->middleware(CheckSelectionAccess::class);
        Route::resource('selections.criteria', CriterionController::class)->except(['create', 'show'])->scoped()->middleware(CheckSelectionAccess::class);
        Route::resource('selections.users', SelectionUserController::class)->only(['index', 'store', 'destroy'])->scoped()->middleware(CheckSelectionOwner::class);

        Route::get('selections/{selection}/accommodations/{accommodation}/criteria/edit', [AccommodationCriterionController::class, 'edit'])
            ->name('selections.accommodations.criteria.edit')->scopeBindings()->middleware(CheckSelectionAccess::class);
        Route::put('selections/{selection}/accommodations/{accommodation}/criteria', [AccommodationCriterionController::class, 'update'])
            ->name('selections.accommodations.criteria.update')->scopeBindings()->middleware(CheckSelectionAccess::class);
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
