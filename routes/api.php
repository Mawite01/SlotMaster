<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Bank\BankController;
use App\Http\Controllers\Api\V1\BannerController;
use App\Http\Controllers\Api\V1\GetBalanceController;
use App\Http\Controllers\Api\V1\PromotionController;
use App\Http\Controllers\Api\V1\Webhook\BetController;
use App\Http\Controllers\Api\V1\Webhook\BetNResultController;
use App\Http\Controllers\Api\V1\Webhook\CancelBetNResultController;
use App\Http\Controllers\Api\V1\Webhook\ResultController;
use App\Http\Controllers\Api\V1\Webhook\CancelBetController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/player-change-password', [AuthController::class, 'playerChangePassword']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('promotion', [PromotionController::class, 'index']);
Route::get('banner', [BannerController::class, 'index']);
Route::get('bannerText', [BannerController::class, 'bannerText']);
Route::get('popup-ads-banner', [BannerController::class, 'AdsBannerIndex']);

Route::post('GetBalance', [GetBalanceController::class, 'getBalance']);
Route::post('BetNResult', [BetNResultController::class, 'handleBetNResult']);
Route::post('CancelBetNResult', [CancelBetNResultController::class, 'handleCancelBetNResult']);
Route::post('Bet', [BetController::class, 'handleBet']);
Route::post('Result', [ResultController::class, 'handleResult']);
Route::post('CancelBet', [CancelBetController::class, 'handleCancelBet']);