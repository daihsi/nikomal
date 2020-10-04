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
                                factory(Animal::class, 1)->make()
                                ->toArray()
                            );
                    });
        $this->edit_post = factory(Post::class)->make();
        $this->edit_image = factory(PostImage::class)->make();
    }

    //投稿編集テスト
    public function testPostEdit(): void
    {
        $post = $this->post[0];
        $array_animals = array_column(factory(Animal::class, 3)->make()->toArray(), 'name');
        $edit_animals = array_unique($array_animals);

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
            $browser->click('.select2-selection__choice__remove');

            //編集データをフォームに入れ確認
            $browser->type('content', $post->content)
                    ->click('#edit_post_image_preview')
                    ->attach('image', $this->edit_image->image);
            foreach ($edit_animals as $index => $name) {
                $browser->select('animals_name[]', $name)
                        ->assertSelected('animals_name[]', $name);
            }
            $browser->assertInputValue('content', $post->content)
                    ->assertSourceHas('image/');
        });
    }

    //投稿編集バリデーションエラー表示テスト
    public function testValidationPostEdit(): void
    {
        $post = $this->post[0];
        $array_animals = array_column(factory(Animal::class, 10)->make()->toArray(), 'name');
        $edit_animals = array_unique($array_animals);

        $this->browse(function ($browser) use ($post, $edit_animals) {
            $browser->loginAs($this->user)
                    ->visitRoute('posts.edit', $post->id);

            //セレクトボックスのクリアボタンを選択分クリックしクリア
            $browser->click('.select2-selection__choice__remove');

            foreach ($edit_animals as $index => $name) {
                $browser->select('animals_name[]', $name)
                        ->assertSelected('animals_name[]', $name);
            }

            //失敗フラッシュメッセージが表示されているか
            //バリデーションエラーメッセージが表示されているか確認
            $browser->click('#content')
                    ->press('変更内容を保存する')
                    ->assertSee('投稿編集に失敗しました')
                    ->assertPresent('.is-invalid')
                    ->assertPresent('.invalid-feedback');
        });
    }

    //投稿削除テスト
    public function testPostDelete(): void
    {
        $post = factory(Post::class)->create([
                    'user_id' => $this->user->id,
                ]);
        //削除ボタン、編集ボタンを確認
        //削除しリダイレクト先と成功フラッシュメッセージが表示されているか確認
        $this->browse(function ($browser) use ($post) {
            $browser->loginAs($this->user)
                    ->visitRoute('posts.show', $post->id)
                    ->assertPresent('.post_delete_alert')
                    ->assertPresent('.fa-edit')
                    ->click('.post_delete_alert')
                    ->acceptDialog()
                    ->pause(500)
                    ->assertPathIs('/')
                    ->assertSee('投稿削除しました');
        });
    }

    //管理ユーザーでの他ユーザーの投稿削除テスト
    public function testAdminDeletePost(): void
    {
        $admin = factory(User::class)->create([
                    'email' => 'admin@example.com',
                ]);
        $post = factory(Post::class)->create([
                    'user_id' => $this->user->id,
                ]);
        $this->browse(function ($browser) use ($admin, $post) {

            //管理ユーザーでログイン
            //他ユーザーの投稿詳細にアクセス
            //削除ボタンの表示を確認、編集ボタンがないことを確認
            //削除しリダイレクト先と成功フラッシュメッセージが表示されているか確認
            $browser->loginAs($admin)
                    ->visitRoute('posts.show', $post->id)
                    ->assertPresent('.post_delete_alert')
                    ->assertMissing('.fa-edit')
                    ->click('.post_delete_alert')
                    ->acceptDialog()
                    ->pause(500)
                    ->assertPathIs('/')
                    ->assertSee('投稿削除しました');
        });

        //データベースに削除した投稿が残っていないか確認
        $this->assertDeleted($post);
    }
}
