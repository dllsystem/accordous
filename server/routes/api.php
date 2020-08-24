<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/property', 'PropertyController@index');
Route::post('/property', 'PropertyController@store');
Route::get('/property/{id}', 'PropertyController@show');
Route::delete('/property/{id}', 'PropertyController@destroy');

Route::get('/contract', 'ContractController@index');
Route::post('/contract', 'ContractController@store');
Route::get('/contract/{id}', 'ContractController@show');
Route::delete('/contract/{id}', 'ContractController@destroy');
