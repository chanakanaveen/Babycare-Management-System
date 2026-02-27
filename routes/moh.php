<?php

use App\Http\Controllers\MohController;
use App\Http\Controllers\VaccinationScheduleController;
use Illuminate\Support\Facades\Route;


Route::prefix('moh')->name('moh.')->group(function () {

    Route::middleware(['guest:moh', 'PreventBackHistory'])->group(function () {
        Route::view('/login', 'back.pages.moh.auth.login')->name('login');
        Route::post('login_handler', [MohController::class, 'loginHandler'])->name('login_handler');
        Route::view('/forgot_password', 'back.pages.moh.auth.forgot-password')->name('forgot-password');
        Route::post('/send-password-reset-link', [MohController::class, 'sendPasswordResetLink'])->name('send-password-reset-link');
        Route::get('/reset-password/{token}', [MohController::class, 'resetPassword'])->name('reset-password');
        Route::post('/reset-password-handler', [MohController::class, 'resetPasswordHandler'])->name('reset-password-handler');
    });

    Route::middleware(['auth:moh', 'PreventBackHistory'])->group(function () {
        Route::get('/home', [MohController::class, 'home'])->name('home');
        Route::post('/logout_handler', [MohController::class, 'logoutHandler'])->name('logout_handler');
        Route::get('/profile', [MohController::class, 'profileView'])->name('profile');
        Route::post('/update_profile', [MohController::class, 'updateProfile'])->name('update_profile');
        Route::post('/change-profile-picture', [MohController::class, 'changeProfilePicture'])->name('change-profile-picture');
        Route::post('/change-password', [MohController::class, 'changePassword'])->name('change-password');
        Route::get('/users', [MohController::class, 'users'])->name('users');
        Route::get('/parents', [MohController::class, 'sellers'])->name('parents');
        Route::get('/pending-midwives', [MohController::class, 'pendingMidwives'])->name('pending-midwives');
        Route::post('/approve-midwife', [MohController::class, 'approveMidwife'])->name('approve-midwife');
        Route::post('/revoke-midwife', [MohController::class, 'revokeMidwife'])->name('revoke-midwife');
        Route::get('/notice', [MohController::class, 'notice'])->name('notice');
        Route::post('/add-notice', [MohController::class, 'addNotice'])->name('add-notice');

        // Vaccination Schedule CRUD (MOH manages the master schedule)
        Route::prefix('vaccination-schedules')->name('vaccination-schedule.')->group(function () {
            Route::get('/', [VaccinationScheduleController::class, 'index'])->name('index');
            Route::post('/', [VaccinationScheduleController::class, 'store'])->name('store');
            Route::put('/{id}', [VaccinationScheduleController::class, 'update'])->name('update');
            Route::delete('/{id}', [VaccinationScheduleController::class, 'destroy'])->name('destroy');
        });

        // Legacy route aliases (kept for backward compatibility with existing views)
        Route::get('/vaccines', [MohController::class, 'vaccination'])->name('vaccines');
        Route::post('/addVaccine', [MohController::class, 'addVaccine'])->name('addVaccine');
    });
});
