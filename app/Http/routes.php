<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('diagnose', 'DiagnoseController@index');
Route::get('diagnose/create', 'DiagnoseController@create');
Route::post('diagnose', 'DiagnoseController@store');
Route::get('diagnose/edit/{id}', 'DiagnoseController@edit');
Route::post('diagnose/{id}', 'DiagnoseController@update');
Route::get('diagnose/{id}', 'DiagnoseController@show');

Route::get('symptom', 'SymptomController@index');
Route::get('symptom/{id}', 'SymptomController@show');

//Route::get('home', 'HomeController@index');
//
//Route::controllers([
//	'auth' => 'Auth\AuthController',
//	'password' => 'Auth\PasswordController',
//]);
