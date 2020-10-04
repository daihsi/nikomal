<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Post;
use App\User;
use App\PostImage;
use App\Animal;

class PostTest extends DuskTestCase
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
        $this->content = factory(Post::class)->make([
                        'user_id' => $this->user->id,
                    ]);
    }

    //新規投稿テスト
    public function testNewPostCreate(): void
    {
        $image = factory(PostImage::class)->make();
        $array_animals = array_column(factory(Animal::class, 3)->make()->toArray(), 'name');
        $animals = array_unique($array_animals);

        //フォームに値を入れてそれらが入っているか確認
        $this->browse(function ($browser) use ($image, $animals) {
            $browser->loginAs($this->user)
                    ->visitRoute('posts.create')
                    ->type('content', $this->content->content)
                    ->click('#post_image_preview')
                    ->attach('image', $image->image);
            foreach ($animals as $index => $name) {
                $browser->select('animals_name[]', $name)
                        ->assertSelected('animals_name[]', $name);
            }
            $browser->assertInputValue('content', $this->content->content)
                    ->assertSourceHas('image/');
        });
    }

    //新規投稿バリデーションエラー表示テスト
    public function testValidationPostCreate(): void
    {
        $array_animals = array_column(factory(Animal::class, 10)->make()->toArray(), 'name');
        $animals = array_unique($array_animals);

        $this->browse(function ($browser) use ($animals) {
            $browser->loginAs($this->user)
                    ->visitRoute('posts.create')
                    ->type('content', $this->content->content);
            foreach ($animals as $index => $name) {
                $browser->select('animals_name[]', $name)
                        ->assertSelected('animals_name[]', $name);
            }

            //失敗フラッシュメッセージが表示されているか
            //バリデーションエラーメッセージが表示されているか確認
            $browser->press('投稿する')
                    ->assertSee('投稿に失敗しました')
                    ->assertPresent('.is-invalid')
                    ->assertPresent('.invalid-feedback');
        });
    }
}
