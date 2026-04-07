<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParentUser\ParentController;
use App\Http\Controllers\ParentUser\GrowthRecordController;
use App\Http\Controllers\ParentUser\BabyVaccinationController;

Route::prefix('parent')->name('parent.')->group(function () {

    Route::middleware(['guest:parent', 'PreventBackHistory'])->group(function () {
        Route::controller(ParentController::class)->group(function () {
            Route::get('/login', 'login')->name('login');
            Route::post('/login-handler', 'loginHandler')->name('login-handler');
            Route::get('/register', 'register')->name('register');
            Route::post('/create', 'createParent')->name('create');
            Route::get('/account/verify/{token}', 'verifyAccount')->name('verify');
            Route::get('/register-success', 'registerSuccess')->name('register-success');
            Route::get('/forgot-password', 'forgotPassword')->name('forgot-password');
            Route::post('/send-password-reset-link', 'sendPasswordResetLink')->name('send-password-reset-link');
            Route::get('/password/reset/{token}', 'showResetForm')->name('reset-password');
            Route::post('/reset-password-handler', 'resetPasswordHandler')->name('reset-password-handler');
        });
    });

    Route::middleware(['auth:parent', 'PreventBackHistory'])->group(function () {

        Route::controller(ParentController::class)->group(function () {
            Route::get('/', 'home')->name('home');
            Route::post('/logout', 'logoutHandler')->name('logout');
            Route::get('/parent-details', 'parentDetails')->name('parent-details');
            Route::post('/save-parent-details', 'saveParentDetails')->name('save-parent-details');
            Route::get('/profile', 'profileView')->name('profile');
            Route::post('/update_profile', 'updateProfile')->name('update_profile');
            Route::post('/change-password', 'changePassword')->name('change-password');
            Route::post('/change-profile-picture', 'changeProfilePicture')->name('change-profile-picture');
            Route::get('/baby', 'baby')->name('baby');
            Route::get('/report', 'report')->name('report');
            Route::post('/report-data', 'getBabyHealthRecord')->name('report-data');
            Route::get('/height-report', 'heightReport')->name('height-report');
        });

        // Notification routes
        Route::prefix('notifications')->name('notification.')->group(function () {
            Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('index');
            Route::post('/{id}/read', [App\Http\Controllers\NotificationController::class, 'markRead'])->name('mark-read');
            Route::get('/unread-count', [App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('unread-count');
            Route::post('/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('mark-all-read');
        });

        // Appointment routes
        Route::prefix('appointments')->name('appointment.')->group(function () {
            Route::get('/', [App\Http\Controllers\ParentUser\AppointmentController::class, 'index'])->name('index');
            Route::get('/book', [App\Http\Controllers\ParentUser\AppointmentController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\ParentUser\AppointmentController::class, 'store'])->name('store');
            Route::get('/midwife-availability', [App\Http\Controllers\ParentUser\AppointmentController::class, 'getAvailability'])->name('availability');
            Route::get('/{id}', [App\Http\Controllers\ParentUser\AppointmentController::class, 'show'])->name('show');
            Route::delete('/{id}', [App\Http\Controllers\ParentUser\AppointmentController::class, 'destroy'])->name('destroy');
        });

        // Chat routes
        Route::prefix('chat')->name('chat.')->group(function () {
            Route::get('/', [App\Http\Controllers\ChatController::class, 'index'])->name('index');
            Route::get('/{chatRoomId}', [App\Http\Controllers\ChatController::class, 'show'])->name('show')->middleware('chat.confirmed');
            Route::get('/{chatRoomId}/messages', [App\Http\Controllers\ChatController::class, 'getMessages'])->name('messages');
            Route::post('/{chatRoomId}/send', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('send');
            Route::post('/{chatRoomId}/mark-read', [App\Http\Controllers\ChatController::class, 'markRead'])->name('mark-read');
        });

        // Baby Vaccination routes (parent can view vaccination schedules for their babies)
        Route::prefix('baby-vaccinations')->name('baby-vaccination.')->group(function () {
            Route::get('/{babyId}', [BabyVaccinationController::class, 'index'])->name('index');
        });

        // Growth Record routes (parent can view their baby's growth records)
        Route::prefix('growth-records')->name('growth-record.')->group(function () {
            Route::get('/{babyId}', [GrowthRecordController::class, 'index'])->name('index');
            Route::get('/{babyId}/{id}', [GrowthRecordController::class, 'show'])->name('show');
            Route::post('/', [GrowthRecordController::class, 'store'])->name('store');
            Route::post('/{recordId}/predict', [GrowthRecordController::class, 'generatePrediction'])->name('generate-prediction');
        });
    });
});
