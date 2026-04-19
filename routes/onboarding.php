<?php

use App\Http\Controllers\OnboardingController;
use Illuminate\Support\Facades\Route;

/**
 * User Onboarding Routes
 * 
 * Routes for the complete onboarding flow with KYC verification
 */

Route::middleware(['auth'])->prefix('onboarding')->name('user.onboarding')->group(function () {
    // Main onboarding dashboard
    Route::get('/', [OnboardingController::class, 'show'])->name('');

    // Personal Information Step
    Route::get('/personal-info', [OnboardingController::class, 'showPersonalInfoForm'])
        ->name('.personal-info');
    Route::post('/personal-info', [OnboardingController::class, 'submitPersonalInfo'])
        ->name('.submit-personal-info');

    // Identity Verification Step (BVN/NIN)
    Route::get('/identity-verification', [OnboardingController::class, 'showIdentityVerificationForm'])
        ->name('.identity-verification');
    Route::post('/identity-verification', [OnboardingController::class, 'submitIdentityVerification'])
        ->name('.submit-identity-verification');

    // Liveness Check Step
    Route::get('/liveness-check', [OnboardingController::class, 'showLivenessCheckForm'])
        ->name('.liveness-check');
    Route::post('/liveness-check/initiate', [OnboardingController::class, 'initiateLivenessCheck'])
        ->name('.initiate-liveness');
    Route::get('/liveness-check/status', [OnboardingController::class, 'checkLivenessStatus'])
        ->name('.check-liveness-status');
    Route::post('/liveness-callback', [OnboardingController::class, 'livenessCallback'])
        ->name('.liveness-callback');

    // Completion
    Route::get('/complete', [OnboardingController::class, 'complete'])
        ->name('.complete');

    // Development/Testing routes
    if (app()->environment('local', 'staging')) {
        Route::post('/skip-step', [OnboardingController::class, 'skipStep'])
            ->name('.skip-step');
    }
});
