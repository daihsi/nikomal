<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Post;
use App\User;
use App\PostImage;
use App\Animal;

class PostSearchTest extends DuskTestCase
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
        $this->posts = factory(Post::class, 12)->create([
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

    //投稿検索テスト
    public function testPostSearch(): void
    {
        $post10 = $this->posts[11];
        $post9 = $this->posts[10];
        $post8 = $this->posts[9];

        $this->browse(function ($first, $second, $third) use ($post10, $post9, $post8) {

            //キーワードのみ値を入れて検索
            $first->visitRoute('posts.search')
                    ->assertRouteIs('posts.search')
                    ->type('keyword', $post10->content)
                    ->assertInputValue('keyword', $post10->content)
                    ->press('検索する')
                    ->pause(1000)

                    //投稿本文と投稿ユーザー名と検索ヒットフラッシュメッセージが表示されているか確認
                    ->assertSee($post10->content)
                    ->assertSee($this->user->name)
                    ->assertSee('ヒットしました');

            //セレクトボックスのみ値を入れて検索
            $second->visitRoute('posts.search');
            foreach ($post9->postCategorys as $postCategory) {
                $second->select('animals_name[]', $postCategory->name)
                        ->assertSelected('animals_name[]', $postCategory->name);
                break;
            }
            $second->press('検索する')
                    ->pause(1000)

                    //投稿本文と投稿ユーザー名と検索ヒットフラッシュメッセージが表示されているか確認
                    ->assertSee($post9->content)
                    ->assertSee($this->user->name)
                    ->assertSee('ヒットしました');

            //キーワードとセレクトボックス両方値を入れて検索
            $third->visitRoute('posts.search')
                    ->type('keyword', $post8->content)
                    ->assertInputValue('keyword', $post8->content);
            foreach ($post8->postCategorys as $postCategory) {
                $third->select('animals_name[]', $postCategory->name)
                    ->assertSelected('animals_name[]', $postCategory->name);
                break;
            }
            $third->press('検索する')
                    ->pause(1000)

                    //投稿本文と投稿ユーザー名と検索ヒットフラッシュメッセージが表示されているか確認
                    ->assertSee($post8->content)
                    ->assertSee($this->user->name)
                    ->assertSee('ヒットしました');
        });
    }

    //投稿検索バリデーションエラー表示テスト
    public function testValidationPostSearch(): void
    {
        $array_animals = array_column(factory(Animal::class, 25)->make()->toArray(), 'name');
        $animals = array_unique($array_animals);
        
        $this->browse(function (Browser $browser) use ($animals) {
            $browser->visitRoute('posts.search');

            //セレクトボックスは10個まで選択可能だが、それ以上選択したと仮定
            foreach ($animals as $index => $name) {
                $browser->select('animals_name[]', $name);
            }

            //バリデーションエラーメッセージと失敗フラッシュメッセージが表示されているか確認
            $browser->press('検索する')
                    ->assertSee('検索に失敗しました')
                    ->assertPresent('.is-invalid')
                    ->assertPresent('.invalid-feedback');
            });
    }

    //検索データが該当ない時の表示テスト
    public function testNoPostSearch(): void
    {
        $keyword = '該当無し';
        $this->browse(function (Browser $browser) use ($keyword) {
            $browser->visitRoute('posts.search')
                    ->type('keyword', $keyword)
                    ->press('検索する')

                    //該当無しのフラッシュメッセージが表示されているか確認
                    //投稿がないことも確認
                    ->assertSee('該当する投稿がありません')
                    ->assertMissing('.post_item');
        });
    }
}
