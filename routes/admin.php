<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin')->name('admin.')->group(function (){

    Route::middleware(['guest:admin','PreventBackHistory'])->group(function () {
        Route::view('/login', 'back.pages.admin.auth.login')->name('login');
        Route::post('login_handler',[AdminController::class,'loginHandler'])->name('login_handler');
        Route::view('/forgot_password', 'back.pages.admin.auth.forgot-password')->name('forgot-password');
        Route::post('/send-password-reset-link',[AdminController::class,'sendPasswordResetLink'])->name('send-password-reset-link');
        Route::get('/reset-password/{token}',[AdminController::class,'resetPassword'])->name('reset-password');
        Route::post('/reset-password-handler',[AdminController::class,'resetPasswordHandler'])->name('reset-password-handler');
    });

    Route::middleware(['auth:admin','PreventBackHistory'])->group(function () {
        // Route::view('/home', 'back.pages.admin.home')->name('home');
        Route::get('/home',[AdminController::class,'home'])->name('home');
        Route::post('/logout_handler',[AdminController::class,'logoutHandler'])->name('logout_handler');
        Route::get('/profile',[AdminController::class,'profileView'])->name('profile');
        Route::post('/update_profile',[AdminController::class,'updateProfile'])->name('update_profile');
        Route::post('/change-profile-picture',[AdminController::class,'changeProfilePicture'])->name('change-profile-picture');
        Route::post('/change-password',[AdminController::class,'changePassword'])->name('change-password');
        Route::get('/users', [AdminController::class,'users'])->name('users');
        Route::get('/sellers', [AdminController::class,'sellers'])->name('sellers');
        Route::get('/pending-seller', [AdminController::class,'pendingSeller'])->name('pending-seller');
        Route::post('/seller-verify',[AdminController::class,'sellerVerify'])->name('seller-verify');
        Route::post('/seller-unverify',[AdminController::class,'sellerUnverify'])->name('seller-unverify');
        Route::get('/vaccines', [AdminController::class,'vaccination'])->name('vaccines');
        Route::post('/addVaccine',[AdminController::class,'addVaccine'])->name('addVaccine');
        Route::get('/notice',[AdminController::class, 'notice'])->name('notice');
        Route::post('/add-notice',[AdminController::class,'addNotice'])->name('add-notice');
    });
});
