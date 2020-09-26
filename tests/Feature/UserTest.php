<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use App\Http\Requests\RegisterRequest;
use App\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */

    //項目入力必須が空の場合のバリデーションテスト
    public function testRegisterRequestNull()
    {
        //ユーザー登録時に必須項目を空でリクエストしたと仮定
        $data = [
            'name' => null,
            'email' => null,
            'password' => null,
            'password_confirmation' => null,
            'avatar' => null,
        ];

        $request = new RegisterRequest;
        $rules = $request->rules();
        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();
        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'name' => ['Required' => [],],
            'email' => ['Required' => [],],
            'password' => ['Required' => [],],
        ];
        //どこがエラーになったのか検証
        $this->assertEquals($expectedFailed, $validator->failed());
    }

    //入力項目が桁数オーバーのためのバリデーションテスト
    //パスワードは桁が足りない時のバリデーション
    public function testRegisterRequestOverflow()
    {
        //ユーザー登録時に名前、メールを桁数オーバーで
        //パスワードは桁が足りないと仮定
        $data = [
            'name' => str_repeat('あ', 16),
            'email' => str_repeat('a', 244). '@example.com',
            'password' => 'test123',
            'password_confirmation' => 'test123',
            'avatar' => null,
        ];

        $request = new RegisterRequest;
        $rules = $request->rules();
        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();
        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'name' => ['Max' => [15],],
            'email' => ['Max' => [255],],
            'password' => ['Min' => [8],],
        ];
        //どこがエラーになったか検証
        $this->assertEquals($expectedFailed, $validator->failed());
    }

    //メール、画像フォーマットのバリデーションテスト
    public function testRegisterRequestFormat()
    {
        //メール、画像のフォーマットを期待値と別のものがリクエストされたと仮定
        $data = [
            'name' => 'テスト',
            'email' => 'aaa',
            'password' => 'test1234',
            'password_confirmation' => 'test1234',
            'avatar' => 'aaaa1234.gif',
        ];

        $request = new RegisterRequest;
        $rules = $request->rules();
        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();
        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'email' => ['Email' => [],],
            'avatar' => ['Mimes' => ['jpeg', 'png', 'jpg'], 'File' => [], 'Image' => [],],
        ];
        //どこがエラーになったか検証
        $this->assertEquals($expectedFailed, $validator->failed());
    }


    //アップロードされた画像ファイルが保存されているかテスト
    public function testAbatarUploadImage()
    {
        Storage::fake('users_avatar');
        $upload_file = UploadedFile::fake()->image('avatar_test.jpg');
        $upload_file->move('storage/framework/testing/disks/users_avatar');

        //ユーザー登録時に画像を選択したものと想定
        $response = $this->post('/register', [
            'avatar' => $upload_file,
        ]);
        //ファイルが保存されたか確認s
        Storage::disk('users_avatar')->assertExists($upload_file->getFilename());
        //別ファイルが保存されていないか確認
        Storage::disk('users_avatar')->assertMissing('avatar_test.pdf');
    }

    //ユーザー登録テスト
    public function testNormalRegisterUser()
    {
        $test_password = '0987654321';

        //ユーザー登録
        $factory_user = factory(User::class)->create([
            'password' => bcrypt($test_password),
        ]);
        $response = $this->post('/register', [
            'name' => $factory_user->name,
            'email' => $factory_user->email,
            'password' => $test_password,
            'password_confirmation' => $test_password,
            'avatar' => $factory_user->avatar,
        ]);

        //登録後リダイレクトの確認
        $response->assertRedirect('/');

        //「さぁ、動物たちの笑っている表情を...」の文字列がトップページに
        //ないことを確認して、認証ユーザー用のページになっているか確認
        $this->get('/')
            ->assertStatus(200)
            ->assertViewIs('welcome')
            ->assertDontSee('さぁ、動物たちの笑っている表情を投稿共有して一緒に癒されましょう');

        //データペースにデータが存在するか確認
        $this->assertDatabaseHas('users', [
            'name' => $factory_user->name,
            'email' => $factory_user->email,
            'avatar' => $factory_user->avatar
        ]);
    }

    //ゲストユーザー用の表示になっているかテスト
    public function testGuestUser()
    {
        $response = $this->get('/');
        //「さぁ、動物たちの笑っている表情を...」の文字列がトップページに
        //あることを確認して、ゲストユーザー用のページになっているか確認
        $response->assertStatus(200)
            ->assertViewIs('welcome')
            ->assertSee('さぁ、動物たちの笑っている表情を');
    }

    //ログインできているかテスト
    public function testUserLogin()
    {
        $test_password = '0987654321';

        $factory_user = factory(User::class)->create([
            'password' => bcrypt($test_password),
        ]);
        //まだ未認証であることを確認
        $this->assertGuest();
        
        //ログインリクエスト
        $response = $this->post('/login', [
            'email' => $factory_user->email,
            'password' => $test_password,
        ]);
        //認証できているか確認
        $this->assertAuthenticatedAs($factory_user);

        //認証後トップページへリダイレクトできているか確認
        $response->assertRedirect('/');

        //「さぁ、動物たちの笑っている表情を...」の文字列がトップページに
        //ないことを確認して、ログインユーザー用のページになっているか確認
        $this->get('/')
            ->assertStatus(200)
            ->assertViewIs('welcome')
            ->assertDontSee('さぁ、動物たちの笑っている表情を投稿共有して一緒に癒されましょう');
    }

    //ログアウトできているかテスト
    public function testUserLogout()
    {
        $test_password = '0987654321';

        $factory_user = factory(User::class)->create([
            'password' => bcrypt($test_password),
        ]);
        //認証を行い、認証されたか確認
        $this->actingAs($factory_user)
            ->assertAuthenticatedAs($factory_user);

        //ログアウトリクエスト
        $response = $this->post(route('logout'));

        //ゲストユーザーになったか確認
        $this->assertGuest();

        //トップページにリダイレクトされたか確認
        $response->assertRedirect('/');

        //「さぁ、動物たちの笑っている表情を...」の文字列がトップページに
        //あることを確認して、ゲストユーザー用のページになっているか確認
        $this->get('/')
            ->assertStatus(200)
            ->assertViewIs('welcome')
            ->assertSee('さぁ、動物たちの笑っている表情を');
    }
}
