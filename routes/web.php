<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Auth\LoginController@login');

    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'Auth\RegisterController@register');

    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');
});

Route::post('logout', 'Auth\LoginController@logout')->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', 'Auth\VerificationController@verify')
        ->name('verification.verify');
    Route::post('email/verification-notification', 'Auth\VerificationController@resend')
        ->middleware('throttle:6,1')
        ->name('verification.send');
    Route::post('email/resend', 'Auth\VerificationController@resend')
        ->middleware('throttle:6,1')
        ->name('verification.resend');
});

Route::resource('kategori-se', 'KategoriSEController')->middleware('auth');
Route::resource('kerangka-kerja', 'KerangkaKerjaController')->middleware('auth');
Route::resource('tata-kelola', 'TataKelolaController')->middleware('auth');
Route::resource('pengelolaan-aset', 'PengelolaanAsetController')->middleware('auth');
Route::resource('risiko', 'RisikoController')->middleware('auth');
Route::resource('teknologi', 'TeknologiController')->middleware('auth');
Route::resource('responden', 'RespondenController')->middleware('auth');

//Route::get('tata-kelola-status', 'TataKelolaController@status')->name('tata-kelola.status')->middleware('auth');

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/tentang-kami', 'HomeController@tentangKami')->name('tentang-kami')->middleware('auth');
Route::get('/json-radar', 'HomeController@getJsonRadar')->name('json-radar');
Route::get('/petunjuk-kami', 'HomeController@petunjukKami')->name('petunjuk-kami')->middleware('auth');
