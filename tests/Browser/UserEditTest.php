<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\User;
use App\Post;
use App\Comment;

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
    public function testUserEdit(): void
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
    public function testValidationUserEdit(): void
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

    //管理ユーザー以外がログインしてユーザー削除ボタンが現れていないかテスト
    public function testNotDisplayedUserDeleteButton(): void
    {
        $users = factory(User::class, 2)->create();

        //ユーザー一覧、ユーザー詳細ページでボタンが現れてないか確認
        $this->browse(function ($browser) use ($users) {
            $browser->loginAs($users[0])
                    ->visitRoute('users.index')
                    ->assertMissing('.user_delete_alert')
                    ->visitRoute('users.show', $users[1]->id)
                    ->assertMissing('.user_delete_alert')
                    ->screenshot('user_delete');
        });
    }

    //管理ユーザーによるユーザー削除テスト
    public function testAdminDeleteUser(): void
    {
        $admin = factory(User::class)->create([
                    'email' => 'admin@example.com',
                ]);
        $users = factory(User::class, 2)->create([
                    'avatar' => null,
                ]);

        $this->browse(function ($browser) use ($admin, $users) {

            //ユーザー一覧ページにアクセス
            //削除ボタンが現れているか確認
            //削除ボタン押下げ後、ダイヤログ確認のokボタンを押して削除
            //成功フラッシュメッセージが表示されているか確認
            $browser->loginAs($admin)
                    ->visitRoute('users.index')
                    ->assertRouteIs('users.index')
                    ->assertPresent('.user_delete_alert')
                    ->click('.user_delete_alert')
                    ->acceptDialog()
                    ->pause(500)
                    ->assertSee('「'.$users[1]->name.'」のアカウントを削除しました')
                    ->assertRouteIs('users.index')
                    ->screenshot('user_delete');

            //ユーザー詳細ページにアクセス
            //削除ボタンが現れているか確認
            //削除ボタン押下げ後、ダイヤログ確認のokボタンを押して削除
            //成功フラッシュメッセージが表示されているか確認
            $browser->visitRoute('users.show', $users[0]->id)
                    ->assertRouteIs('users.show', $users[0]->id)
                    ->assertPresent('.user_delete_alert')
                    ->press('削除')
                    ->acceptDialog()
                    ->pause(500)
                    ->assertSee('「'.$users[0]->name.'」のアカウントを削除しました')
                    ->assertPathIs('/')
                    ->screenshot('user_delete');

            //削除したユーザーがコンテンツに表示していないか確認
            $browser->visitRoute('users.index')
                    ->assertDontSee($users[1]->name)
                    ->assertDontSee($users[0]->name);

            //簡単ログインユーザーを生成
            //簡単ログインユーザーは削除できなようボタンが現れていないか確認
            $guest_login_user = factory(User::class)->create([
                                'email' => 'guest@example.com',
                            ]);
            $browser->visitRoute('users.show', $guest_login_user->id)
                    ->assertMissing('.user_delete_alert');
        });
    }
}