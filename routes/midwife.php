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

        // Notification routes
        Route::prefix('notifications')->name('notification.')->group(function () {
            Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('index');
            Route::post('/{id}/read', [App\Http\Controllers\NotificationController::class, 'markRead'])->name('mark-read');
            Route::get('/unread-count', [App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('unread-count');
            Route::post('/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('mark-all-read');
        });

        // Appointment routes
        Route::prefix('appointments')->name('appointment.')->group(function () {
            Route::get('/', [App\Http\Controllers\Midwife\AppointmentController::class, 'index'])->name('index');
            Route::get('/{id}', [App\Http\Controllers\Midwife\AppointmentController::class, 'show'])->name('show');
            Route::post('/{id}/confirm', [App\Http\Controllers\Midwife\AppointmentController::class, 'confirm'])->name('confirm');
            Route::post('/{id}/reject', [App\Http\Controllers\Midwife\AppointmentController::class, 'reject'])->name('reject');
            Route::post('/{id}/complete', [App\Http\Controllers\Midwife\AppointmentController::class, 'complete'])->name('complete');
        });

        // Availability routes
        Route::get('/availability', [App\Http\Controllers\Midwife\AppointmentController::class, 'editAvailability'])->name('availability');
        Route::post('/availability', [App\Http\Controllers\Midwife\AppointmentController::class, 'saveAvailability'])->name('availability.save');

        // Chat routes
        Route::prefix('chat')->name('chat.')->group(function () {
            Route::get('/', [App\Http\Controllers\ChatController::class, 'index'])->name('index');
            Route::get('/{chatRoomId}', [App\Http\Controllers\ChatController::class, 'show'])->name('show')->middleware('chat.confirmed');
            Route::get('/{chatRoomId}/messages', [App\Http\Controllers\ChatController::class, 'getMessages'])->name('messages');
            Route::post('/{chatRoomId}/send', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('send');
            Route::post('/{chatRoomId}/mark-read', [App\Http\Controllers\ChatController::class, 'markRead'])->name('mark-read');
        });

        // Baby Vaccination routes (midwife manages vaccinations for assigned babies)
        Route::prefix('baby-vaccinations')->name('baby-vaccination.')->group(function () {
            Route::get('/{babyId}', [BabyVaccinationController::class, 'index'])->name('index');
            Route::post('/', [BabyVaccinationController::class, 'schedule'])->name('schedule');
            Route::put('/{recordId}', [BabyVaccinationController::class, 'update'])->name('update');
        });

        // Bulk Vaccination routes
        Route::prefix('bulk-vaccinations')->name('bulk-vaccination.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Midwife\BulkVaccinationController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Midwife\BulkVaccinationController::class, 'store'])->name('store');
        });

        // Growth prediction route for midwife
        Route::post('/growth-records/{recordId}/predict', function (\Illuminate\Http\Request $request, $recordId) {
            $midwifeId = \Illuminate\Support\Facades\Auth::guard('midwife')->id();
            $record = \App\Models\WeightRecord::where('record_id', $recordId)->firstOrFail();
            $baby = \App\Models\Baby::where('baby_id', $record->baby_id)
                        ->where('midwife_id', $midwifeId)->firstOrFail();

            $service = new \App\Services\GrowthPredictionService();
            $prediction = $service->generatePrediction($baby, $record);

            $record->ai_prediction = $prediction;
            $record->save();

            return response()->json([
                'status' => 1,
                'msg' => 'AI prediction generated successfully.',
                'prediction' => $prediction,
            ]);
        })->name('growth-prediction.generate');
    });
});
