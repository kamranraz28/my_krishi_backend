<?php

use App\Http\Controllers\Web\AgentController;
use App\Http\Controllers\Web\FaqController;
use App\Http\Controllers\Web\InvestorController;
use App\Http\Controllers\Web\ProjectController;
use App\Http\Controllers\Web\TermsController;
use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('', function () {
    return view('login');
})->name('login');

Route::get('/cache-refresh', [WebController::class, 'refreshProjectCache']);
Route::get('/cache-size', [WebController::class, 'getCacheSize']);

Route::get('/test', function () {
    Mail::raw('Hi', function ($message) {
        $message->to('kamranraz28@gmail.com')
                ->subject('Test Email');
    });

    return 'Test email sent to kamranraz28@gmail.com';
});


Route::post('/user-login', [WebController::class, 'userLogin'])->name('userLogin');
Route::get('/terms-and-conditions/show/{id?}', [TermsController::class, 'show'])->name('terms.show');


Route::middleware(['auth', 'preventBackAfterLogout'])->group(function () {
    Route::get('/dashboard', [WebController::class, 'dashboard'])->name('dashboard');
    Route::get('/user-logout', [WebController::class, 'userLogout'])->name('userLogout');

    Route::prefix('projects')->name('projects.')->group(function () {
        Route::resource('/', ProjectController::class)->parameters(['' => 'project']);
        Route::get('/{id?}/people', [ProjectController::class, 'people'])->name('people');
        Route::get('/{id?}/updates', [ProjectController::class, 'updates'])->name('updates');
        Route::post('/filter', [ProjectController::class, 'filter'])->name('filter');
        Route::get('/{id?}/finance', [ProjectController::class, 'finance'])->name('finance');
        Route::post('/finance/store', [ProjectController::class, 'financeStore'])->name('financeStore');
        Route::get('/{id?}/start', [ProjectController::class, 'start'])->name('start');
        Route::post('/close', [ProjectController::class, 'close'])->name('close');
        Route::get('/{id?}/finance/details', [ProjectController::class, 'financeDetails'])->name('financeDetails');
        Route::get('/{id?}/finance/details/print', [ProjectController::class, 'printFinanceDetails'])->name('printFinanceDetails');
        Route::post('/{id?}/comment', [ProjectController::class, 'comment'])->name('comment');
        Route::post('/assign-agent', [ProjectController::class, 'assignAgent'])->name('assignAgent');
        Route::get('/{id?}/remove-agent', [ProjectController::class, 'removeAgent'])->name('removeAgent');
        Route::post('/assign-investor', [ProjectController::class, 'assignInvestor'])->name('assignInvestor');

    });

    Route::prefix('faqs')->name('faqs.')->group(function () {
        Route::get('/{id?}', [FaqController::class, 'index'])->name('index');
        Route::post('/store', [FaqController::class, 'store'])->name('store');
        Route::get('/{id?}/edit', [FaqController::class, 'edit'])->name('edit');
        Route::put('/update/{id?}', [FaqController::class, 'update'])->name('update');
        Route::get('/{id?}/delete', [FaqController::class, 'delete'])->name('delete');

    });


    Route::prefix('investors')->name('investors.')->group(function () {
        Route::resource('/', InvestorController::class)->parameters(['' => 'investor']);
        Route::post('/filter', [InvestorController::class, 'filter'])->name('filter');
        Route::get('/{id?}/suspend', [InvestorController::class, 'suspend'])->name('suspend');
        Route::get('/{id?}/activate', [InvestorController::class, 'activate'])->name('activate');
        Route::get('/{id?}/nid/view', [InvestorController::class, 'nid'])->name('nid');
        Route::get('/{id?}/blank-cheque/view', [InvestorController::class, 'cheque'])->name('cheque');
        Route::get('/{id?}/history', [InvestorController::class, 'history'])->name('history');


    });

    Route::prefix('agents')->name('agents.')->group(function () {
        Route::resource('/', AgentController::class)->parameters(['' => 'agent']);
        Route::post('/filter', [AgentController::class, 'filter'])->name('filter');
        Route::get('/{id?}/suspend', [AgentController::class, 'suspend'])->name('suspend');
        Route::get('/{id?}/activate', [AgentController::class, 'activate'])->name('activate');

    });

    Route::prefix('terms-and-conditions')->name('conditions.')->group(function () {
        Route::resource('/', TermsController::class)->parameters(['' => 'term']);
    });


    Route::get('office-payment/pending', [WebController::class, 'pendingPayment'])->name('officePendingPayment');
    Route::get('bank-payment/pending', [WebController::class, 'bankPendingPayment'])->name('bankPendingPayment');
    Route::get('office-payment/confirm/{id?}', [WebController::class, 'confirmOfficePayment'])->name('confirmOfficePayment');
    Route::get('office-payment/cancel/{id?}', [WebController::class, 'cancelOfficePayment'])->name('cancelOfficePayment');
    Route::get('bank-payment/confirm/{id?}', [WebController::class, 'confirmBankPayment'])->name('confirmBankPayment');
    Route::get('bank-payment/cancel/{id?}', [WebController::class, 'cancelBankPayment'])->name('cancelBankPayment');
    Route::get('bank-receipt/view/{id?}', [WebController::class, 'viewBankReceopt'])->name('viewBankReceopt');


});
