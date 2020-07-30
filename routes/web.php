<?php

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

Route::get('/', 'PostsController@index');
Auth::routes();
Route::resource('users', 'UsersController', ['only' => ['index', 'show']]);

Route::group(['middleware' => ['auth']], function() {
    Route::resource('users', 'UsersController', ['only' => ['edit', 'update']]);
    Route::resource('posts', 'PostsController', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
});

Route::resource('posts', 'PostsController', ['only' => 'show']);
