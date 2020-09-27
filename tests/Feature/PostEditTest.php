<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Post;
use App\User;
use App\PostImage;
use App\Animal;
use App\Http\Requests\PostEditRequest;
use Tests\TestCase;

class PostEditTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function setUp(): void
    {
        parent::setUp();

        //一つの投稿と関係リレーションデータ同時生成
        $this->factory_user = factory(User::class)->create();
        $this->posts = factory(Post::class, 1)->create([
                    'user_id' => $this->factory_user->id,
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

        //仮画像作成。バリデーション用
        Storage::fake('post_images');
        $this->upload_file = UploadedFile::fake()->image('test.jpg')->size(2048);
        $this->upload_file->move('storage/framework/testing/disks/post_images');
        $this->file_name = $this->upload_file->getFilename();

        //動物カテゴリーデータ生成。更新とバリーデーション用
        $this->i = rand(1,6);
        $this->animals_name = array_rand(config('animals.animals'. $this->i), 3);
    }

    //投稿者以外のユーザーが、投稿にアクセスしても編集・削除ボタンが表示されていないかテスト
    public function testPostNoEditButton()
    {
        $factory_userA = factory(User::class)->create();
        $factory_userB = factory(User::class)->create();
        $this->actingAs($factory_userA);
        $post = factory(Post::class)->create([
                    'user_id' => $factory_userB->id,
                ]);
        $this->get(route('posts.show', $post->id))
            ->assertStatus(200)
            ->assertDontSee('<button class="btn btn-outline-success rounded-pill fas fa-edit mt-1">編集</button>')
            ->assertDontSee('<button type="submit" class="btn btn-danger rounded-pill fas fa-trash-alt mt-1 ml-1">削除</button>');
    }

    //投稿者以外が投稿編集ページにアクセスしても、前のページにリダイレクトされるかテスト
    public function testPostEditpageAccessdenied()
    {
        $factory_userA = factory(User::class)->create();
        $factory_userB = factory(User::class)->create();
        $this->actingAs($factory_userA);
        $post = factory(Post::class)->create([
                    'user_id' => $factory_userB->id,
                ]);
        $url = route('posts.show', $post->id);
        $this->from($url)
            ->get(route('posts.edit', $post->id))
            ->assertStatus(302)
            ->assertRedirect($url);
    }

    //ゲストユーザー又は投稿所有者以外が、新規投稿できないようになっているかテスト
    public function testGuestUserPostCreate()
    {
        $user = $this->factory_user;
        $posts = $user->posts;

        //一つだけ投稿を取得
        foreach ($posts as $post) {
            $post = $post;
            break;
        }

        //更新データを作成
        $update_post = factory(Post::class)->make([
                        'user_id' => $user->id
                    ]);
        $data = [
                'content' => $update_post->content,
                'animals_name' => ['テスト:イヌ', 'テスト:ネコ', 'テスト:クマ'],
            ];

        $this->put(route('posts.update', $post->id), $data);

        //postsテーブルにデータが保存されていないか確認
        $this->assertDatabaseMissing('posts', [
            'content' => $data['content']
        ]);

        //animalsテーブルにデータが保存されていないか確認
        foreach($data['animals_name'] as $animal_name) {
            $this->assertDatabaseMissing('animals', [
                'name' => $animal_name,
            ]);
        }
    }

    //投稿編集データがデータベースに更新されているか
    public function testPostEdit()
    {
        $user = $this->factory_user;
        $this->actingAs($user);
        $posts = $user->posts;

        //一つだけ投稿を取得
        foreach ($posts as $post) {
            $post = $post;
            break;
        }

        //更新データを作成
        $update_post = factory(Post::class)->make([
                        'user_id' => $user->id
                    ]);
        $data = [
                'content' => $update_post->content,
                'animals_name' => $this->animals_name,
            ];

        //更新リクエスト後、リダイレクト先の確認
        $this->put(route('posts.update', $post->id), $data)
            ->assertStatus(302)
            ->assertRedirect('/');

        //更新した投稿のリレーション情報を取得
        $animals = $post->postCategorys;

        //postsテーブルにデータが更新保存してあるか確認
        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'content' => $data['content'],
        ]);

        //animalsテーブルにデータが更新保存してあるか確認
        foreach($data['animals_name'] as $animal_name) {
            $this->assertDatabaseHas('animals', [
                'name' => $animal_name,
            ]);
            foreach ($animals as $animal) {
            $animal_ids[] = $animal->id;
            }
        }

        //post_categoryテーブルにデータが更新保存してあるか確認
        foreach($animal_ids as $animal_id){
            $this->assertDatabaseHas('post_category', [
                'animal_id' => $animal_id,
                'post_id' => $post->id,
            ]);
        }
    }

    //投稿が削除されているかテスト
    public function testPostDelete()
    {
        $user = $this->factory_user;
        $this->actingAs($user);
        $posts = $user->posts;

        //一つだけ投稿を取得
        foreach ($posts as $post) {
            $post = $post;
            break;
        }

        //リレーションデータ取得
        $animals = $post->postCategorys;
        $data = [
                'content' => $post->content,
                'user_id' => $user->id,
                'post_id' => $post->id,
                'image' => $post->postImages,
            ];

        //削除リクエスト後、リダイレクト先の確認
        $this->delete(route('posts.destroy', $post->id))
            ->assertStatus(302)
            ->assertRedirect('/');

        //postsテーブルのデータが削除されたか確認
        $this->assertDeleted('posts', [
                'content' => $data['content'],
                'user_id' => $data['user_id'],
            ]);

        //post_imagesテーブルのデータが削除されたか確認
        $this->assertDeleted('post_images', [
                'image' => $data['image'],
            ]);

        //post_categoryテーブルのデータが削除されたか確認
        foreach ($animals as $animal) {
            $this->assertDeleted('post_category', [
                'animal_id' => $animal->id,
                'post_id' => $data['post_id'],
            ]);
        }
    }

    //必須項目が空でリクエストされた場合のバリデーションテスト
    public function testPostEditRequestNull()
    {
        //キャプショと動物カテゴリーが空でリクエストされたと仮定
        $data = [
                'content' => null,
                'animals_name' => null,
                'image' => $this->upload_file,
            ];

        $request = new PostEditRequest;
        $rules = $request->rules();

        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();

        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'content' => ['Required' => [],],
            'animals_name' => ['Required' => [],],
        ];

        //どこがエラーになったか検証
        $this->assertEquals($expectedFailed, $validator->failed());
    }

    //contentの桁あふれの際のバリデーションテスト
    public function testtPostEditRequestOverFlow()
    {
        //contentが1文字多いと仮定
        $data = [
            'content' => str_repeat('あ', 151),
            'image' => $this->upload_file,
            'animals_name' => $this->animals_name,
        ];
        $request = new PostEditRequest;
        $rules = $request->rules();

        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();

        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'content' => ['Max' => [150],],
        ];

        //どこがエラーになったか検証
        $this->assertEquals($expectedFailed, $validator->failed());
    }

    //画像フォーマット、サイズの期待値外でのリクエストのバリデーションテスト
    public function testtPostEditRequestFormat()
    {
        Storage::fake('post_images');
        $upload_file = UploadedFile::fake()->image('test.gif')->size(2049);
        $upload_file->move('storage/framework/testing/disks/post_images');
        $file_name = $upload_file->getFilename();

        //画像のフォーマット、サイズを期待値外の値がリクエストされたと仮定
        $data = [
            'content' => str_repeat('あ', 150),
            'image' => $upload_file,
            'animals_name' => $this->animals_name,
        ];
        $request = new PostEditRequest;
        $rules = $request->rules();

        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();

        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'image' => ['Mimes' => ['jpeg', 'png', 'jpg'], 'Max' => [2048],],
        ];

        //どこがエラーになったか検証
        $this->assertEquals($expectedFailed, $validator->failed());
    }

    //バリデーションの通過テスト
    public function testPostEditRequestNomal()
    {
        $data = [
            'content' => str_repeat('ああああああtest', 15),
            'image' => $this->upload_file,
            'animals_name' => $this->animals_name,
        ];
        $request = new PostEditRequest;
        $rules = $request->rules();

        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();
        
        //データが真であるか確認
        $this->assertTrue($result);
    }

    //管理ユーザーでログイン
    //管理ユーザーで投稿削除可能かテスト
    //成功フラッシュメッセージが表示されているか確認
    public function testAdminDeletePost()
    {
        $admin = factory(User::class)->create([
                    'email' => 'admin@example.com',
                ]);
        $url = route('posts.show', $this->posts[0]);

        //管理ユーザーでログイン
        $this->actingAs($admin)
            ->from($url)
            ->delete(route('posts.destroy', $this->posts[0]))
            ->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHas('msg_success', '投稿削除しました');
    }
}