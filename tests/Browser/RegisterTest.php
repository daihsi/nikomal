<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\User;

class RegisterTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * A Dusk test example.
     *
     * @return void
     */

    //ユーザー登録テスト
    public function testRegister()
    {
        $password = '123456789';
        $user = factory(User::class)->make();
        $this->browse(function ($browser) use ($user, $password) {
            $browser->visit('/register')
                    ->type('name', $user->name)
                    ->type('email', $user->email)
                    ->type('password', $password)
                    ->type('password_confirmation', $password)
                    ->press('登録する')
                    ->assertPathIs('/')
                    ->assertSee('ユーザー登録完了しました') //toastrのフラッシュメッセージが表示されているか確認
                    ->screenshot('register');
        });
    }

    //バリデーションで登録ページにリダイレクトしたかテスト
    public function testValidationRegister()
    {
        //名前欄の入力が、一文字多い
        $name = str_repeat('あ', 16);
        $password = '123456789';
        $user = factory(User::class)->make();
        $this->browse(function ($browser) use ($user, $password, $name) {
            $browser->visit('/register')
                    ->type('name', $name)
                    ->type('email', $user->email)
                    ->type('password', $password)
                    ->type('password_confirmation', $password)
                    ->press('登録する')
                    ->assertPathIs('/register')
                    ->assertSee('ユーザー登録に失敗しました') //toastrのフラッシュメッセージが表示されているか確認
                    ->screenshot('register');
        });
    }

    //ヘッダーナビゲーションから登録ページへ遷移しているかテスト
    public function testLinkRegister()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->clickLink('ユーザー登録')
                    ->assertPathIs('/register');
        });
    }
}