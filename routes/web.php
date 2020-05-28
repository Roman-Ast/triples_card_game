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
Route::get('/makePassword', 'PasswordController@makePassword')->name('passwordGenerate');
Route::get('/game/{id}', 'GameController@index')->name('game');
Route::get('/admin_panel', 'GameController@admin_panel')->name('admin_panel');
Route::get('/runServer', "GameController@runServer")->name('runServer');

Route::get('/', function() {
    return view("welcome");
});
Route::post('/chargeBalance', 'GameController@chargeBalanceOutGame')->name('chargeBalanceOutGame');
Route::get('/login', "LoginController@index");
Route::post('/login', "LoginController@login")->name("login");

Route::get('/register', "RegisterController@index");
Route::post('/register', "RegisterController@register")->name("register");
