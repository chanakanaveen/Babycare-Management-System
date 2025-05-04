<?php

use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('example-frontend');
// });
Route::controller(FrontendController::class)->group(function(){
    Route::get('/','homePage')->name('home-page');
    Route::get('/about','aboutPage')->name('about-page');
    Route::get('/contact','contactPage')->name('contact-page');
});

Route::view('/example-page', 'example-page');
Route::view('/example-auth', 'example-auth');
Route::view('/example-frontend', 'example-frontend');
