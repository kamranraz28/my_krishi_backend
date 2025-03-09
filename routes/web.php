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

Route::post('/user-login', [WebController::class, 'userLogin'])->name('userLogin');

Route::middleware(['auth', 'preventBackAfterLogout'])->group(function () {
    Route::get('/dashboard', [WebController::class, 'dashboard'])->name('dashboard');
    Route::get('/user-logout', [WebController::class, 'userLogout'])->name('userLogout');
    Route::get('/projects', [WebController::class, 'projects'])->name('projects');
    Route::get('/project-edit/{id?}', [WebController::class, 'projectEdit'])->name('project.edit');
    Route::post('/project-store', [WebController::class, 'storeProject'])->name('storeProject');
    Route::put('/project-update/{id}', [WebController::class, 'updateProject'])->name('projects.update');
    Route::get('/projects/updates/{id?}', [WebController::class, 'projectUpdates'])->name('projectUpdates');

    Route::post('/comment/{id?}', [WebController::class, 'comment'])->name('comment');

});
