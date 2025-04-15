<?php

use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;

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


Route::post('/user-login', [WebController::class, 'userLogin'])->name('userLogin');

Route::middleware(['auth', 'preventBackAfterLogout'])->group(function () {
    Route::get('/dashboard', [WebController::class, 'dashboard'])->name('dashboard');
    Route::get('/user-logout', [WebController::class, 'userLogout'])->name('userLogout');
    Route::get('/projects', [WebController::class, 'projects'])->name('projects');
    Route::post('/project-filter', [WebController::class, 'projectFilter'])->name('projectFilter');
    Route::get('/project-edit/{id?}', [WebController::class, 'projectEdit'])->name('project.edit');
    Route::post('/project-store', [WebController::class, 'storeProject'])->name('storeProject');
    Route::put('/project-update/{id}', [WebController::class, 'updateProject'])->name('projects.update');
    Route::get('/projects/updates/{id?}', [WebController::class, 'projectUpdates'])->name('projectUpdates');
    Route::get('/project-people/{id?}', [WebController::class, 'projectPeople'])->name('projectPeople');
    Route::get('/investor-history/{id?}', [WebController::class, 'investorHistory'])->name('investorHistory');
    Route::post('/comment/{id?}', [WebController::class, 'comment'])->name('comment');
    Route::post('/assign-agent', [WebController::class, 'assignAgent'])->name('assign.agent');
    Route::delete('/agent/{id}/delete', [WebController::class, 'deleteAgent'])->name('agent.delete');
    Route::post('/assign-investor', [WebController::class, 'assignInvestor'])->name('assign.investor');
    Route::get('/agents', [WebController::class, 'agents'])->name('agents');
    Route::delete('/agent/delete/{id?}', [WebController::class, 'agentDelete'])->name('agentDelete');
    Route::post('/agent-store', [WebController::class, 'agentStore'])->name('agentStore');
    Route::get('/investors', [WebController::class, 'investors'])->name('investors');
    Route::delete('/investor/delete/{id?}', [WebController::class, 'investorDelete'])->name('investorDelete');
    Route::post('/investor-store', [WebController::class, 'investorStore'])->name('investorStore');
    Route::get('/projects/costs/{id?}', [WebController::class, 'projectCosts'])->name('projectCosts');
    Route::post('/projects/costs/store', [WebController::class, 'projectCostsStore'])->name('projectCostsStore');
    Route::post('/projects/close', [WebController::class, 'projectClose'])->name('projectClose');
    Route::get('/projects/finance/details/{id?}', [WebController::class, 'financeDetails'])->name('financeDetails');
    Route::get('project/print-finance-details/{id}', [WebController::class, 'printFinanceDetails'])->name('printFinanceDetails');
    Route::post('/react', [WebController::class, 'react'])->name('react');
    Route::get('office-payment/pending', [WebController::class, 'pendingPayment'])->name('officePendingPayment');
    Route::get('bank-payment/pending', [WebController::class, 'bankPendingPayment'])->name('bankPendingPayment');
    Route::get('office-payment/confirm/{id?}', [WebController::class, 'confirmOfficePayment'])->name('confirmOfficePayment');
    Route::get('office-payment/cancel/{id?}', [WebController::class, 'cancelOfficePayment'])->name('cancelOfficePayment');
    Route::get('bank-receipt/view/{id?}', [WebController::class, 'viewBankReceopt'])->name('viewBankReceopt');
    Route::get('investors/{id?}/nid/view', [WebController::class, 'viewNid'])->name('viewNid');
    Route::get('investors/{id?}/blank-check/view', [WebController::class, 'viewCheck'])->name('viewCheck');

});
