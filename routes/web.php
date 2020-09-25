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
    Route::get('api/broadcasting/token', [\App\Http\Controllers\BroadcastingTokenController::class, 'show']);
    Route::get('api/webhooks', [\App\Http\Controllers\Api\WebhookController::class, 'index']);
});

Route::any('api/webhook/endpoints/{broadcasting_token}/{slug}', 'App\Http\Controllers\WebhooksHandlerController')->name('endpoints');
Route::any('api/write/log', function () {
    logs()->info('TEST REQUEST');
    return response()->json([
        'headers' => request()->header(),
        'payload' => request()->all()
    ]);
})->name('api.write.log');

Route::any('api/{broadcasting_token}/webhooks', 'App\Http\Controllers\WebhooksByBroadcastingTokenController')->name('webhooks');
