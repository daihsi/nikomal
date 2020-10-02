<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\User;
use App\Post;
use App\PostImage;
use App\Animal;

class PostPopularTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * A Dusk test example.
     *
     * @return void
     */

    //いいねランキングページがいいね順で並んでいるかテスト
    public function testPostPouplar(): void
    {
        $users = factory(User::class, 3)->create();
        $posts = factory(Post::class, 3)->create([
                    'user_id' => $users[0]->id,
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

        $this->browse(function ($browser, $first, $second, $third) use ($users, $posts) {

            //ユーザー１で全投稿をいいねする
            $first->loginAs($users[0])
                    ->visit('/');
            foreach ($posts as $post) {
                $first->click('.like_icon')
                    ->pause(500);
            }

            //ユーザー２で投稿２ついいねする
            $second->loginAs($users[1])
                    ->visit('/');
            foreach ($posts as $post) {
                if ($post->id == 1) {
                    continue;
                }
                else {
                    $second->click('.like_icon')
                        ->pause(500);
                }
            }

            //ユーザー３で投稿１ついいねする
            $third->loginAs($users[2])
                ->visit('/');
            foreach ($posts as $post) {
                if ($post->id == 1 || $post->id == 2) {
                    continue;
                }
                else {
                    $third->click('.like_icon');
                }
            }
            $third->click('.posts_sort')
                ->pause(500)
                ->assertRouteIs('posts.popular')

                //いいね件数順で投稿が並んでいるかブラウザで確認
                ->screenshot('popular');
        });
    }
}
