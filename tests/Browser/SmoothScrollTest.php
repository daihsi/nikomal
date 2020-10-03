<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\User;
use App\Post;
use App\PostImage;
use App\Animal;

class SmoothScrollTest extends DuskTestCase
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

        $this->users = factory(User::class, 36)->create();

        //投稿をリレーションデータを含めて生成
        $this->posts = factory(Post::class, 36)->create([
                    'user_id' => $this->users[0]->id,
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

    //トップページのページトップへ戻るボタンテスト
    public function testSmoothScrollToppage(): void
    {
        $this->browse(function (Browser $browser) {

            //一ページ目最下位にスクロール移動
            $browser->visit('/')
                    ->driver
                    ->executeScript('window.scrollTo(0, 1200);');

            //二ページ目最下位にスクロール移動
            //ボタンの存在を確認
            $browser->pause(1000)
                    ->assertVisible('#page_top_button')
                    ->press('もっと見る')
                    ->pause(1000)
                    ->driver
                    ->executeScript('window.scrollTo(0, 2400);');

            //三ページ目最下位にスクロール移動
            //ボタンの存在を確認
            $browser->pause(1000)
                    ->assertVisible('#page_top_button')
                    ->press('もっと見る')
                    ->pause(1000)
                    ->driver
                    ->executeScript('window.scrollTo(0, 3600);');

            //ボタンクリックしてページトップへ移動していることを確認
            $browser->pause(1000)
                    ->assertVisible('#page_top_button')
                    ->click('#page_top_button')
                    ->pause(1000)
                    ->screenshot('smootyscroll');
        });
    }

    //ユーザー一覧ページのトップページへ戻るボタンテスト
    public function testSmoothScrollUsers(): void
    {
        $this->browse(function (Browser $browser) {

            //一ページ目最下位にスクロール移動
            $browser->visitRoute('users.index')
                    ->press('もっと見る')
                    ->pause(1000)
                    ->driver
                    ->executeScript('window.scrollTo(0, 1000);');

            //二ページ目最下位にスクロール移動
            //ボタンの存在を確認
            $browser->pause(1000)
                    ->assertVisible('#page_top_button')
                    ->press('もっと見る')
                    ->pause(1000)
                    ->driver
                    ->executeScript('window.scrollTo(0, 2000);');

            //三ページ目最下位にスクロール移動
            //ボタンの存在を確認
            //ボタンクリックしてページトップへ移動していることを確認
            $browser->pause(1000)
                    ->assertVisible('#page_top_button')
                    ->click('#page_top_button')
                    ->pause(1000)
                    ->screenshot('smootyscroll');
        });
    }
}