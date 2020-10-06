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
