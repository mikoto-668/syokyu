<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TimeController;
use App\Http\Controllers\BreaktimeController;
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

    Route::middleware('auth')->group(function () {
    Route::get('/', [AuthController::class, 'index']);
    });

     //出退勤打刻
    Route::get('/timein',[TimeController::class,'timein']);
    Route::get('/timeout',[TimeController::class,'timeout']);
     //休憩打刻
    Route::get('/breakin',[BreaktimeController::class,'breakin']);
    Route::get('/breakout',[BreaktimeController::class,'breakout']);

    Route::get('/categories', [AuthController::class, 'index']);
    Route::get('/daily', [TimeController::class, 'index']);
    //  //勤怠実績
    // Route::get('/performance','TimeController@performance');
    // Route::post('/performance','TimeController@result');
    //  //日次勤怠
    // Route::get('/time/daily','TimeController@daily');
    // Route::post('/time/daily','TimeController@dailyResult');