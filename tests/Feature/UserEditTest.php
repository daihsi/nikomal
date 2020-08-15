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
class UserEditTest extends TestCase
{

    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */

    //正常に認証ユーザーがユーザー詳細ページにアクセス
    //その後編集ページにアクセスできるか確認
    public function testEditUserNormalAccess()
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
    public function testEditUserAbnormalAccess()
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
    public function testGuestUserEditPegeAbnormalAccess()
    {
        $factory_user = factory(User::class)->create();

        //登録ユーザーの詳細ページにアクセス認証
        //ユーザーページでしか表示しない編集ボタンが表示されていないか確認
        $response = $this->get(route('users.show', $factory_user->id))
            ->assertStatus(200)
            ->assertViewIs('users.show')
            ->assertDontSee('編集');

        //登録ユーザーの編集ページにアクセスできないことを確認
        $response = $this->get(route('users.edit', $factory_user->id))>assertStatus(302);
    }

    //必須項目である名前欄を空でリクエストした場合のバリデーションテスト
    public function testUserUpdateRequestNameNull()
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
    public function testUserUpdateRequestOverflow()
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
    public function testUserUpdateRequestFormat()
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
    public function testUserUpdateRequestNomal()
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

        $response = $this->from($url)->put(route('users.update', $factory_user->id),
                [
                    'name' => 'テスト',
                    'file' => $avatar,
                    'self_introduction' => str_repeat('あ', 150),
                ]);
        $this->assertDatabaseHas('users', $data);

        //リクエストに成功したため、詳細ページにリダイレクトしているか確認
        $response->assertStatus(302)->assertRedirect($url);
    }

    //詳細ページにリダイレクトした際に、情報が更新されて表示されているかテスト
    public function testInformationChanged()
    {
        $factory_user = factory(User::class)->create();
        $this->actingAs($factory_user);
        $url = route('users.show', $factory_user->id);

        //更新前の情報か確認
        $this->get($url)
            ->assertStatus(200)
            ->assertViewIs('users.show')
            ->assertSee($factory_user->name, $factory_user->self_introduction);

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
            ->assertSee($name, $self_introduction);
    }
}