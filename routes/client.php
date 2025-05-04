<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\ClientController;

Route::prefix('client')->name('client.')->group(function(){

    Route::middleware(['guest:client','PreventBackHistory'])->group(function(){
        Route::controller(ClientController::class)->group(function(){
           Route::get('/login','login')->name('login');
           Route::post('/login-handler','loginHandler')->name('login-handler');
           Route::get('/register','register')->name('register');
           Route::post('/create','createClient')->name('create');
           Route::get('/account/verify/{token}','verifyAccount')->name('verify');
           Route::get('/register-success','registerSuccess')->name('register-success');
           Route::get('/forgot-password','forgotPassword')->name('forgot-password');
           Route::post('/send-password-reset-link','sendPasswordResetLink')->name('send-password-reset-link');
           Route::get('/password/reset/{token}','showResetForm')->name('reset-password');
           Route::post('/reset-password-handler','resetPasswordHandler')->name('reset-password-handler');
        });
    });

    Route::middleware(['auth:client','PreventBackHistory'])->group(function(){

        Route::controller(ClientController::class)->group(function(){
           Route::get('/','home')->name('home');
           Route::post('/logout','logoutHandler')->name('logout');
           Route::get('/client-details','clientDetails')->name('client-details');
           Route::post('/save-client-details','saveClientDetails')->name('save-client-details');
           Route::get('/find-servise','findServise')->name('find-servise');
           Route::get('/profile','profileView')->name('profile');
           Route::post('/update_profile','updateProfile')->name('update_profile');
           Route::post('/change-profile-picture','changeProfilePicture')->name('change-profile-picture');
           Route::match(['get', 'post'],'/find-sellers','findSellers')->name('find-sellers');
           Route::post('/servise-request','serviseRequest')->name('servise-request');


           Route::get('/change-password','changePassword')->name('change-password');
           Route::get('/shop-settings','shopSettings')->name('shop-settings');
           Route::post('/shop-setup','shopSetup')->name('shop-setup');

           Route::get('/baby','baby')->name('baby');
           Route::get('/report','report')->name('report');
           Route::post('/report-data','getBabyHealthRecord')->name('report-data');
           Route::get('/height-report','heightReport')->name('height-report');
        });

    });

 });
