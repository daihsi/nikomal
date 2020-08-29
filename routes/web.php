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
//トップページ(新規投稿順)
Route::get('/', 'PostsController@index');

//認証関係
Auth::routes();

//登録ユーザー詳細ページのフォロー・フォロワー・いいね投稿ページ
Route::group(['prefix' => 'users/{id}'], function() {
    Route::get('following', 'UsersController@followings')->name('users.followings');
    Route::get('follower', 'UsersController@followers')->name('users.followers');
    Route::get('like', 'UsersController@likes')->name('users.likes');
});

//登録ユーザー詳細ページ
Route::resource('users', 'UsersController', ['only' => ['index', 'show']]);

//認証ユーザーのみ
Route::group(['middleware' => ['auth']], function() {

    //フォロー・アンフォロー
    Route::group(['prefix' => 'users/{id}'], function() {
        Route::post('follow', 'UserFollowController@store')->name('user.follow');
        Route::delete('unfollow', 'UserFollowController@destroy')->name('user.unfollow');
    });

    //ユーザー編集
    Route::resource('users', 'UsersController', ['only' => ['edit', 'update']]);

    //いいね登録
    Route::group(['prefix' => 'posts/{id}'], function() {
        Route::post('like', 'LikesController@store')->name('posts.like');
        Route::delete('unlike', 'LikesController@destroy')->name('posts.unlike');
    });

    //いいねが多い順の投稿一覧
    Route::group(['prefix' => 'posts/'], function() {
        Route::get('popular', 'PostsPopularController@index')->name('posts.popular');
    });

    //新規投稿・投稿編集
    Route::resource('posts', 'PostsController', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);

    //コメント投稿・削除
    Route::post('/comments', 'CommentsController@store')->name('posts.comment');
    Route::delete('comments/{id}', 'CommentsController@destroy')->name('posts.uncomment');
});

//投稿詳細ページ
Route::resource('posts', 'PostsController', ['only' => 'show']);
