<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Midwife\MidwifeController;
use App\Http\Controllers\Midwife\BabyVaccinationController;

Route::prefix('midwife')->name('midwife.')->group(function () {

    Route::middleware(['guest:midwife', 'PreventBackHistory'])->group(function () {
        Route::controller(MidwifeController::class)->group(function () {
            Route::get('/login', 'login')->name('login');
            Route::post('/login-handler', 'loginHandler')->name('login-handler');
            Route::get('/register', 'register')->name('register');
            Route::post('/create', 'createMidwife')->name('create');
            Route::get('/account/verify/{token}', 'verifyAccount')->name('verify');
            Route::get('/register-success', 'registerSuccess')->name('register-success');
            Route::get('/forgot-password', 'forgotPassword')->name('forgot-password');
            Route::post('/send-password-reset-link', 'sendPasswordResetLink')->name('send-password-reset-link');
            Route::get('/password/reset/{token}', 'showResetForm')->name('reset-password');
            Route::post('/reset-password-handler', 'resetPasswordHandler')->name('reset-password-handler');
        });
    });

    Route::middleware(['auth:midwife', 'PreventBackHistory'])->group(function () {

        Route::controller(MidwifeController::class)->group(function () {
            Route::get('/home', 'home')->name('home');
            Route::post('/logout', 'logoutHandler')->name('logout');
            Route::get('/profile', 'profileView')->name('profile');
            Route::post('/change-profile-picture', 'changeProfilePicture')->name('change-profile-picture');
            Route::get('/midwife-details', 'midwifeDetails')->name('midwife-details');
            Route::post('/save-midwife-details', 'saveMidwifeDetails')->name('save-midwife-details');
            Route::post('/update_profile', 'updateProfile')->name('update_profile');
            Route::post('/change-password', 'changePassword')->name('change-password');
            Route::get('/job', 'job')->name('job');
            Route::post('/document-upload', 'midwifeDocumentUpload')->name('document-upload');
            Route::get('/parent', 'parentList')->name('parent');
            Route::get('/baby', 'baby')->name('baby');
            Route::post('/baby-store', 'babyStore')->name('baby-store');
            Route::post('/weight-record-store', 'weightRecordStore')->name('weight-record-store');
            Route::get('/report', 'report')->name('report');
            Route::post('/report-data', 'getBabyHealthRecord')->name('report-data');
            Route::get('/height-report', 'heightReport')->name('height-report');
            Route::get('/notice', 'notice')->name('notice');
        });

        // Baby Vaccination routes (midwife manages vaccinations for assigned babies)
        Route::prefix('baby-vaccinations')->name('baby-vaccination.')->group(function () {
            Route::get('/{babyId}', [BabyVaccinationController::class, 'index'])->name('index');
            Route::post('/', [BabyVaccinationController::class, 'schedule'])->name('schedule');
            Route::put('/{recordId}', [BabyVaccinationController::class, 'update'])->name('update');
        });
    });
});
