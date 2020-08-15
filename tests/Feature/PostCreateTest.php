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
use App\Http\Requests\PostRequest;
use Tests\TestCase;

class PostCreateTest extends TestCase
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

        // 仮画像作成
        Storage::fake('post_images');
        $this->upload_file = UploadedFile::fake()->image('test.jpg')->size(2048);
        $this->upload_file->move('storage/framework/testing/disks/post_images');
        $this->file_name = $this->upload_file->getFilename();
    }

    //ゲストユーザーが、新規投稿ページにアクセスできないようになっているかテスト
    public function testGuestUserAccessPostCreatePage()
    {
        $this->get(route('posts.create'))->assertStatus(302);
    }

    //ゲストユーザーが、新規投稿できないようになっているかテスト
    public function testGuestUserPostCreate()
    {
        $this->post(route('posts.store'), [
            'content' => 'テストテスト'
        ]);
        $this->assertDatabaseMissing('posts', [
            'content' => 'テストテスト'
        ]);
    }

    //新規投稿が正常にデータベースに保存されているかテスト
    public function testNewPostCreate()
    {
        $factory_user = factory(User::class)->create();
        $this->actingAs($factory_user);
        $post = factory(Post::class)->make();

        //カテゴリーをランダムに選ぶ
        $i = rand(1,6);
        foreach (array_rand(config('animals.animals'. $i), 3) as $index => $name){
                $animals_name[] = $name;
        }

        $data = [
            'content' => $post->content,
            'image' => $this->upload_file,
            'animals_name' => $animals_name,
        ];
        $url = route('posts.create', $factory_user->id);
        $response = $this->from($url)->post(route('posts.store', $factory_user->id), $data);

        //postsテーブルにデータが正常に保存してあるか確認
        $this->assertDatabaseHas('posts', [
            'user_id' => $factory_user->id,
            'content' => $post->content,
        ]);
        //animalsテーブルにデータが正常に保存してあるか確認
        $k = 1;
        foreach($animals_name as $animal_name) {
            $this->assertDatabaseHas('animals', [
                'name' => $animal_name,
            ]);
            $animal_ids[] = $k++;
        }
        //中間テーブルにデータが正常に保存してあるか確認
        foreach($animal_ids as $animal_id){
            $this->assertDatabaseHas('post_category', [
                'animal_id' => $animal_id,
            ]);
        }
    }

    //必須項目を空でリクエストした際のバリデーションテスト
    public function testPostRequestNull()
    {
        $data = [
            'content' => null,
            'image' => null,
            'animals_name' => null,
        ];
        $request = new PostRequest;
        $rules = $request->rules();
        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();
        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'content' => ['Required' => [],],
            'image' => [
                'Required' => [],
                'File' => [], 
                'Image' => [], 
                'Mimes' => ['jpeg', 'png', 'jpg'], 
            ],
            'animals_name' => ['Required' => [],],
        ];
        //どこがエラーになったか検証
        $this->assertEquals($expectedFailed, $validator->failed());
    }

    //contentの桁あふれの際のバリデーションテスト
    public function testPostRequestOverFlow()
    {
        //contentが1文字多いと仮定
        $data = [
            'content' => str_repeat('あ', 151),
            'image' => $this->upload_file,
            'animals_name' => 'イヌ',
        ];
        $request = new PostRequest;
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
    public function testPostRequestFormat()
    {
        Storage::fake('post_images');
        $upload_file = UploadedFile::fake()->image('test.gif')->size(2049);
        $upload_file->move('storage/framework/testing/disks/post_images');
        $file_name = $upload_file->getFilename();

        //画像のフォーマット、サイズを期待値外��ものがリクエストされたと仮定
        $data = [
            'content' => str_repeat('あ', 150),
            'image' => $upload_file,
            'animals_name' => 'イヌ',
        ];
        $request = new PostRequest;
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
    public function testPostRequestNomal()
    {
        $animals_name = ['イヌ', 'ネコ', 'オラウータン'];
        $data = [
            'content' => str_repeat('ああああああtest', 15),
            'image' => $this->upload_file,
            'animals_name' => $animals_name,
        ];
        $request = new PostRequest;
        $rules = $request->rules();
        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();
        //データが真であるか確認
        $this->assertTrue($result);
    }

    //トップページに投稿が表示されているかテスト
    public function testPostViewToppage()
    {
        $factory_user = factory(User::class)->create();
        $this->actingAs($factory_user);
        $post = factory(Post::class)->make();

        $i = rand(1,6);
        foreach (array_rand(config('animals.animals'. $i), 3) as $index => $name){
                $animals_name[] = $name;
        }

        $data = [
            'content' => $post->content,
            'image' => $this->upload_file,
            'animals_name' => $animals_name,
        ];
        $response = $this->post(route('posts.store', $factory_user->id), $data)
                        ->assertStatus(302)
                        //リダイレクト先の確認
                        ->assertRedirect('/');

        //トップページに投稿が表示されているか確認
        $this->get('/')
            ->assertStatus(200)
            ->assertViewIs('welcome')
            ->assertSee($post->content)
            ->assertSeeTextInOrder($animals_name);
    }

    //ユーザー詳細ページに投稿が表示されているかテスト
    public function testPostViewUserShowpage()
    {
        $factory_user = factory(User::class)->create();
        $this->actingAs($factory_user);
        $post = factory(Post::class)->make();

        $i = rand(1,6);
        foreach (array_rand(config('animals.animals'. $i), 3) as $index => $name){
                $animals_name[] = $name;
        }

        $data = [
            'content' => $post->content,
            'image' => $this->upload_file,
            'animals_name' => $animals_name,
        ];
        $response = $this->post(route('posts.store', $factory_user->id), $data)
                        ->assertStatus(302)
                        //リダイレクト先の確認
                        ->assertRedirect('/');

        //ユーザー詳細ページに投稿が表示されているか確認
        $this->get(route('users.show', $factory_user->id))
            ->assertStatus(200)
            ->assertViewIs('users.show')
            ->assertSee($post->content)
            ->assertSeeTextInOrder($animals_name);
    }
}
