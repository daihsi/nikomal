<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\User;
use App\Post;
use App\PostImage;
use App\Animal;

class SelectTest extends DuskTestCase
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
        $this->posts = factory(Post::class, 24)->create([
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

    //検索フォームのselect2ボックスのテスト
    public function testSearchSelect()
    {
        //連想配列のインデックスを数字に変更
        $keys = array_keys(config('animals.animals1'));

        //動物名を10個取り出す
        $animals_name = array_slice($keys, 0, 10);

        //セレクトボックスから10個選択
        //選択されているか確認
        $this->browse(function ($browser) use ($animals_name) {
            $browser->visitRoute('posts.search');
            foreach ($animals_name as $index => $name) {
                $browser->select('animals_name[]', $animals_name[$index])
                        ->assertSelected('animals_name[]', $animals_name[$index]);
            }

            //10個選択され表示されているかブラウザで確認
            $browser->screenshot('select');
        });
    }

    //新規投稿・投稿編集フォームのselect2ボックスのテスト
    public function testPostSelect()
    {
        //連想配列のインデックスを数字に変更
        $keys = array_keys(config('animals.animals1'));

        //動物名を3個取り出す
        $animals_name = array_slice($keys, 0, 3);
        $post = $this->posts[0];
        $selected = $post->postCategorys;

        //セレクトボックスから3個選択
        //選択されているか確認
        $this->browse(function ($browser) use ($animals_name, $selected, $post) {
            $browser->loginAs($this->user)
                    ->visitRoute('posts.create');
            foreach ($animals_name as $index => $name) {
                $browser->select('animals_name[]', $animals_name[$index])
                        ->assertSelected('animals_name[]', $animals_name[$index]);
            }

            //3個選択され表示されているかブラウザで確認
            $browser->screenshot('select');

            //投稿編集ページで、現在の投稿のカテゴリーが選択済みで表示されているか確認
            $browser->visitRoute('posts.edit', $post->id);
            foreach ($selected as $index => $name) {
                $browser->assertSelected('animals_name[]', $selected[$index]);
            }

            //3個選択され表示されているかブラウザで確認
            $browser->screenshot('select');
        });
    }

    //バリデーションを通過せずリダイレクトした時に
    //セレクトボックスに値が保持されているか確認
    public function testValidationSearchSelect()
    {
        //連想配列のインデックスを数字に変更
        $keys = array_keys(config('animals.animals1'));

        //動物名を11個取り出す
        $animals_name = array_slice($keys, 0, 11);

        //セレクトボックスから11個選択
        $this->browse(function ($browser) use ($animals_name) {
            $browser->visitRoute('posts.search');
            foreach ($animals_name as $index => $name) {
                $browser->select('animals_name[]', $animals_name[$index]);
            }

            //選択個数が10個以上選択したためバリデーションを通過できず
            //エラーメッセージが表示されたか確認
            $browser->press('検索する')
                    ->pause(1000)
                    ->assertRouteIs('posts.search')
                    ->assertSee('検索に失敗しました') //toastrのフラッシュメッセージが表示されているか確認
                    ->assertSee('動物カテゴリーは10個以下で選択してください');

            //セレクトボックスに値が保持されているか確認
            foreach ($animals_name as $index => $name) {
                $browser->assertSelected('animals_name[]', $animals_name[$index]);
            }
        });
    }

    //バリデーションを通過せずリダイレクトした時に
    //セレクトボックスに値が保持されているか確認
    public function testValidationPostSelect()
    {
        //連想配列のインデックスを数字に変更
        $keys = array_keys(config('animals.animals1'));

        //動物名を4個取り出す
        $animals_name = array_slice($keys, 0, 4);

        //セレクトボックスから4個選択
        $this->browse(function ($browser) use ($animals_name) {
            $browser->loginAs($this->user)
                    ->visitRoute('posts.create')
                    ->type('content', $this->posts[0]->content);
            foreach ($animals_name as $index => $name) {
                $browser->select('animals_name[]', $animals_name[$index]);
            }

            //選択個数が3個以上選択したためバリデーションを通過できず
            //エラーメッセージが表示されたか確認
            $browser->press('投稿する')
                    ->pause(1000)
                    ->assertRouteIs('posts.create')
                    ->assertSee('投稿に失敗しました') //toastrのフラッシュメッセージが表示されているか確認
                    ->assertSee('動物カテゴリーは3個以下で選択してください')
                    ->screenshot('select');

            //セレクトボックスに値が保持されているか確認
            foreach ($animals_name as $index => $name) {
                $browser->assertSelected('animals_name[]', $animals_name[$index]);
            }
        });
    }

    //検索が成功して検索ページに戻った際に
    //セレクトボックスに検索した値が保持されているかテスト
    public function testSuccessSearchSelect()
    {
        $post = $this->posts[0];
        $animals_name = $post->postCategorys;

        $this->browse(function ($browser) use ($animals_name) {
            $browser->visitRoute('posts.search');
            foreach ($animals_name as $animal_name) {
                $browser->select('animals_name[]', $animal_name->name);
            }

            $browser->press('検索する')
                    ->pause('1000')
                    ->assertSee('ヒットしました') //toastrのフラッシュメッセージが表示されているか確認
                    ->screenshot('select');

            //セレクトボックスに値が保持されているか確認
            foreach ($animals_name as $animal_name) {
                $browser->assertSelected('animals_name[]', $animal_name->name);
            }
        });
    }
}
