<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/hello', function () {
    return response()->json(['message' => 'Test Successful!']);
});

Route::post('/messages/store', [ProjectController::class, 'messageStore']);


Route::post('/login', [AuthController::class, 'login']);
Route::get('/projects', [ProjectController::class, 'projectList']);
Route::get('/projects/{id?}', [ProjectController::class, 'projectDetails']);

Route::post('/otp/send', [AuthController::class, 'sendOtp']);
Route::post('/otp/verify', [AuthController::class, 'verifyOtp']);

Route::get('/shurjopay/response', [PaymentController::class, 'verifyPayment'])->name('api.shurjopay.response');


Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::group(['prefix' => 'admin'], function () {
        Route::get('/projects/create', [ProjectController::class, 'createProject']);
        Route::post('/projects/store', [ProjectController::class, 'storeProject']);
        Route::get('/projects/agents/map', [ProjectController::class, 'agentMapping']);
        Route::post('/projects/agents/map/confirm', [ProjectController::class, 'agentMappingConfirm']);

    });

    Route::group(['prefix' => 'investor'], function () {
        Route::get('/projects', [InvestorController::class, 'projectList']);
        Route::get('/projects/details/{id?}', [InvestorController::class, 'projectDetails']);
        Route::get('/projects/booking/{id?}', [InvestorController::class, 'projectBooking']);
        Route::post('/projects/booking/cart/{id?}', [InvestorController::class, 'addToCart']);
        Route::get('/carts', [InvestorController::class, 'cartList']);
        Route::get('/carts/edit/{id?}', [InvestorController::class, 'cartEdit']);
        Route::put('/carts/update/{id?}', [InvestorController::class, 'cartUpdate']);
        Route::post('/carts/remove', [InvestorController::class, 'removeFromCart']);
        Route::post('/booking/payments/online', [InvestorController::class, 'onlinePayment']);
        Route::post('/booking/payments/office', [InvestorController::class, 'officePayment']);
        Route::post('/booking/payments/bank', [InvestorController::class, 'bankPayment']);
        Route::get('/projects/my-bookings', [InvestorController::class, 'myBookings']);
        Route::get('/projects/update/{id?}', [InvestorController::class, 'projectUpdate']);
        //comment by Projectupdate id
        Route::post('/projects/comment/{id?}', [InvestorController::class, 'comment']);
        //reply by Comment id
        Route::post('/projects/reply/{id?}', [InvestorController::class, 'reply']);
        // Route::post('/reaction', [ReactionController::class, 'react']);
        Route::post('/profile/update/{id?}', [InvestorController::class, 'profileUpdate']);
        Route::get('/banks', [InvestorController::class, 'profileEdit']);
        Route::get('/notifications', [InvestorController::class, 'getUnseenNotifications']);
        Route::post('/notifications/mark-as-seen/{id?}', [InvestorController::class, 'markNotificationAsSeen']);
        Route::get('/finance', [InvestorController::class, 'finance']);
        Route::get('/finance/matured', [InvestorController::class, 'maturedFinance']);
        Route::get('/finance/details/{id?}', [InvestorController::class, 'maturedFinanceDetails']);


    });

    Route::group(['prefix' => 'agent'], function () {
        Route::get('/projects', [AgentController::class, 'projectList']);
        Route::post('/projects/update/store/{id?}', [AgentController::class, 'projectUpdateStore']);
        Route::get('/projects/update/{id?}', [AgentController::class, 'projectUpdate']);
        //comment by Projectupdate id
        Route::post('/projects/comment/{id?}', [AgentController::class, 'comment']);
        //reply by Comment id
        Route::post('/projects/reply/{id?}', [AgentController::class, 'reply']);
    });

});

