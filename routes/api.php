    <?php

    use App\Http\Controllers\AuthController;
    use App\Http\Controllers\CarController;
    use App\Http\Controllers\CarServicingController;
    use App\Http\Controllers\CarServicingJobController;
    use App\Http\Controllers\CityController;
    use App\Http\Controllers\CountryController;
    use App\Http\Controllers\GarageController;
    use App\Http\Controllers\MechanicController;
    use App\Http\Controllers\ServiceTypeController;
    use App\Http\Controllers\StateController;
    use App\Http\Controllers\UserController;

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

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('user', function () {
            return auth()->user();
        });
    });


    /******************************************************Guest User:**********************************************************************/

    // Auth :

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
        Route::get('show/{id}', 'show');
    });


    /********************************************************Auth User:**********************************************************************/

    Route::middleware('auth:sanctum')->group(function () {

        // User Profile:

        Route::controller(UserController::class)->prefix('user')->group(function () {
            Route::get('profile', 'profile');
            Route::post('change-password', 'changePassword');
            Route::post('get-history', 'getHistory');
            Route::post('logout', 'logout');
        });

        // Car :

        Route::controller(CarController::class)->prefix('car')->group(function () {
            Route::get('list', 'list');
            Route::post('add-to-service','addToService'); 
            Route::post('create', 'create');
            Route::post('update/{id}', 'update');
            Route::post('delete/{id}', 'delete');
            Route::get('show/{id}', 'show');
        });

        //Service

        Route::controller(CarServicingController::class)->prefix('car-service')->group(function () {
            Route::get('list-of-garage', 'list');
            Route::post('create', 'create');
        });

        /******************************************************Owner:**************************************************************/

        Route::middleware(['hasOwner'])->group(function () {
            // Garage :

            Route::controller(GarageController::class)->prefix('garage')->group(function () {
                Route::get('list', 'list');
                Route::post('create', 'create');
                Route::post('update/{id}', 'update');
                Route::post('delete/{id}', 'delete');
                Route::get('show/{id}', 'show');
            });

            // Car Service Job :

            Route::controller(CarServicingJobController::class)->prefix('car-servicing-job')->group(function () {
                Route::get('list', 'list');
                Route::post('assign', 'assign');
                Route::post('update', 'update');
                Route::get('delete/{id}', 'delete');
                Route::get('show/{id}', 'id');
            });
        });

        /**************************************************Mechanic****************************************************************/

        Route::middleware(['hasMechanic'])->group(function () {
            Route::controller(MechanicController::class)->prefix('mechanic')->group(function () {
                Route::get('profile', 'profile');
                Route::post('update-status', 'updateStatus');
            });
        });
    });
