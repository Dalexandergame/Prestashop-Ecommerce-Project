<?php

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

//Application routes
Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/welcome', function () {
    return view('welcome');
});

//Deliveries routes
Route::get('/', 'HomeController@deliveries')->name('deliveries');
Route::get('/deliveries-bloc', 'HomeController@deliveries_bloc')->name('deliveries-bloc');
Route::get('/delivery/{id}/details', 'HomeController@details')->name('deliveries-detail');
Route::post('/delivery/{id}/{reference}/change', 'HomeController@change_state')->name('deliveries-change-state');
Route::post('/delivery/{id}/{nextId}/{reference}/change-accordingly', 'HomeController@change_state_accordingly')->name('deliveries-change-state-accordingly');

//Other routes
Route::post('/change_name', 'HomeController@updateName')->name('change-name');

//SMS senders
Route::get('/sms/{number}/send', 'HomeController@sendSMS')->name('sms-sender');
Route::get('/cron/send-sms', 'HomeController@cronSendSMS')->name('cron-send-sms');