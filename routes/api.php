<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BankDetailController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\GamePlayController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\WithdrawController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\WinningHistoryController;
use App\Http\Controllers\Api\AndarBaharController;











/*
|--------------------------------------------------------------------------
| Public APIs (No Login Required)
|--------------------------------------------------------------------------
*/



Route::post('/signup', [AuthController::class, 'signup']); //User Signup &&  Registers a new user using name, mobile number and password
Route::post('/login', [AuthController::class, 'login']); //User Login &&  Logs in user using mobile number and password
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']); // * Forgot Password  &&  * Takes mobile number and sends OTP (currently static: 1234)
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);  // * Verify OTP &&  * Verifies OTP entered by user (only OTP in request body)
Route::post('/reset-password', [AuthController::class, 'resetPassword']); // * Reset Password &&  * Resets user password using mobile number and new password


/*
|--------------------------------------------------------------------------
| Protected APIs (Login Required - Later)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']); // * Logout User && * Deletes the current access token and logs out the user
    Route::get('/profile', [ProfileController::class, 'getProfile']); // Get profile 
    Route::put('/user/update-phone', [AuthController::class, 'updatePhone']);     // Update logged-in user's phone number
    Route::post('/bank-details', [BankDetailController::class, 'store']); //Add && Update bank details
    Route::get('/bank-details', [BankDetailController::class, 'show']); //    Get bank details
    Route::get('/notifications', [NotificationController::class, 'index']); // Get all notifications
    Route::get('notifications/{id}', [NotificationController::class, 'show']);// Get notificatio threw id

    Route::get('/games', [GameController::class, 'index']);  // Get list of all available games
    Route::get('/games/{id}', [GameController::class, 'show']);  // Get details of a single game by its ID



    Route::post('/game/{gameId}/play/add', [GamePlayController::class, 'add']);  // Add a new game play entry (when a user plays a game)
    Route::get('/game/{gameId}/plays', [GamePlayController::class, 'list']); // Get all play records for a specific game

    Route::post('/play-andar/{game}', [AndarBaharController::class, 'playAndar']); //plays game in andar category between 0 to 9
    Route::post('/play-bahar/{game}', [AndarBaharController::class, 'playBahar']);  // plays game in Bahar category between 0 to 9

    Route::get('/correct-answers', [GameController::class, 'correctAnswers']); // correct answers chart

    Route::get('/chart/filter', [GameController::class, 'chartFilter']);  //chart filter api


    Route::post('/wallet/add-funds', [WalletController::class, 'addFunds']);// Add money to wallet (Credit) &&     // Used when user clicks "Add Funds" from the app
    Route::get('/wallet/history', [WalletController::class, 'history']);   //     * Wallet history (amount, type, date-time only)



    Route::post('/wallet/withdraw', [WithdrawController::class, 'store']);         // Create a new withdrawal request for the authenticated user



    Route::get('/wallet/withdraws', [WithdrawController::class, 'list']);      // Get a list of all withdrawal requests of the authenticated user




    Route::post('/payment/pay-now', [PaymentController::class, 'payNow']);  //Make a payment 



    Route::get('winning-history', [WinningHistoryController::class, 'index']);  // when display winning history when game status will be closed 

















});

