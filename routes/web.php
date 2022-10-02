<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('index');
})->name('index');

Route::get('/admin/calendar', function () {
    return view('index');
})->name('admin.calendar');

Route::get('/{catchall?}', function () {
    return response()->view('index');
})->where('catchall', '(.*)');

//Route::get('/?{name}', function(){
//    return redirect('index');
//})->where('name', '[A-Za-z]+');

//Auth2::routes();
