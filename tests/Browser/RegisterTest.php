<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\User;

class RegisterTest extends DuskTestCase
{
    //use DatabaseMigrations;
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
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->screenshot('Register')
                    ->type('name', 'test1')
                    ->type('email', 'test1@test1.com')
                    ->type('password', '11111111')
                    ->type('password_confirmation', '11111111')
                    ->press('登録する');
                    //->assertPathIs('/');
        });
    }

    //バリデーションに引っ掛かった際のテスト
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
                    ->screenshot('test')
                    ->press('登録する')
                    ->assertPathIs('/register');
                    //->dump();
        });
    }
}
