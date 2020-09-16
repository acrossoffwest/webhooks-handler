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

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::middleware('auth:web')->group(function () {
    Route::resource('webhooks', \App\Http\Controllers\WebhookController::class);
    Route::get('api/token', [\App\Http\Controllers\ApiTokenController::class, 'update']);
    Route::get('api/webhooks', [\App\Http\Controllers\Api\WebhookController::class, 'index']);
});

Route::any('api/webhook/endpoints/{user_id}/{slug}', function ($userId, $slug) {
    dd(\App\Models\Webhook::query()->where('user_id', $userId)->where('in', $slug)->get());
});
