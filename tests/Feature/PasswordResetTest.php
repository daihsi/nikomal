<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use Tests\TestCase;
use App\User;

class PasswordResetTest extends TestCase
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
        $this->user = factory(User::class)->create();

        //テストの為、トークンを新しく生成する
        $this->token = hash_hmac('sha256', \Str::random(40), $this->user);
    }

    //パスワード再設定リクエストのため、
    //メールアドレスに再設定用通知送信テスト
    public function testUserPasswordRequest()
    {
        $url = route('password.request');

        //パスワード再設定フォームにアクセス
        $this->get($url)
            ->assertOk();

        //パスワード再設定リクエスト送信
        $response = $this->from($url)
                    ->post(route('password.email'), [
                        'email' => $this->user->email,
                        'guest_login_email' => 'guest@example.com',
                    ]);

        //再設定リクエストが成功し、同ページにリダイレクトしたか確認
        //成功フラッシュメッセージが表示されたかも確認
        $response->assertStatus(302)
                ->assertRedirect($url)
                ->assertSessionHas('msg_success',
                    'パスワードリセット用URLを送信しました。');
    }

    //パスワード再設定リクエストのため、
    //メールアドレスに再設定用通知送信のバリデーションエラーテスト
    public function testFailureUserPasswordRequest()
    {
        $url = route('password.request');
        $user = factory(User::class)->make();

        //パスワード再設定リクエスト送信
        $response = $this->from($url)
                    ->post(route('password.email'), [
                        'email' => $user->email,
                        'guest_login_email' => 'guest@example.com',
                    ]);

        //再設定リクエストが成功し、同ページにリダイレクトしたか確認
        //失敗フラッシュメッセージが表示されたかも確認
        //バリデーションエラーメッセージも表示されているか確認
        $response->assertStatus(302)
                ->assertRedirect($url)
                ->assertSessionHas('msg_error',
                    'リクエストに失敗しました')
                ->assertSessionHasErrors('email',
                    'メールアドレスに一致するユーザーが見つかりません。');
    }

    //パスワード再設定リクエスト
    public function testUserPasswordReset()
    {
        $password = 1234567890;

        //パスワード再設定リクエスト送信
        $this->post(route('password.email'), [
            'email' => $this->user->email,
            'guest_login_email' => 'guest@example.com',
        ]);

        //テストの為、トークンを新しく生成する
        //$token = hash_hmac('sha256', \Str::random(40), $this->user);
            \DB::table('password_resets')
                ->where('email', $this->user->email)
                ->update([
                    'token' => password_hash($this->token, PASSWORD_BCRYPT, ['cost' => '10'])
                ]);

        //パスワードリセットフォームにアクセス
        $this->get(route('password.reset', [
            'token' => $this->token,
            'email' => $this->user->email,
            ]))
            ->assertOk();

        //リクエストデータの格納
        $data = [
                'token' => $this->token,
                'email' => $this->user->email,
                'password' => $password,
                'password_confirmation' => $password,
            ];

        //パスワードリセットリクエスト
        $this->post(route('password.update'), $data)
            ->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHas('msg_success', 'パスワードを変更しました');

        //パスワードが変わったか確認
        $new_password = User::first()->password;
        $this->assertTrue(\Hash::check($password, $new_password));
    }

    //パスワード再設定リクエストバリデーションエラーテスト
    public function testFailureEmailUserPasswordReset()
    {
        $password = 1234567890;
        $user = factory(User::class)->make();

        //パスワード再設定リクエスト送信
        $this->post(route('password.email'), [
            'email' => $this->user->email,
            'guest_login_email' => 'guest@example.com',
        ]);

        //テストの為、トークンを新しく生成する
        //$token = hash_hmac('sha256', \Str::random(40), $this->user);
            \DB::table('password_resets')
                ->where('email', $this->user->email)
                ->update([
                    'token' => password_hash($this->token, PASSWORD_BCRYPT, ['cost' => '10'])
                ]);
        $url = route('password.reset', [
                'token' => $this->token,
                'email' => $this->user->email,
            ]);

        //パスワードリセットフォームにアクセス
        $this->get($url)
            ->assertOk();

        //リクエストデータの格納
        //メールがリクエストユーザーのアドレスでない
        $data = [
                'token' => $this->token,
                'email' => $user->email,
                'password' => $password,
                'password_confirmation' => $password,
            ];

        //パスワードリセットリクエストが失敗
        //リダイレクト確認
        //失敗フラッシュメッセージ、エラーメッセージが表示されているか確認
        $this->from($url)
            ->post(route('password.update'), $data)
            ->assertStatus(302)
            ->assertRedirect($url)
            ->assertSessionHas('msg_error', 'リクエストに失敗しました')
            ->assertSessionHasErrors('email', 'パスワードリセット用トークンが不正です。');

        //パスワードが変わってないか確認
        $new_password = User::first()->password;
        $this->assertFalse(\Hash::check($password, $new_password));
    }

    //パスワード再設定リクエストバリデーションエラーテスト
    public function testFailurePasswordUserPasswordReset()
    {
        $password = 1234567;

        //パスワード再設定リクエスト送信
        $this->post(route('password.email'), [
            'email' => $this->user->email,
            'guest_login_email' => 'guest@example.com',
        ]);

        //テストの為、トークンを新しく生成する
        //$token = hash_hmac('sha256', \Str::random(40), $this->user);
            \DB::table('password_resets')
                ->where('email', $this->user->email)
                ->update([
                    'token' => password_hash($this->token, PASSWORD_BCRYPT, ['cost' => '10'])
                ]);
        $url = route('password.reset', [
                'token' => $this->token,
                'email' => $this->user->email,
            ]);

        //パスワードリセットフォームにアクセス
        $this->get($url)
            ->assertOk();

        //リクエストデータの格納
        //メールがリクエストユーザーのアドレスでない
        $data = [
                'token' => $this->token,
                'email' => $this->user->email,
                'password' => $password,
                'password_confirmation' => $password,
            ];

        //パスワードリセットリクエストが失敗
        //リダイレクト確認
        //エラーメッセージが表示されているか確認
        $this->from($url)
            ->post(route('password.update'), $data)
            ->assertStatus(302)
            ->assertRedirect($url)
            ->assertSessionHas('msg_error', 'リクエストに失敗しました')
            ->assertSessionHasErrors('password', 'パスワードは8文字以上にして、確認用入力欄と一致させてください。');

        //パスワードが変わってないか確認
        $new_password = User::first()->password;
        $this->assertFalse(\Hash::check($password, $new_password));
    }

    //必須項目すべての値を空でリクエストした場合のバリデーションテスト
    //(パスワード再設定フォーム)
    public function testResetPasswordResetNull()
    {
        $password = 12345678;

        //全ての値を空で更新リクエストしたと仮定
        $data = [
            'token' => null,
            'email' => null,
            'password' => null,
            'password_confirmation' => null,
        ];
        $this->post(route('password.update'), $data);

        $request = new ResetPasswordRequest;
        $rules = $request->rules();

        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();

        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'token' => ['Required' => [],],
            'email' => ['Required' => [],],
            'password' => ['Required' => [],],
        ];
        //どこがエラーになったのか検証
        $this->assertEquals($expectedFailed, $validator->failed());
    }

    //メールフォーマットのバリデーションテスト
    //(パスワード再設定フォーム)
    public function testResetPasswordResetFormat()
    {
        $password = 12345678;

        //不正メールフォーマットでリクエストしたと仮定
        $data = [
            'token' => $this->token,
            'email' => 'aaa',
            'password' => $password,
            'password_confirmation' => $password,
        ];
        $this->post(route('password.update'), $data);

        $request = new ResetPasswordRequest;
        $rules = $request->rules();

        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();

        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'email' => ['Email' => [],],
        ];
        //どこがエラーになったのか検証
        $this->assertEquals($expectedFailed, $validator->failed());
    }

    //メールアドレスの桁あふれとパスワードの桁足らずのバリデーションテスト
    //(パスワード再設定フォーム)
    public function testResetPasswordResetOverflow()
    {
        $password = 1234567;

        //メールアドレスの桁あふれとパスワードの桁足らずと仮定
        $data = [
            'token' => $this->token,
            'email' => str_repeat('a', 244). '@example.com',
            'password' => $password,
            'password_confirmation' => $password,
        ];
        $this->post(route('password.update'), $data);

        $request = new ResetPasswordRequest;
        $rules = $request->rules();

        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();

        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'email' => ['Max' => [255],],
            'password' => ['Min' => [8],],
        ];
        //どこがエラーになったのか検証
        $this->assertEquals($expectedFailed, $validator->failed());
    }

    //必須項目であるメールアドレスを空でリクエストした場合のバリデーションテスト
    //(メールアドレスに再設定通知を送るフォーム)
    public function testResetPasswordRequestNull()
    {
        //メールアドレスを空で更新リクエストしたと仮定
        $data = [
            'email' => null,
            'guest_login_email' => 'guest@example.com',
        ];
        $this->post(route('password.email'), $data);

        $request = new ForgotPasswordRequest;
        $rules = $request->rules();

        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();

        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'email' => ['Required' => [],],
        ];
        //どこがエラーになったのか検証
        $this->assertEquals($expectedFailed, $validator->failed());
    }

    //メールフォーマットのバリデーションテスト
    //(メールアドレスに再設定通知を送るフォーム)
    public function testResetPasswordRequestFormat()
    {
        //不正メールフォーマットでリクエストしたと仮定
        $data = [
            'email' => 'aaa',
            'guest_login_email' => 'guest@example.com',
        ];
        $this->post(route('password.email'), $data);

        $request = new ForgotPasswordRequest;
        $rules = $request->rules();

        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();

        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'email' => ['Email' => [],],
        ];
        //どこがエラーになったのか検証
        $this->assertEquals($expectedFailed, $validator->failed());
    }

    //メールアドレスの桁あふれのバリデーションテスト
    //(メールアドレスに再設定通知を送るフォーム)
    public function testResetPasswordRequestOverflow()
    {
        //メールアドレスが桁あふれしたと仮定
        $data = [
            'email' => str_repeat('a', 244). '@example.com',
            'guest_login_email' => 'guest@example.com',
        ];
        $this->post(route('password.email'), $data);

        $request = new ForgotPasswordRequest;
        $rules = $request->rules();

        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();

        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'email' => ['Max' => [255],],
        ];
        //どこがエラーになったのか検証
        $this->assertEquals($expectedFailed, $validator->failed());
    }

    //簡単ログイン用のメールアドレスでリクエストした際のバリデーションテスト
    //(メールアドレスに再設定通知を送るフォーム)
    public function testResetPasswordRequestGuestLoginEmail()
    {
        $guest_login_email = 'guest@example.com';

        //簡単ログイン用のメールアドレスでリクエストしたと仮定
        $data = [
            'email' => $guest_login_email,
            'guest_login_email' => $guest_login_email,
        ];
        $this->post(route('password.email'), $data);

        $request = new ForgotPasswordRequest;
        $rules = $request->rules();

        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();

        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'email' => ['Different' => ['guest_login_email'],],
        ];
        //どこがエラーになったのか検証
        $this->assertEquals($expectedFailed, $validator->failed());
    }
}