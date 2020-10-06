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
    public function testUserPasswordRequestLoginPageLink(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->click('.login_icon')
                    ->click('.btn-link')
                    ->assertRouteIs('password.request');
        });
    }

    //ログイン後、ナビゲーションバーのリンクで正常にアクセスできるかテスト
    public function testUserPasswordRequestNavbarLink(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/')
                    ->click('#navbarDropdown')
                    ->click('.password_reset_icon')
                    ->assertRouteIs('password.request');
        });
    }

    //パスワード再設定リクエストのテスト
    //メールアドレス通知～リセット成功までの一連のテスト
    public function testUserPasswordReset(): void
    {
        $password = 1234567890;

        $this->browse(function ($browser) use ($password) {
            $browser->visitRoute('password.request')
                    ->type('email', $this->user->email)
                    ->assertInputValue('email', $this->user->email)
                    ->press('再設定URLを送信')
                    ->waitForText('パスワードリセット用URLを送信しました')
                    ->assertSee('パスワードリセット用URLを送信しました');

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
                    ->assertSee('パスワードを変更しました');
        });
    }

    //パスワード再設定リクエストのリセット失敗テスト
    public function testFailureUserPasswordReset(): void
    {
        $password = 1234567890;
        $user = factory(User::class)->make();
        $email = $user->email;

        $this->browse(function ($browser) use ($password, $email) {
            $browser->visitRoute('password.request')
                    ->type('email', $this->user->email)
                    ->assertInputValue('email', $this->user->email)
                    ->press('再設定URLを送信')
                    ->waitForText('パスワードリセット用URLを送信しました')
                    ->assertSee('パスワードリセット用URLを送信しました');

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
                    ->assertSee('メールアドレスに一致するユーザーが見つかりません。');
        });
    }

    //簡単ログイン用のメールアドレスで再設定できないかテスト
    //失敗フラッシュメッセージが表示されているか確認
    public function testGuestUserLoginEmailResetPasswordRequest(): void
    {
        //簡単ログイン用メールアドレス
        $guest_login_email = 'guest@example.com';

        $this->browse(function ($browser) use ($guest_login_email) {
            $browser->visitRoute('password.request')
                    ->type('email', $guest_login_email)
                    ->assertInputValue('email', $guest_login_email)
                    ->press('再設定URLを送信')
                    ->assertSee('リクエストに失敗しました')
                    ->assertSee('簡単ログイン用のパスワードは変更できません');
        });
    }

    //メールアドレスの桁あふれリクエストエラーテスト
    //失敗フラッシュメッセージが表示されているか確認
    public function testFailureEmailResetPasswordRequest(): void
    {
        $email = str_repeat('a', 244). '@example.com';

        $this->browse(function ($browser) use ($email) {
            $browser->visitRoute('password.request')
                    ->type('email', $email)
                    ->assertInputValue('email', $email)
                    ->press('再設定URLを送信')
                    ->assertSee('リクエストに失敗しました')
                    ->assertSee('メールアドレスは255字以下で入力してください。');
        });
    }

    //管理ユーザーは、パスワード再設定フォームにアクセスできないかテスト
    //リダイレクトの確認
    //失敗フラッシュメッセージが表示されているか確認
    public function testAdminInaccessiblePasswordResetPage(): void
    {
        $admin = factory(User::class)->create([
                    'email' => 'admin@example.com',
                ]);
        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/')
                    ->visitRoute('password.request')
                    ->assertPathIs('/')
                    ->assertSee('管理ユーザーはパスワード再設定ができません');
        });
    }

    //ナビゲーションバーにパスワード再設定フォームのリンクが表示されていないかテスト
    public function testNavbarPasswordResetLinkNotDisplayed(): void
    {
        $admin = factory(User::class)->create([
                    'email' => 'admin@example.com',
                ]);
        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/')
                    ->click('#navbarDropdown')
                    ->assertMissing('.password_reset_icon');
        });
    }
}
