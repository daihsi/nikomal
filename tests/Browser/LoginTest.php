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

    //ログインテスト
    public function testLogin()
    {
        $password = 123456789;
        $user = factory(User::class)->create([
                    'password' => bcrypt($password),
                    'remember_token' => null,
                ]);
        $this->browse(function ($browser) use ($user, $password) {
            $browser->visit('/login')
                    ->assertSee('ログイン')
                    ->type('email', $user->email)
                    ->type('password', $password)
                    ->screenshot('form')
                    ->press('ログイン')
                    ->pause(5000)
                    ->assertPathIs('/');
        });
    }

    public function testtest()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->screenshot('test')
                    ->assertSeeLink('ログイン')
                    ->clicklink('ログイン');
                    //->screenshot('test')
                    //->waitForLocation('/')
                    //->assertPathIs('/');
                    //->assertSee('テストタスク');
                    //->assertPathIs('/');
        });
    }
}
