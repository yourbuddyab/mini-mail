<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ProccesStatusController;
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
require __DIR__.'/auth.php';
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::get('campaign', [CampaignController::class, 'index']);
    Route::post('campaign', [CampaignController::class, 'store']);
    Route::patch('campaign/{campaign}', [CampaignController::class, 'update']);
    Route::get('campaign/{campaign}/edit', [CampaignController::class, 'edit']);
    Route::get('status/{campaign_id}', [ProccesStatusController::class, 'index']);
});