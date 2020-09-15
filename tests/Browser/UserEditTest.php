<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\User;

class UserEditTest extends DuskTestCase
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

        $this->auth_user = factory(User::class)->create();
        $this->user = factory(User::class)->make();
    }

    //ユーザー編集テスト
    public function testUserEdit()
    {
        $auth_self_introduction = $this->auth_user->self_introduction;
        $auth_name = $this->auth_user->name;
        $self_introduction = $this->user->self_introduction;
        $name = $this->user->name;

        $this->browse(function ($browser) use ($auth_self_introduction, $auth_name, $self_introduction, $name) {
            $browser->loginAs($this->auth_user)
                    ->visitRoute('users.edit', $this->auth_user)

                    //既存データの確認
                    ->assertInputValue('name', $auth_name)
                    ->assertInputValue('self_introduction', $auth_self_introduction)

                    //編集データの挿入
                    ->type('name', $name)
                    ->type('self_introduction', $self_introduction)

                    //編集データの確認
                    ->assertInputValue('name', $name)
                    ->assertInputValue('self_introduction', $self_introduction)
                    ->press('変更内容を保存する')
                    ->pause(1000)

                    //toastrのフラッシュメッセージの確認
                    ->assertSee('変更を保存しました')
                    ->assertRouteIs('users.show', $this->auth_user->id)

                    //ユーザーデータが変更してあるか確認
                    ->assertSee($name)
                    ->assertSee($self_introduction)
                    ->screenshot('user_edit');
        });
    }

    //ユーザー編集バリデーション通過しなかった際のテスト
    public function testValidationUserEdit()
    {
        $auth_self_introduction = $this->auth_user->self_introduction;
        $auth_name = $this->auth_user->name;
        $self_introduction = str_repeat('あ', 151);
        $name = str_repeat('あ', 17);

        $this->browse(function ($browser) use ($auth_self_introduction, $auth_name, $self_introduction, $name) {
            $browser->loginAs($this->auth_user)
                    ->visitRoute('users.edit', $this->auth_user)

                    //既存データの確認
                    ->assertInputValue('name', $auth_name)
                    ->assertInputValue('self_introduction', $auth_self_introduction)

                    //編集データの挿入
                    ->type('name', $name)
                    ->type('self_introduction', $self_introduction)
                    ->press('変更内容を保存する')
                    ->pause(1000)

                    //toastrのフラッシュメッセージの確認
                    //失敗してリダイレクトしたことの確認
                    ->assertSee('ユーザー編集に失敗しました')
                    ->assertRouteIs('users.edit', $this->auth_user->id)

                    //ユーザー情報が変更されていないか確認
                    ->visitRoute('users.show', $this->auth_user->id)
                    ->assertSee($auth_self_introduction)
                    ->assertSee($auth_name)
                    ->screenshot('user_edit');
        });
    }
}