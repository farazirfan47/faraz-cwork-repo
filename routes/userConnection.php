<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User Connection Routes
|--------------------------------------------------------------------------
|
| Here is where you can register user connections routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/suggestions', [App\Http\Controllers\SuggestionController::class, 'index']);

Route::get('/sent-requests', [App\Http\Controllers\SentRequestController::class, 'index']);
Route::post('/send-request', [App\Http\Controllers\SentRequestController::class, 'store']);
Route::delete('/withdraw-request', [App\Http\Controllers\SentRequestController::class, 'destroy']);

Route::get('/received-requests', [App\Http\Controllers\ReceivedRequestController::class, 'index']);

Route::post('/connect', [App\Http\Controllers\ConnectionController::class, 'update']);
Route::get('/connections', [App\Http\Controllers\ConnectionController::class, 'index']);
Route::delete('/remove-connection', [App\Http\Controllers\ConnectionController::class, 'destroy']);

Route::get('/common-connections', [App\Http\Controllers\CommonConnectionController::class, 'index']);
Route::get('/connections-counts', [App\Http\Controllers\ConnectionCountController::class, 'index']);
