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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

//Auth
Route::post('/login', 'AuthController@login');
Route::post('/register', 'AuthController@register');

//Boletos
Route::get('/boleto', 'BoletoController@index');

//CEP
Route::get('/cep/{cep}', 'CepController@show');

//Users
Route::apiResource('user', 'UserController');
