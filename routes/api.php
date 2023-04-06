<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\CarServicingJobController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\GarageController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\UserController;
use App\Models\CarServicingJob;
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


// Guest User:

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::get('verify-email/{email_verification_code}', 'verifyEmail');
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
    Route::get('list/{id}', 'list');
    Route::post('create', 'create');
    Route::post('update/{id}', 'update');
    Route::post('delete/{id}', 'delete');
    Route::get('show', 'show');
});

// City :

Route::controller(CityController::class)->prefix('city')->group(function () {
    Route::get('list/{id}', 'list');
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

    // User Profile:

    Route::controller(UserController::class)->prefix('user')->group(function () {
        Route::get('profile', 'profile');
        Route::get('list-of-garage', 'list');
        Route::post('change-password', 'changePassword');
        Route::post('logout', 'logout');
    });

    // Car :
    Route::controller(CarController::class)->prefix('car')->group(function () {
        Route::get('list', 'list');
        Route::post('create', 'create');
        Route::post('update/{id}', 'update');
        Route::post('delete/{id}', 'delete');
        Route::get('show', 'show');
    });

    // Garage :
    Route::middleware(['hasAccess'])->group(function () {
        Route::controller(GarageController::class)->prefix('garage')->group(function () {
            Route::get('list', 'list');
            Route::post('create', 'create');
            Route::post('update/{id}', 'update');
            Route::post('delete/{id}', 'delete');
            Route::get('show', 'show');
        });

        Route::controller(CarServicingJobController::class)->prefix('car-servicing-job')->group(function(){
            Route::get('list','list');
            Route::post('assign','assign');
            Route::post('update','update');
            Route::get('delete/{id}','delete');
            Route::get('show/{id}','id');
        });
    });
});
