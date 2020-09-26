<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\User;

class PasswordResetTest extends DuskTestCase
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

        //テストの為、トークンを新しく生成する
        $this->token = hash_hmac('sha256', \Str::random(40), $this->user);
    }

    //ログインページ内のリンクで正常にアクセスできるかテスト
    public function testUserPasswordRequestLoginPageLink()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->click('.login_icon')
                    ->click('.btn-link')
                    ->assertRouteIs('password.request')
                    ->screenshot('password_reset');
        });
    }

    //ログイン後、ナビゲーションバーのリンクで正常にアクセスできるかテスト
    public function testUserPasswordRequestNavbarLink()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/')
                    ->click('#navbarDropdown')
                    ->click('.password_reset_icon')
                    ->assertRouteIs('password.request')
                    ->screenshot('password_reset');
        });
    }

    //パスワード再設定リクエストのテスト
    //メールアドレス通知～リセット成功までの一連のテスト
    public function testUserPasswordReset()
    {
        $password = 1234567890;

        $this->browse(function ($browser) use ($password) {
            $browser->visitRoute('password.request')
                    ->type('email', $this->user->email)
                    ->assertInputValue('email', $this->user->email)
                    ->press('再設定URLを送信')
                    ->assertSee('パスワードリセット用URLを送信しました')
                    ->screenshot('password_reset');

            //テーブルのトークンを更新
            \DB::table('password_resets')
                ->where('email', $this->user->email)
                ->update([
                    'token' => password_hash($this->token, PASSWORD_BCRYPT, ['cost' => '10'])
                ]);

            $browser->visitRoute('password.reset', [
                        'token' => $this->token,
                        'email' => $this->user->email,
                    ])
                    ->type('email', $this->user->email)
                    ->type('password', $password)
                    ->type('password_confirmation', $password)
                    ->assertInputValue('email', $this->user->email)
                    ->assertInputValue('password', $password)
                    ->assertInputValue('password_confirmation', $password)
                    ->press('パスワード再設定')
                    ->assertPathIs('/')
                    ->assertSee('パスワードを変更しました')
                    ->screenshot('password_reset');
        });
    }

    //パスワード再設定リクエストのリセット失敗テスト
    public function testFailureUserPasswordReset()
    {
        $password = 1234567890;
        $user = factory(User::class)->make();
        $email = $user->email;

        //簡単ログイン用メールアドレス
        $guest_login_email = 'guest@example.com';

        $this->browse(function ($browser) use ($password, $email, $guest_login_email) {
            $browser->visitRoute('password.request')
                    ->type('email', $this->user->email)
                    ->assertInputValue('email', $this->user->email)
                    ->press('再設定URLを送信')
                    ->assertSee('パスワードリセット用URLを送信しました')
                    ->screenshot('password_reset');

            //テーブルのトークンを更新
            \DB::table('password_resets')
                ->where('email', $this->user->email)
                ->update([
                    'token' => password_hash($this->token, PASSWORD_BCRYPT, ['cost' => '10'])
                ]);

            $browser->visitRoute('password.reset', [
                        'token' => $this->token,
                        'email' => $this->user->email,
                    ])
                    ->type('email', $email)
                    ->type('password', $password)
                    ->type('password_confirmation', $password)
                    ->assertInputValue('email', $email)
                    ->assertInputValue('password', $password)
                    ->assertInputValue('password_confirmation', $password)
                    ->press('パスワード再設定')
                    ->assertSee('リクエストに失敗しました')
                    ->assertSee('メールアドレスに一致するユーザーが見つかりません。')
                    ->screenshot('password_reset');
        });
    }

    //簡単ログイン用のメールアドレスで再設定できないかテスト
    //失敗フラッシュメッセージが表示されているか確認
    public function testGuestUserLoginEmailResetPasswordRequest()
    {
        //簡単ログイン用メールアドレス
        $guest_login_email = 'guest@example.com';

        $this->browse(function ($browser) use ($guest_login_email) {
            $browser->visitRoute('password.request')
                    ->type('email', $guest_login_email)
                    ->assertInputValue('email', $guest_login_email)
                    ->press('再設定URLを送信')
                    ->assertSee('リクエストに失敗しました')
                    ->assertSee('簡単ログイン用のパスワードは変更できません')
                    ->screenshot('password_reset');
        });
    }

    //メールアドレスの桁あふれリクエストエラーテスト
    //失敗フラッシュメッセージが表示されているか確認
    public function testFailureEmailResetPasswordRequest()
    {
        $email = str_repeat('a', 244). '@example.com';

        $this->browse(function ($browser) use ($email) {
            $browser->visitRoute('password.request')
                    ->type('email', $email)
                    ->assertInputValue('email', $email)
                    ->press('再設定URLを送信')
                    ->assertSee('リクエストに失敗しました')
                    ->assertSee('メールアドレスは255字以下で入力してください。')
                    ->screenshot('password_reset');
        });
    }
}
