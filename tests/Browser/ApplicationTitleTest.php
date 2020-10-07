<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\User;
use App\Post;

class ApplicationTitleTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * 各ページにアクセスしてブラウザタブのページ概要が変化しているかテスト
     *
     * @return void
     */
    public function testApplicationTitle(): void
    {
        
        $title = 'nikomal | ';
        $user = factory(User::class)->create();
        $post = factory(Post::class)->create([
                    'user_id' => $user->id,
                ]);
        $this->browse(function ($browser) use($title, $user, $post) {

                    //ログイン前トップページ
            $browser->visit('/')
                    ->assertTitle($title. 'トップページ')

                    //ログインページ
                    ->visitRoute('login')
                    ->assertTitle($title. 'ログイン')

                    //ユーザー登録ページ
                    ->visitRoute('register')
                    ->assertTitle($title. 'ユーザー登録')

                    //ログイン後トップページ
                    ->loginAs($user)
                    ->visit('/')
                    ->assertTitle($title. '新規投稿一覧')

                    //プロフィール編集ページ
                    ->visitRoute('users.edit', $user->id)
                    ->assertTitle($title. 'プロフィール編集')

                    //フォロワー一覧ページ
                    ->visitRoute('users.followers', $user->id)
                    ->assertTitle($title. 'フォロワー一覧')

                    //フォロー一覧ページ
                    ->visitRoute('users.followings', $user->id)
                    ->assertTitle($title. 'フォロー一覧')

                    //ユーザー一覧ページ
                    ->visitRoute('users.index')
                    ->assertTitle($title. 'ユーザー一覧')

                    //いいね投稿一覧ページ
                    ->visitRoute('users.likes', $user->id)
                    ->assertTitle($title. 'いいね投稿一覧')

                    //ユーザー詳細ページ
                    ->visitRoute('users.show', $user->id)
                    ->assertTitle($title. 'マイページ')

                    //新規投稿ページ
                    ->visitRoute('posts.create')
                    ->assertTitle($title. '新規投稿')

                    //投稿編集ページ
                    ->visitRoute('posts.edit', $post->id)
                    ->assertTitle($title. '投稿編集')

                    //投稿いいねユーザー一覧ページ
                    ->visitRoute('post.likes', $post->id)
                    ->assertTitle($title. 'いいねユーザー一覧')

                    //いいねランキングページ
                    ->visitRoute('posts.popular')
                    ->assertTitle($title. 'いいねが多い順投稿一覧')

                    //投稿検索ページ
                    ->visitRoute('posts.search')
                    ->assertTitle($title. '投稿検索')

                    //投稿詳細ページ
                    ->visitRoute('posts.show', $post->id)
                    ->assertTitle($title. '投稿詳細')

                    //パスワード再設定ページ
                    ->visitRoute('password.request')
                    ->assertTitle($title. 'パスワード再設定')

                    //メールアドレス再設定ページ
                    ->visitRoute('email.request')
                    ->assertTitle($title. 'メールアドレス再設定');
        });
    }
}
