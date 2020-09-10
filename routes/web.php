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

//投稿詳細ページ(いいねユーザー一覧)
Route::get('posts/{id}/likes', 'PostsController@likes')->name('post.likes');

//投稿検索表示ページ
Route::get('posts/search', 'PostsSearchController@index')->name('posts.search');

//投稿カテゴリーリンクルーティング
Route::get('categorys/{id}', 'PostsSearchController@categorys')->name('posts.categorys');

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
    Route::post('users/{id}/follow', 'UserFollowController@store')->name('user.follow');

    //ユーザー編集
    Route::resource('users', 'UsersController', ['only' => ['edit', 'update']]);

    //いいね登録・解除(非同期通信)
    Route::post('posts/{id}/like', 'LikesController@store')->name('posts.like');

    //いいねが多い順の投稿一覧
    Route::get('posts/popular', 'PostsPopularController@index')->name('posts.popular');

    //新規投稿・投稿編集
    Route::resource('posts', 'PostsController', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);

    //コメント投稿・削除
    Route::post('comments', 'CommentsController@store')->name('posts.comment');
    Route::delete('comments/{id}', 'CommentsController@destroy')->name('posts.uncomment');
});

//投稿詳細ページ(コメント一覧)
Route::resource('posts', 'PostsController', ['only' => 'show']);
