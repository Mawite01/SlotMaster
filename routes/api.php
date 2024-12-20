<?php

use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Bank\BankController;
use App\Http\Controllers\Api\V1\BannerController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\DepositRequestController;
use App\Http\Controllers\Api\V1\GetBalanceController;
use App\Http\Controllers\Api\V1\PromotionController;
use App\Http\Controllers\Api\V1\Slot\GameController;
use App\Http\Controllers\Api\V1\Slot\GetDaySummaryController;
use App\Http\Controllers\Api\V1\Slot\LaunchGameController;
use App\Http\Controllers\Api\V1\TransactionController;
use App\Http\Controllers\Api\V1\WagerController;
use App\Http\Controllers\Api\V1\Webhook\AdjustmentController;
use App\Http\Controllers\Api\V1\Webhook\BetController;
use App\Http\Controllers\Api\V1\Webhook\BetNResultController;
use App\Http\Controllers\Api\V1\Webhook\BetResultController;
use App\Http\Controllers\Api\V1\Webhook\CancelBetController;
use App\Http\Controllers\Api\V1\Webhook\CancelBetNResultController;
use App\Http\Controllers\Api\V1\Webhook\RewardController;
use App\Http\Controllers\Api\V1\WithDrawRequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\GetAdminSiteLogoNameController;


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/player-change-password', [AuthController::class, 'playerChangePassword']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('promotion', [PromotionController::class, 'index']);
Route::get('banner', [BannerController::class, 'index']);
Route::get('bannerText', [BannerController::class, 'bannerText']);
Route::get('popup-ads-banner', [BannerController::class, 'AdsBannerIndex']);
Route::get('contact', [ContactController::class, 'get']);

// sameless route

Route::post('GetBalance', [GetBalanceController::class, 'getBalance']);
Route::post('BetNResult', [BetNResultController::class, 'handleBetNResult']);
Route::post('CancelBetNResult', [CancelBetNResultController::class, 'handleCancelBetNResult']);
Route::post('Bet', [BetController::class, 'handleBet']);
Route::post('Result', [BetResultController::class, 'handleResult']);
Route::post('CancelBet', [CancelBetController::class, 'handleCancelBet']);
Route::post('Adjustment', [AdjustmentController::class, 'handleAdjustment']);
Route::post('Reward', [RewardController::class, 'handleReward']);


Route::post('/GetDaySummary', [GetDaySummaryController::class, 'getDaySummary']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('GameLogin', [LaunchGameController::class, 'LaunchGame']);
    Route::get('wager-logs', [WagerController::class, 'index']); //GSC
    Route::get('transactions', [TransactionController::class, 'index']);

    Route::get('user', [AuthController::class, 'getUser']);
    Route::get('contact', [AuthController::class, 'getContact']);
    Route::get('agent', [AuthController::class, 'getAgent']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('change-password', [AuthController::class, 'changePassword']);
    Route::post('profile', [AuthController::class, 'profile']);
    Route::get('agentPaymentType', [BankController::class, 'all']);
    Route::post('deposit', [DepositRequestController::class, 'deposit']);
    Route::get('depositlog', [DepositRequestController::class, 'log']);
    Route::get('paymentType', [BankController::class, 'paymentType']);
    Route::post('withdraw', [WithDrawRequestController::class, 'withdraw']);
    Route::get('withdrawlog', [WithDrawRequestController::class, 'log']);
    Route::get('sitelogo-name', [GetAdminSiteLogoNameController::class, 'GetSiteLogoAndSiteName']);
});

Route::get('gameTypeProducts/{id}', [GameController::class, 'gameTypeProducts']);
Route::get('allGameProducts', [GameController::class, 'allGameProducts']);
Route::get('gameType', [GameController::class, 'gameType']);
Route::get('hotgamelist', [GameController::class, 'HotgameList']);
Route::get('gamelist/{provider_id}/{game_type_id}/', [GameController::class, 'gameList']);
Route::get('gameFilter', [GameController::class, 'gameFilter']);