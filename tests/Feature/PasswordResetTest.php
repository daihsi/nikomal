<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
    }

    //パスワード再設定リクエストテスト
    public function testUserPasswordReset()
    {
        $url = route('password.request');

        //パスワード再設定フォームにアクセス
        $this->get($url)
            ->assertOk();

        //パスワード再設定リクエスト送信
        $response = $this->from($url)
                    ->post(route('password.email'), [
                        'email' => $this->user->email
                    ]);

        //再設定リクエストが成功し、同ページにリダイレクトしたか確認
        //成功フラッシュメッセージが表示されたかも確認
        $response->assertStatus(302)
                ->assertRedirect($url)
                ->assertSessionHas('msg_success',
                    'パスワードリセット用URLを送信しました。');
    }

    //パスワード再設定バリエーションエラーテスト
    public function testFailureUserPasswordReset()
    {
        $url = route('password.request');
        $user = factory(User::class)->make();

        //パスワード再設定リクエスト送信
        $response = $this->from($url)
                    ->post(route('password.email'), [
                        'email' => $user->email
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
}
