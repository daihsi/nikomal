<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use App\Http\Requests\UserUpdateRequest;
use App\User;
use App\Post;
use App\PostImage;
use App\Animal;
use App\Comment;

class UserEditTest extends TestCase
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
        $this->users = factory(User::class, 2)->create();

        //削除用の投稿を生成
        $this->posts = factory(Post::class, 2)->create([
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

        //削除用のコメントを生成
        $this->comments = factory(Comment::class, 2)->create([
                            'user_id' => $this->users[0]->id,
                            'post_id' => $this->posts[0]->id,
                        ]);
    }

    //正常に認証ユーザーがユーザー詳細ページにアクセス
    //その後編集ページにアクセスできるか確認
    public function testEditUserNormalAccess(): void
    {
        $factory_user = factory(User::class)->create();
        //認証を行い、認証されたか確認
        $this->actingAs($factory_user)
            ->assertAuthenticatedAs($factory_user);

        //ユーザー詳細ページにアクセス認証
        //ユーザーページでしか表示しない編集ボタンが表示されているか確認
        $response = $this->get(route('users.show', $factory_user->id))
            ->assertStatus(200)
            ->assertViewIs('users.show')
            ->assertSee('編集');

        //ユーザー編集ページにアクセス
        $response = $this->get(route('users.edit', $factory_user->id))
            ->assertStatus(200)
            ->assertViewIs('users.edit')
            ->assertSee($factory_user->name);
    }

    //認証ユーザーではないユーザーの詳細ページにアクセス
    //編集ページにアクセスできないか確認
    public function testEditUserAbnormalAccess(): void
    {
        $factory_userA = factory(User::class)->create();
        $factory_userB = factory(User::class)->create();
        //認証を行い、認証されたか確認
        $this->actingAs($factory_userA)
            ->assertAuthenticatedAs($factory_userA);

        //別ユーザーの詳細ページにアクセス認証
        //ユーザーページでしか表示しない編集ボタンが表示されていないか確認
        $response = $this->get(route('users.show', $factory_userB->id))
            ->assertStatus(200)
            ->assertViewIs('users.show')
            ->assertDontSee('編集');

        //別ユーザーの編集ページにアクセスできないことを確認
        $response = $this->get(route('users.edit', $factory_userB->id))->assertStatus(302);
    }

    //ゲストユーザーが登録ユーザーページにアクセスできるか確認
    //編集ページにアクセスできないか確認
    public function testGuestUserEditPegeAbnormalAccess(): void
    {
        $factory_user = factory(User::class)->create();

        //登録ユーザーの詳細ページにアクセス認証
        //ユーザーページでしか表示しない編集ボタンが表示されていないか確認
        $response = $this->get(route('users.show', $factory_user->id))
            ->assertStatus(200)
            ->assertViewIs('users.show')
            ->assertDontSee('編集');

        //登録ユーザーの編集ページにアクセスできないことを確認
        $response = $this->get(route('users.edit', $factory_user->id))->assertStatus(302);
    }

    //必須項目である名前欄を空でリクエストした場合のバリデーションテスト
    public function testUserUpdateRequestNameNull(): void
    {
        $factory_user = factory(User::class)->create();
        $this->actingAs($factory_user);

        //ユーザー名を空で更新リクエストしたと仮定
        $data = [
            'name' => null,
            'avatar' => null,
            'self_introduction' => null,
        ];
        $url = route('users.show', $factory_user->id);

        $response = $this->from($url)->put(route('users.update', $factory_user->id), $data);

        $request = new UserUpdateRequest;
        $rules = $request->rules();
        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();
        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'name' => ['Required' => [],],
        ];
        //どこがエラーになったのか検証
        $this->assertEquals($expectedFailed, $validator->failed());

        //データベースにデータが保存されていないか確認
        $this->assertDatabaseMissing('users', $data);
        //リクエストに失敗したため、詳細ページにリダイレクトしているか確認
        $response->assertStatus(302)->assertRedirect($url);
    }

    //名前と自己紹介欄の桁あふれでリクエストした場合のバリデーションテスト
    public function testUserUpdateRequestOverflow(): void
    {
        $factory_user = factory(User::class)->create();
        $this->actingAs($factory_user);

        //ユーザー名を空で更新リクエストしたと仮定
        $data = [
            'name' => str_repeat('あ', 16),
            'avatar' => null,
            'self_introduction' => str_repeat('あ', 151),
        ];
        $url = route('users.show', $factory_user->id);

        $response = $this->from($url)->put(route('users.update', $factory_user->id), $data);

        $request = new UserUpdateRequest;
        $rules = $request->rules();
        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();
        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'name' => ['Max' => [15],],
            'self_introduction' => ['Max' => [150],],
        ]; 
        //どこがエラーになったのか検証
        $this->assertEquals($expectedFailed, $validator->failed());

        //データベースにデータが保存されていないか確認
        $this->assertDatabaseMissing('users', $data);
        //リクエストに失敗したため、詳細ページにリダイレクトしているか確認
        $response->assertStatus(302)->assertRedirect($url);
    }

    //画像フォーマット、サイズの期待値外でのリクエストのバリデーションテスト
    public function testUserUpdateRequestFormat(): void
    {
        $factory_user = factory(User::class)->create();
        $this->actingAs($factory_user);

        Storage::fake('users_avatar');
        $avatar = UploadedFile::fake()->create('abc1234.gif')->size(2049);
        $avatar->move('storage/framework/testing/disks/users_avatar');
        $file_name = $avatar->getFilename();

        //画像のフォーマット、サイズを期待値外のものがリクエストされたと仮定
        $data = [
            'name' => $factory_user->name,
            'avatar' => $avatar,
            'self_introduction' => $factory_user->self_introduction,
        ];

        $url = route('users.show', $factory_user->id);

        $request = new UserUpdateRequest;
        $rules = $request->rules();
        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();
        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'avatar' => ['Mimes' => ['jpeg', 'png', 'jpg'], 'Max' => [2048],],
        ];
        //どこがエラーになったか検証
        $this->assertEquals($expectedFailed, $validator->failed());

        $response = $this->from($url)->put(route('users.update', $factory_user->id),
                [
                    'name' => $factory_user->name,
                    'file' => $avatar,
                    'self_introduction' => $factory_user->self_introduction,
                ]);
        //データベースにデータが保存されていないか確認
        $this->assertDatabaseMissing('users', $data);
        //リクエストに失敗したため、詳細ページにリダイレクトしているか確認
        $response->assertStatus(302)->assertRedirect($url);
    }

    //バリデーションを通過したかテスト
    public function testUserUpdateRequestNomal(): void
    {
        $factory_user = factory(User::class)->create();
        $this->actingAs($factory_user);

        Storage::fake('users_avatar');
        $avatar = UploadedFile::fake()->image('abc1234.jpg')->size(2048);
        $avatar->move('storage/framework/testing/disks/users_avatar');
        $file_name = $avatar->getFilename();

        //すべての項目、期待値内の値がリクエストされたと仮定
        $data = [
            'name' => 'テスト',
            'avatar' => $avatar,
            'self_introduction' => str_repeat('あ', 150),
        ];
        $url = route('users.show', $factory_user->id);

        $request = new UserUpdateRequest;
        $rules = $request->rules();
        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();
        //データが真であるか確認
        $this->assertTrue($result);
    }

    //詳細ページにリダイレクトした際に、情報が更新されて表示されているかテスト
    public function testInformationChanged(): void
    {
        $factory_user = factory(User::class)->create();
        $this->actingAs($factory_user);
        $url = route('users.show', $factory_user->id);

        //更新前の情報か確認
        $this->get($url)
            ->assertStatus(200)
            ->assertViewIs('users.show')
            ->assertSee($factory_user->name)
            ->assertSee($factory_user->self_introduction);

        $name = 'テストテストtest';
        $self_introduction = str_repeat('ああああああtest', 15);
        $data = [
            'name' => $name,
            'self_introduction' => $self_introduction,
        ];
        $response = $this->from($url)->put(route('users.update', $factory_user->id), $data);
        $test = $this->assertDatabaseHas('users', $data);

        //リクエストに成功したため、詳細ページにリダイレクトしているか確認
        $response->assertStatus(302)->assertRedirect($url);

        //更新後の情報か確認
        $this->get($url)
            ->assertStatus(200)
            ->assertViewIs('users.show')
            ->assertSee($name)
            ->assertSee($self_introduction);
    }

    //管理ユーザー以外がユーザー削除リクエストで削除できていないかテスト
    public function testCannotUserDeleteRequest(): void
    {
        //usersテーブル確認用データ
        $u_data = [
                    'id' => $this->users[0]->id,
                    'name' => $this->users[0]->name,
                    'email' => $this->users[0]->email,
                ];

        //postsテーブル確認用データ
        $p_data = [
                    'id' => $this->posts[0]->id,
                    'content' => $this->posts[0]->content,
                ];

        //commentsテーブル確認用データ
        $c_data = [
                    'id' => $this->comments[0]->id,
                    'comment' => $this->comments[0]->comment,
                ];

        //削除リクエストしたが403ステータスコード
        //認可されてないためサーバー拒否したか確認
        $this->actingAs($this->users[0])
            ->from('/')
            ->delete(route('users.destroy', $this->users[1]->id))
            ->assertForbidden();

        //データベースにデータが残っているか確認
        $this->assertDatabaseHas('users', $u_data)
            ->assertDatabaseHas('posts', $p_data)
            ->assertDatabaseHas('comments', $c_data);
    }

    //管理ユーザーでユーザー削除テスト
    public function testIndexPageAdminDeleteUser(): void
    {
        $user = factory(User::class)->create([
                    'avatar' => null,
                ]);

        $admin = factory(User::class)->create([
                    'email' => 'admin@example.com',
                ]);

        $index = route('users.index');

        //ユーザー一覧でユーザー削除リクエスト
        //リダイレクト先の確認
        //成功フラッシュメッセージが表示されているか確認
        $response = $this->actingAs($admin)
            ->from($index)
            ->delete(route('users.destroy', $user->id))
            ->assertStatus(302)
            ->assertRedirect($index)
            ->assertSessionHas('msg_success', '「'.$user->name.'」のアカウントを削除しました');

        //テーブルにデータが残っていないか確認
        $this->assertDeleted($user);
    }
}