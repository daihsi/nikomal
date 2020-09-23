<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Post;
use App\User;
use App\PostImage;
use App\Animal;

class PostEditTest extends DuskTestCase
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
        $this->post = factory(Post::class, 1)->create([
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
        $this->edit_post = factory(Post::class)->make();
        $this->edit_image = factory(PostImage::class)->make();
    }

    //投稿編集テスト
    public function testPostEdit()
    {
        $post = $this->post[0];
        $edit_animals = factory(Animal::class, 3)->make();

        //投稿編集ページで既存のデータ値がフォームに入っているか確認
        $this->browse(function ($browser) use ($post, $edit_animals) {
            $browser->loginAs($this->user)
                    ->visitRoute('posts.edit', $post->id)
                    ->assertInputValue('content', $post->content);
            foreach ($post->postImages as $postImage) {
                $browser->assertSourceHas($postImage->image);
            }
            foreach ($post->postCategorys as $postCategory) {
                $browser->assertSelected('animals_name[]', $postCategory->name);
            }

            //セレクトボックスのクリアボタンを選択分クリックしクリア
            for ($i = 1; $i <= 3; $i++) {
                $browser->click('.select2-selection__choice__remove');
            }

            //編集データをフォームに入れ確認
            $browser->type('content', $post->content)
                    ->click('#edit_post_image_preview')
                    ->attach('image', $this->edit_image->image);
            foreach ($edit_animals as $edit_animal) {
                $browser->select('animals_name[]', $edit_animal->name)
                        ->assertSelected('animals_name[]', $edit_animal->name);
            }

            $browser->assertInputValue('content', $post->content)
                    ->assertSourceHas('image/')
                    ->screenshot('post');
        });
    }

    //投稿編集バリデーションエラー表示テスト
    public function testValidationPostEdit()
    {
        $post = $this->post[0];
        $edit_animals = factory(Animal::class, 4)->make();

        $this->browse(function ($browser) use ($post, $edit_animals) {
            $browser->loginAs($this->user)
                    ->visitRoute('posts.edit', $post->id);

            //セレクトボックスのクリアボタンを選択分クリックしクリア
            for ($i = 1; $i <= 3; $i++) {
                $browser->click('.select2-selection__choice__remove');
            }

            foreach ($edit_animals as $edit_animal) {
                $browser->select('animals_name[]', $edit_animal->name)
                        ->assertSelected('animals_name[]', $edit_animal->name);
            }

            //失敗フラッシュメッセージが表示されているか
            //バリデーションエラーメッセージが表示されているか確認
            $browser->click('#content')
                    ->press('変更内容を保存する')
                    ->assertSee('投稿編集に失敗しました')
                    ->assertPresent('.is-invalid')
                    ->assertPresent('.invalid-feedback')
                    ->screenshot('post');
        });
    }
}
