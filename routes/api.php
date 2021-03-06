<?php
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

//UserAuthController::get();

//Route::post('/register', 'UserAuthController@register');
//Route::post('/register', 'App\Http\Controllers\Auth\UserAuthController@register')->name('user.register');
//Route::post('/login', 'App\Http\Controllers\Auth\UserAuthController@login')->name('user.login');
Route::post('/register', 'AuthController@register')->name('user.register');
Route::post('/login', 'AuthController@login')->name('user.login');

//Route::get('/email/verify', function () {
//    return view('auth.verify-email');
//})->middleware('auth')->name('verification.notice');

//Route::group(['middleware' => ['auth']], function() {
Route::group(['middleware' => ['auth:api']], function() {
    /**
     * Verification Routes
     */
//    Route::get('/email/verify', function () {
//        return view('emails.emailVerificationEmail');
//    })->middleware('auth')->name('verification.email-ver-notice');

    Route::get('/email/verify', 'VerificationController@show')->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', 'VerificationController@verify')->name('verification.verify')->middleware(['signed']);
    Route::post('/email/resend', 'VerificationController@resend')->name('verification.resend');

//    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//        $request->fulfill();
//
//        return redirect('/home');
//    })->middleware(['auth', 'signed'])->name('verification.verify');


    //only verified account can access with this group
    Route::group(['middleware' => ['verified']], function() {
        /**
         * Dashboard Routes
         */
        Route::get('/dashboard', 'DashboardController@index')->name('dashboard.index');
    });
});

////only authenticated can access this group
//Route::group(['middleware' => ['auth']], function() {
//    //only verified account can access with this group
//    Route::group(['middleware' => ['verified']], function() {
//        /**
//         * Dashboard Routes
//         */
//        Route::get('/dashboard', 'DashboardController@index')->name('dashboard.index');
//    });
//});


//Route::post('/login',[App\Http\Controllers\Auth\UserAuthController::class,'login']);
//Route::resource('/user',AuthController::class)->middleware('auth');

//Route::post('users/register', [AuthController::class, 'register'])->name('users.register');
//Route::post('users/login', [AuthController::class, 'login'])->name('users.login');

Route::group(['middleware' => ['auth:api']], function () {
    Route::group(['prefix' => 'users'], function () {
//        Route::get('profile', [UserController::class, 'profile'])->name('users.get-profile');
        Route::get('profile', 'UserController@profile')->name('users.get-profile');
//        Route::get('profile', 'App\Http\Controllers\UserController@profile')->name('users.get-profile');
        Route::patch('profile', 'UserController@update')->name('users.update-profile');
    });

    // reservations
    Route::group(['prefix' => 'reservations'], function () {
        Route::get('/', 'ReservationController@index')->name('reservations.index');
        Route::get('/{reservation}', 'ReservationController@show')->name('reservations.show');
        Route::post('/', 'ReservationController@store')->name('reservations.store');
        Route::post('/{reservation}/cancel', 'ReservationController@cancel')->name('reservations.cancel');
    });
});

// subscription plans
Route::group(['prefix' => 'subscription-plans'], function () {
    Route::get('/', 'SubscriptionPlanController@index')->name('subscription-plans.index');
    Route::get('{subscriptionPlan}', 'SubscriptionPlanController@show')->name('subscription-plans.show');
});


Route::group(['middleware' => ['auth:api', 'admin.access']], function () {
//Route::group(['middleware' => ['auth:api']], function () {
    Route::group(['prefix' => 'admin'], function () {
        // users
        Route::group(['prefix' => 'users'], function () {
            Route::get('/', 'UserController@index')->name('users.index');
            Route::get('{user}', 'Admin\AdminUserController@show')->name('admin.users.show');

//        Route::get('profile', 'App\Http\Controllers\UserController@profile')->name('user.get-profile');

//        Route::post('users/password', 'UserController@updatePassword')->name('user.update-password');
//        Route::delete('users/delete', 'UserController@deleteUser')->name('user.destroy');
//        Route::get('users/permissions', 'UserController@getPermissions')->name('user.get-permissions');
        });
        // gym classes
        Route::group(['prefix' => 'gym-classes'], function () {
            Route::get('/', 'Admin\GymClassController@index')->name('admin.gym-classes.index');
            Route::post('/', 'Admin\GymClassController@store')->name('admin.gym-classes.store');
            Route::get('{gymClass}', 'Admin\GymClassController@show')->name('admin.gym-classes.show');
        });
        // subscription plans
        Route::group(['prefix' => 'subscription-plans'], function () {
            Route::get('/', 'Admin\AdminSubscriptionPlanController@index')->name('admin.subscription-plans.index');
            Route::post('/', 'Admin\AdminSubscriptionPlanController@store')->name('admin.subscription-plans.store');
            Route::patch('{subscriptionPlan}', 'Admin\AdminSubscriptionPlanController@update')->name('admin.subscription-plans.update');
            Route::get('{subscriptionPlan}', 'Admin\AdminSubscriptionPlanController@show')->name('admin.subscription-plans.show');
            Route::delete('{subscriptionPlan}', 'Admin\AdminSubscriptionPlanController@delete')->name('admin.subscription-plans.delete');
        });
        // subscriptions
        Route::group(['prefix' => 'subscriptions'], function () {
            Route::get('/', 'Admin\SubscriptionController@index')->name('admin.subscriptions.index');
            Route::post('/', 'Admin\SubscriptionController@store')->name('admin.subscriptions.store');
            Route::patch('{subscription}', 'Admin\SubscriptionController@update')->name('admin.subscriptions.update');
            Route::get('{subscription}', 'Admin\SubscriptionController@show')->name('admin.subscriptions.show');
            Route::delete('{subscription}', 'Admin\SubscriptionController@delete')->name('admin.subscriptions.delete');
        });
        // reservations
        Route::group(['prefix' => 'reservations'], function () {
            Route::get('/', 'Admin\AdminReservationController@index')->name('admin.reservations.index');
            Route::post('/', 'Admin\AdminReservationController@store')->name('admin.reservations.store');
            Route::patch('{reservation}', 'Admin\AdminReservationController@update')->name('admin.reservations.update');
            Route::get('{reservation}', 'Admin\AdminReservationController@show')->name('admin.reservations.show');
            Route::post('/{reservation}/decline', 'Admin\AdminReservationController@decline')->name('admin.reservations.decline');
            Route::delete('{reservation}', 'Admin\AdminReservationController@delete')->name('admin.reservations.delete');
        });
    });
});

//Route::apiResource('/employee', 'EmployeeController')->middleware('auth:api');
