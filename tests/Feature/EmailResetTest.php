<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\ResetEmailRequest;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Illuminate\Support\Str;
use App\User;
use App\EmailReset;

class EmailResetTest extends TestCase
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
    }

    public function tearDown(): void
    {
        Artisan::call('migrate:refresh');
        parent::tearDown();
    }

    //メールアドレス再設定リクエストテスト
    public function testEmailResetRequest(): void
    {
        $user = factory(User::class)->make();
        $email = $user->email;
        $url = route('email.request');
        $data = [
                'new_email' => $email,
                'guest_login_email' => 'guest@example.com',
            ];

        //メールアドレス再設定フォームにアクセス
        $this->actingAs($this->user)
            ->get($url)
            ->assertOk();

        //メールアドレス再設定リクエスト送信
        //再設定リクエストが成功し、同ページにリダイレクトしたか確認
        //成功フラッシュメッセージが表示されたかも確認
        $response = $this->from($url)
                ->post(route('email.email'), $data)
                ->assertStatus(302)
                ->assertRedirect($url)
                ->assertSessionHas('msg_success', '確認メールを送信しました');

        //テーブルにリクエストデータが保存してあるか確認
        $this->assertDatabaseHas('email_resets', [
                        'user_id' => $this->user->id,
                        'new_email' => $data['new_email'],
                    ]);
    }

    //簡単ログイン用のメールアドレス再設定エラーテスト
    public function testFailureEmaiResetRequest(): void
    {
        $url = route('email.request');

        //簡単ログイン用のメールアドレスをデータ格納
        $guest_user_email = 'guest@example.com';
        $data = [
                'new_email' => $guest_user_email,
                'guest_login_email' => $guest_user_email,
            ];

        //メールアドレス再設定リクエスト送信
        //簡単ログイン用メールアドレスはバリエーションに通らない
        //再設定リクエストが失敗し、同ページにリダイレクトしたか確認
        //失敗フラッシュメッセージが表示されたかも確認
        $response = $this->actingAs($this->user)
                ->from($url)
                ->post(route('email.email'), $data)
                ->assertStatus(302)
                ->assertRedirect($url)
                ->assertSessionHas('msg_error', 'リクエストに失敗しました')
                ->assertSessionHasErrors('new_email', '簡単ログイン用のメールアドレスは変更できません');
    }

    //メールアドレス変更テスト
    public function testEmailReset(): void
    {
        $user = factory(User::class)->make();
        $email = $user->email;
        $data = [
                'new_email' => $email,
                'guest_login_email' => 'guest@example.com',
            ];

        //メールアドレス再設定リクエスト送信
        $this->actingAs($this->user)
            ->post(route('email.email'), $data);

        //email_resetsテーブルのトークンを取得
        $email_resets = EmailReset::first();
        $token = $email_resets->token;

        //メールアドレス変更処理
        //リダイレクト確認
        //成功フラッシュメッセージが表示されているか確認
        $this->get(route('email.reset', $token))
            ->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHas('msg_success', 'メールアドレスを変更しました');

        //メールアドレスが変更してあるかテーブル確認
        $this->assertDatabaseHas('users', [
                'email' => $email,
            ]);

        //一時保存のトークン情報等のテーブル削除してあるかテーブル確認
        $this->assertDeleted('email_resets', [
                'new_email' => $email,
                'token' => $token,
            ]);
    }

    //メールアドレス変更テスト
    public function testFailureEmailReset(): void
    {
        $user = factory(User::class)->make();
        $email = $user->email;
        $data = [
                'new_email' => $email,
                'guest_login_email' => 'guest@example.com',
            ];

        //トークン生成
        $token = hash_hmac(
                'sha256',
                Str::random(40). $email,
                config('app.key')
            );

        //メールアドレス再設定リクエスト送信
        $this->actingAs($this->user)
            ->post(route('email.email'), $data);

        //メールアドレス変更処理(失敗)
        //リダイレクト確認
        //失敗フラッシュメッセージが表示されているか確認
        $this->get(route('email.reset', $token))
            ->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHas('msg_error', 'メールアドレスの変更に失敗しました');

        //メールアドレスが変更してないかテーブル確認
        $this->assertDatabaseMissing('users', [
                'email' => $email,
            ]);
    }

    //必須項目であるメールアドレスを空でリクエストした場合のバリデーションテスト
    public function testResetEmailRequestNull(): void
    {
        $this->actingAs($this->user);

        //メールアドレスを空で更新リクエストしたと仮定
        $data = [
            'new_email' => null,
            'guest_login_email' => 'guest@example.com',
        ];
        $this->post(route('email.email'), $data);

        $request = new ResetEmailRequest;
        $rules = $request->rules();

        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();

        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'new_email' => ['Required' => [],],
        ];
        //どこがエラーになったのか検証
        $this->assertEquals($expectedFailed, $validator->failed());

        //データベースにデータが保存されていないか確認
        $this->assertDatabaseMissing('email_resets', [
                    'new_email' => $data['new_email'],
                ]);
    }

    //メールフォーマットのバリデーションテスト
    public function testResetEmailRequestFormat(): void
    {
        $this->actingAs($this->user);

        //不正メールフォーマットでリクエストしたと仮定
        $data = [
            'new_email' => 'aaa',
            'guest_login_email' => 'guest@example.com',
        ];
        $this->post(route('email.email'), $data);

        $request = new ResetEmailRequest;
        $rules = $request->rules();

        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();

        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'new_email' => ['Email' => [],],
        ];
        //どこがエラーになったのか検証
        $this->assertEquals($expectedFailed, $validator->failed());

        //データベースにデータが保存されていないか確認
        $this->assertDatabaseMissing('email_resets', [
                    'new_email' => $data['new_email'],
                ]);
    }

    //メールアドレスが一意でない場合のバリデーションテスト
    public function testResetEmailRequestUnque(): void
    {
        $this->actingAs($this->user);
        $user = factory(User::class)->create();

        //メールアドレスが一意でないと仮定
        $data = [
            'new_email' => $user->email,
            'guest_login_email' => 'guest@example.com',
        ];
        $this->post(route('email.email'), $data);

        $request = new ResetEmailRequest;
        $rules = $request->rules();

        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();

        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'new_email' => ['Unique' => ['users', 'email'],],
        ];
        //どこがエラーになったのか検証
        $this->assertEquals($expectedFailed, $validator->failed());

        //データベースにデータが保存されていないか確認
        $this->assertDatabaseMissing('email_resets', [
                    'new_email' => $data['new_email'],
                ]);
    }

    //メールアドレスの桁あふれのバリデーションテスト
    public function testResetEmailRequestOverflow(): void
    {
        $this->actingAs($this->user);

        //メールアドレスが桁あふれしたと仮定
        $data = [
            'new_email' => str_repeat('a', 244). '@example.com',
            'guest_login_email' => 'guest@example.com',
        ];
        $this->post(route('email.email'), $data);

        $request = new ResetEmailRequest;
        $rules = $request->rules();

        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();

        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'new_email' => ['Max' => [255],],
        ];
        //どこがエラーになったのか検証
        $this->assertEquals($expectedFailed, $validator->failed());

        //データベースにデータが保存されていないか確認
        $this->assertDatabaseMissing('email_resets', [
                    'new_email' => $data['new_email'],
                ]);
    }

    //管理ユーザーはメールアドレス再設定フォームにアクセスできないかテスト
    //リダイレクトの確認
    //失敗フラッシュメッセージが表示されているか確認
    public function testAdminInaccessibleEmailResetPage(): void
    {
        $admin = factory(User::class)->create([
                    'email' => 'admin@example.com',
                ]);
        $this->actingAs($admin)
            ->from('/')
            ->get(route('email.request'))
            ->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHas('msg_error', '管理ユーザーはメールアドレス再設定ができません');
    }
}