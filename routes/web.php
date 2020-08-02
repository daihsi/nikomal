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
Route::group(['prefix' => 'users/{id}'], function() {
    Route::get('following', 'UsersController@followings')->name('users.followings');
    Route::get('follower', 'UsersController@followers')->name('users.followers');
    Route::get('like', 'UsersController@likes')->name('users.likes');
});
Route::resource('users', 'UsersController', ['only' => ['index', 'show']]);

Route::group(['middleware' => ['auth']], function() {
    Route::group(['prefix' => 'users/{id}'], function() {
        Route::post('follow', 'UserFollowController@store')->name('user.follow');
        Route::delete('unfollow', 'UserFollowController@destroy')->name('user.unfollow');
    });
    Route::resource('users', 'UsersController', ['only' => ['edit', 'update']]);

    Route::group(['prefix' => 'posts/{id}'], function() {
        Route::post('like', 'LikesController@store')->name('posts.like');
        Route::delete('unlike', 'LikesController@destroy')->name('posts.unlike');
    });
    Route::resource('posts', 'PostsController', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
});
Route::resource('posts', 'PostsController', ['only' => 'show']);
