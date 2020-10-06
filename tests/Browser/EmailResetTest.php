<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\User;
use App\EmailReset;

class EmailResetTest extends DuskTestCase
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
    }

    //簡単ログイン用メールアドレスは変更できないようになっているかテスト
    public function testGuestUserLoginEmailReset(): void
    {
        //簡単ログイン用メールアドレス
        $guest_login_email = 'guest@example.com';

        $this->browse(function ($browser) use ($guest_login_email) {
            $browser->loginAs($this->user)
                    ->visitRoute('email.request')
                    ->assertRouteIs('email.request')
                    ->type('new_email', $guest_login_email)
                    ->assertInputValue('new_email', $guest_login_email)
                    ->press('再設定URLを送信')

                    //バリデーションエラーメッセージが表示しているか確認
                    //失敗フラッシュメッセージ表示してあるか確認
                    ->assertSee('リクエストに失敗しました')
                    ->assertSee('簡単ログイン用のメールアドレスは変更できません');
        });
    }

    //一意ではないメールアドレスでリクエストした際、失敗するかテスト
    public function testFailureUniqueEmailReset(): void
    {
        //すでにデータがあるメールアドレスでリクエスト
        $user = factory(User::class)->create();
        $email = $user->email;

        $this->browse(function ($browser) use ($email) {
            $browser->loginAs($this->user)
                    ->visitRoute('email.request')
                    ->assertRouteIs('email.request')
                    ->type('new_email', $email)
                    ->assertInputValue('new_email', $email)
                    ->press('再設定URLを送信')

                    //バリデーションエラーメッセージが表示しているか確認
                    //失敗フラッシュメッセージ表示してあるか確認
                    ->assertSee('リクエストに失敗しました')
                    ->assertSee('メールアドレスは既に取得されているため、違うものを入力してください。');
        });
    }

    //ナビゲーションバーからメールアドレス再設定フォームにアクセスできるかテスト
    public function testEmailResetLink(): void
    {
        $this->browse(function (Browser $browser) {
           $browser->loginAs($this->user)
                ->visit('/')
                ->click('#navbarDropdown')
                ->click('.email_reset_icon')
                ->assertPathIs('/email/reset');
        });
    }

    //管理ユーザーは、メールアドレス再設定フォームにアクセスできないかテスト
    //リダイレクトの確認
    //失敗フラッシュメッセージが表示されているか確認
    public function testAdminInaccessibleEmailResetPage(): void
    {
        $admin = factory(User::class)->create([
                    'email' => 'admin@example.com',
                ]);
        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/')
                    ->visitRoute('email.request')
                    ->assertPathIs('/')
                    ->assertSee('管理ユーザーはメールアドレス再設定ができません');
        });
    }

    //ナビゲーションバーにメールアドレス再設定フォームのリンクが表示されていないかテスト
    public function testNavbarEmailResetLinkNotDisplayed(): void
    {
        $admin = factory(User::class)->create([
                    'email' => 'admin@example.com',
                ]);
        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/')
                    ->click('#navbarDropdown')
                    ->assertMissing('.email_reset_icon');
        });
    }
}
