<?php

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

Route::post('users/register', 'AuthController@register')->name('users.register');
Route::post('users/login', 'AuthController@login')->name('users.login');
Route::get('email/verify/{id}/{hash}', 'AuthController@verifyEmail')->name('users.verify');
Route::post('users/forgot-password', 'AuthController@sendResetPasswordLinkEmail')->middleware('guest')->name('users.forgot-password');
Route::post('users/reset-password', 'AuthController@resetPassword')->middleware('guest')->name('users.reset-password');

// week days
Route::group(['prefix' => 'calendar'], function () {
    Route::get('/week', 'WeekDayController@weekCalendar')->name('week-days.week-calendar');
});

Route::group(['middleware' => ['auth:api']], function () {
    Route::group(['prefix' => 'users'], function () {
        Route::get('profile', 'UserController@profile')->name('users.get-profile');
        Route::patch('profile', 'UserController@update')->name('users.update-profile');
        Route::patch('password', 'UserController@updatePassword')->name('users.update-password');
        Route::patch('update-email', 'UserController@updateEmail')->name('users.update-email');
        Route::post('logout', 'AuthController@logout')->name('users.logout');
    });

    // reservations
    Route::group(['prefix' => 'reservations'], function () {
        Route::get('/', 'ReservationController@index')->name('reservations.index');
        Route::get('/{reservation}', 'ReservationController@show')->name('reservations.show');
        Route::post('/', 'ReservationController@store')->name('reservations.store');
        Route::post('/{reservation}/cancel', 'ReservationController@cancel')->name('reservations.cancel');
    });

    // week days
    Route::group(['prefix' => 'calendar'], function () {
        Route::get('/', 'WeekDayController@studentCalendar')->name('week-days.student-calendar');
    });

    // emails
    Route::group(['prefix' => 'email'], function () {
        Route::post('/resend-verification-email', 'AuthController@resendVerificationEmail')->name('users.resend-verification-email');
    });
});

// subscription plans
Route::group(['prefix' => 'subscription-plans'], function () {
    Route::get('/', 'SubscriptionPlanController@index')->name('subscription-plans.index');
    Route::get('{subscriptionPlan}', 'SubscriptionPlanController@show')->name('subscription-plans.show');
});

Route::group(['middleware' => ['auth:api', 'admin.access']], function () {
    Route::group(['prefix' => 'admin'], function () {
        // users
        Route::group(['prefix' => 'users'], function () {
            Route::get('/', 'UserController@index')->name('users.index');
            Route::get('{user}', 'Admin\AdminUserController@show')->name('admin.users.show');
        });
        // gym classes
        Route::group(['prefix' => 'gym-classes'], function () {
            Route::get('/', 'Admin\GymClassController@index')->name('admin.gym-classes.index');
            Route::post('/', 'Admin\GymClassController@store')->name('admin.gym-classes.store');
            Route::patch('{gymClass}', 'Admin\GymClassController@update')->name('admin.gym-classes.update');
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
        // week days
        Route::group(['prefix' => 'calendar'], function () {
            Route::get('/', 'Admin\AdminWeekDayController@adminCalendar')->name('admin.calendar.adminCalendar');
        });
        // excluded calendar dates
        Route::group(['prefix' => 'excluded-calendar-dates'], function () {
            Route::get('/', 'Admin\ExcludedCalendarDateController@index')->name('admin.excluded-calendar-dates.index');
            Route::post('/', 'Admin\ExcludedCalendarDateController@store')->name('admin.excluded-calendar-dates.store');
            Route::patch('{excludedCalendarDate}', 'Admin\ExcludedCalendarDateController@update')->name('admin.excluded-calendar-dates.update');
            Route::get('{excludedCalendarDate}', 'Admin\ExcludedCalendarDateController@show')->name('admin.excluded-calendar-dates.show');
            Route::delete('{excludedCalendarDate}', 'Admin\ExcludedCalendarDateController@delete')->name('admin.excluded-calendar-dates.delete');
        });
    });
});
