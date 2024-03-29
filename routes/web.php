<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactNoteController;
use App\Http\Controllers\Settings\AccountController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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


Route::get('/', WelcomeController::class);

// Contacts Routes
Route::resource('/contacts', ContactController::class);
Route::delete('/contacts/{contact}/restore', [ContactController::class, 'restore'])
    ->name('contacts.restore')
    ->withTrashed();
Route::delete('/contacts/{contact}/force-delete', [ContactController::class, 'forceDelete'])
    ->name('contacts.force-delete')
    ->withTrashed();

// Companies Routes
Route::resource('/companies', CompanyController::class);


Route::resources([
    '/tags' => TagController::class,
    '/tasks' => TaskController::class
]);

Route::resource('/contacts.notes', ContactNoteController::class)->shallow();
Route::resource('/activities', ActivityController::class)->parameters([
    'activities' => 'active'
]);

Auth::routes(['verify' => true]);

Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('settings/account', [AccountController::class, 'index']);
