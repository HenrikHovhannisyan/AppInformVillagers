<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Auth;
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

Route::get('/', [AdminController::class, 'index'])->name('admin.index');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
    Route::patch('/admin/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('admin.users.toggleStatus');

    Route::patch('/admin/accounts/{account}/approve', [AccountController::class, 'approve'])->name('admin.accounts.approve');
    Route::get('/admin/accounts/{account}/edit', [AccountController::class, 'edit'])->name('admin.accounts.edit');
    Route::patch('/account/{account}', [AccountController::class, 'updateWeb'])->name('account.update');

    Route::patch('/admin/users/{id}/statistics', [UserController::class, 'updateStatistic'])->name('admin.users.updateStatistic');

});
