<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\User;
use App\Post;
use App\PostImage;
use App\Animal;

class LikeTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();

        //投稿をリレーションデータを含めて生成
        $this->post = factory(Post::class)->create([
                    'user_id' => $this->user->id,
                    ])
                    ->each(function ($post) {
                        //画像データの生成
                        $post->postImages()
                            ->save(
                                factory(PostImage::class)->make()
                            );
                        //動物カテゴリーデータの生成
                        $post->postCategorys()
                            ->createMany(
                                factory(Animal::class, 3)->make()
                                ->toArray()
                            );
                    });
    }

    //トップページでいいねテスト
    public function testLike()
    {
        $like_button = '.like_button';
        $like_icon = '.like_icon';
        $like_now_icon = '.like_now_icon';

        $this->browse(function ($frist, $second) use ($like_button, $like_icon, $like_now_icon) {

            //いいねして、アイコンが切り替わっているか確認
            //フラッシュメッセージが表示されているか確認
            $frist->loginAs($this->user)
                    ->visit('/')
                    ->click($like_button)
                    ->waitFor($like_now_icon)
                    ->assertPresent($like_now_icon)

                    //アイコン横のカウントが1になっていることを確認
                    ->assertSourceHas('<span class="align-self-end post_count">1</span>')
                    ->assertSee('投稿にいいねしました'); //toastrのフラッシュメッセージが表示されているか確認

            //いいねを外して、アイコンが切り替わっているか確認
            //フラッシュメッセージが表示されているか確認
            $second->loginAs($this->user)
                    ->visit('/')
                    ->click($like_button)
                    ->waitFor($like_icon)
                    ->assertPresent($like_icon)

                    //アイコン横のカウントが0になっていることを確認
                    ->assertSourceHas('<span class="align-self-end post_count">0</span>')
                    ->assertSee('投稿のいいねを外しました') //toastrのフラッシュメッセージが表示されているか確認
                    ->screenshot('like');
        });
    }

    //投稿詳細ページでいいねテスト
    public function testPostShowPageLike()
    {
        $like_button = '.like_button';
        $like_icon = '.like_icon';
        $like_now_icon = '.like_now_icon';

        $this->browse(function ($frist, $second) use ($like_button, $like_icon, $like_now_icon) {

            //いいねして、アイコンが切り替わっているか確認
            //フラッシュメッセージが表示されているか確認
            $frist->loginAs($this->user)
                    ->visitRoute('posts.show', $this->post)
                    ->click($like_button)
                    ->waitFor($like_now_icon)
                    ->assertPresent($like_now_icon)

                    //アイコン横のカウントが1になっていることを確認
                    ->assertSourceHas('<span class="align-self-end post_count">1</span>')

                    //ナビゲーションタブのいいねカウントが1になっていることを確認
                    ->assertSourceHas('<span class="badge badge-white badge-pill p_count_badge">1</span>')
                    ->assertSee('投稿にいいねしました'); //toastrのフラッシュメッセージが表示されているか確認

            //いいねを外して、アイコンが切り替わっているか確認
            //フラッシュメッセージが表示されているか確認
            $second->loginAs($this->user)
                    ->visitRoute('posts.show', $this->post)
                    ->click($like_button)
                    ->waitFor($like_icon)
                    ->assertPresent($like_icon)

                    //アイコン横のカウントが0になっていることを確認
                    ->assertSourceHas('<span class="align-self-end post_count">0</span>')

                    //ナビゲーションタブのいいねカウントが0になっていることを確認
                    ->assertSourceHas('<span class="badge badge-white badge-pill p_count_badge">0</span>')
                    ->assertSee('投稿のいいねを外しました'); //toastrのフラッシュメッセージが表示されているか確認
        });
    }

    //投稿詳細ページでいいねテスト
    public function testUserPageLikeCount()
    {
        $like_button = '.like_button';
        $like_icon = '.like_icon';
        $like_now_icon = '.like_now_icon';

        //fristはユーザー詳細ページ
        $this->browse(function ($frist, $second) use ($like_button, $like_icon, $like_now_icon) {

            //いいねアイコン横のいいねカウント確認
            //ナビゲーションタブのカウント確認
            $frist->loginAs($this->user)
                    ->visitRoute('users.show', $this->user->id)

                    //いいねする
                    ->click($like_button)
                    ->waitFor($like_now_icon)

                    //アイコン横のカウントが1になっていることを確認
                    ->assertSourceHas('<span class="align-self-end post_count">1</span>')

                    //ナビゲーションタブのいいねカウントが1になっていることを確認
                    ->assertSourceHas('<span class="badge badge-white badge-pill u_count_badge">1</span>')
                    ->screenshot('like');

            //いいねアイコン横のいいねカウント確認
            //ナビゲーションタブのカウント確認
            //いいね外す
            $frist->click($like_button)
                    ->waitFor($like_icon)

                    //アイコン横のカウントが0になっていることを確認
                    ->assertSourceHas('<span class="align-self-end post_count">0</span>')

                    //ナビゲーションタブのいいねカウントが0になっていることを確認
                    ->assertSourceHas('<span class="badge badge-white badge-pill u_count_badge">0</span>')
                    ->screenshot('like')
                    ->click($like_button);

            //secondは、ユーザーいいね投稿ページ
            //いいねアイコン横のいいねカウント確認
            //ナビゲーションタブのカウント確認
            $second->loginAs($this->user)
                    ->visitRoute('users.likes', $this->user->id)

                    //いいねを外す
                    ->click($like_button)
                    ->waitFor($like_icon)

                    //アイコン横のカウントが0になっていることを確認
                    ->assertSourceHas('<span class="align-self-end post_count">0</span>')

                    //ナビゲーションタブのいいねカウントが0になっていることを確認
                    ->assertSourceHas('<span class="badge badge-white badge-pill u_count_badge">0</span>')
                    ->screenshot('like');

            //いいねアイコン横のいいねカウント確認
            //ナビゲーションタブのカウント確認
            //いいねする
            $second->click($like_button)
                    ->waitFor($like_now_icon)

                    //アイコン横のカウントが1になっていることを確認
                    ->assertSourceHas('<span class="align-self-end post_count">1</span>')

                    //ナビゲーションタブのいいねカウントが1になっていることを確認
                    ->assertSourceHas('<span class="badge badge-white badge-pill u_count_badge">1</span>')
                    ->screenshot('like');
        });
    }
}