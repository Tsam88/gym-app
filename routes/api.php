<?php
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\UserController;
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
//
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
});


Route::group(['middleware' => ['auth:api', 'admin.access']], function () {
//Route::group(['middleware' => ['auth:api']], function () {
    Route::group(['prefix' => 'admin'], function () {
        Route::group(['prefix' => 'users'], function () {
            Route::get('/', 'UserController@index')->name('users.index');
            Route::get('{user}', 'Admin\AdminUserController@show')->name('admin.users.show');

//        Route::get('profile', 'App\Http\Controllers\UserController@profile')->name('user.get-profile');

//        Route::post('users/password', 'UserController@updatePassword')->name('user.update-password');
//        Route::delete('users/delete', 'UserController@deleteUser')->name('user.destroy');
//        Route::get('users/permissions', 'UserController@getPermissions')->name('user.get-permissions');
        });

        Route::group(['prefix' => 'gym-classes'], function () {
            Route::get('/', 'Admin\GymClassController@index')->name('admin.gym-classes.index');
            Route::post('/', 'Admin\GymClassController@store')->name('admin.gym-classes.store');
            Route::get('{gymClass}', 'Admin\GymClassController@show')->name('admin.gym-classes.show');
        });

        Route::group(['prefix' => 'subscription-plans'], function () {
            Route::get('/', 'Admin\SubscriptionPlanController@index')->name('admin.subscription-plans.index');
            Route::post('/', 'Admin\SubscriptionPlanController@store')->name('admin.subscription-plans.store');
            Route::patch('{subscriptionPlan}', 'Admin\SubscriptionPlanController@update')->name('admin.subscription-plans.update');
            Route::get('{subscriptionPlan}', 'Admin\SubscriptionPlanController@show')->name('admin.subscription-plans.show');
        });
    });


});

//Route::apiResource('/employee', 'EmployeeController')->middleware('auth:api');
