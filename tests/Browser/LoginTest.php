<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\User;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * A Dusk test example.
     *
     * @return void
     */

    //ヘッダーナビゲーションからログインページへ遷移しているかテスト
    public function testValidationLogin()
    {
        //パスワードを間違えたと仮定
        $password = 12345678;
        $user = factory(User::class)->create();

        $this->browse(function ($browser) use($user, $password) {
            $browser->visit('/login')
                    ->type('email', $user->email)
                    ->type('password', $password)
                    ->press('ログイン')
                    ->assertPathIs('/login')
                    ->assertSee('ログイン') //ログインページのテキスト表示されているか確認
                    ->screenshot('login');
        });
    }

    //ログインテスト
    public function testLogin()
    {
        $password = 123456789;
        $user = factory(User::class)->create([
                    'password' => bcrypt($password),
                ]);
        $this->browse(function ($browser) use ($user, $password) {
            $browser->visit('/login')
                    ->type('email', $user->email)
                    ->type('password', $password)
                    ->press('ログイン')
                    ->assertPathIs('/')
                    ->assertSee('ログインしました') //toastrのフラッシュメッセージが表示されているか確認
                    ->screenshot('login');
        });
    }

    //ヘッダーナビゲーションからログインページへ遷移しているかテスト
    public function testLoginLink()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->clickLink('ログイン')
                    ->assertPathIs('/login');
        });
    }

    //ログアウトテスト(確認ダイヤログもテスト)
    public function testLogout()
    {
        $user = factory(User::class)->create();
        $this->browse(function ($browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/')
                    ->click('#navbarDropdown')
                    ->click('.logout_alert')
                    ->assertDialogOpened('ログアウトしてよろしいですか？')
                    ->acceptDialog() //ダイアログのokボタンを押す
                    ->screenshot('logout');
        });
    }
}