<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\GarageController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// GuestUser:

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('verify-email/{email_verification_code}', 'verifyEmail');
    Route::post('login', 'login');
    Route::post('reset-password-link', 'resetPasswordLink');
    Route::post('reset-password', 'resetPassword');
});

// Country :

Route::controller(CountryController::class)->prefix('country')->group(function () {
    Route::get('list/{id}', 'list');
    Route::post('create', 'create');
    Route::post('update/{id}', 'update');
    Route::post('delete/{id}', 'delete');
    Route::get('show', 'show');
});

// State :

Route::controller(StateController::class)->prefix('state')->group(function () {
    Route::get('list', 'list');
    Route::post('create', 'create');
    Route::post('update/{id}', 'update');
    Route::post('delete/{id}', 'delete');
    Route::get('show', 'show');
});

// City :

Route::controller(CityController::class)->prefix('city')->group(function () {
    Route::get('list', 'list');
    Route::post('create', 'create');
    Route::post('update/{id}', 'update');
    Route::post('delete/{id}', 'delete');
    Route::get('show', 'show');
});

// Garage :

Route::controller(GarageController::class)->prefix('garage')->group(function () {
    Route::get('list', 'list');
    Route::post('create', 'create');
    Route::post('update/{id}', 'update');
    Route::post('delete/{id}', 'delete');
    Route::get('show', 'show');
});

// Service Type :

Route::controller(ServiceTypeController::class)->prefix('service-type')->group(function () {
    Route::get('list', 'list');
    Route::post('create', 'create');
    Route::post('update/{id}', 'update');
    Route::post('delete/{id}', 'delete');
    Route::get('show', 'show');
});


// Logged-in User

Route::middleware('auth:sanctum')->group(function () {

    Route::controller(UserController::class)->prefix('user')->group(function () {
        Route::get('profile', 'profile');
        Route::post('change-password', 'changePassword');
        Route::post('logout', 'logout');
    });
});
