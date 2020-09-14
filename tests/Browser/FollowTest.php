<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\User;

class FollowTest extends DuskTestCase
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

        $this->users = factory(User::class, 2)->create();
        $this->auth_user = $this->users[0];
    }

    //ユーザー一覧ページのフォローとアンフォローのテスト
    public function testFollow()
    {
        $follow_button = '.follow';
        $unfollow_button = '.follow_button';

        $this->browse(function ($first, $second) use ($follow_button, $unfollow_button){

            //フォローをして、ボタンが切り替わっているか確認
            //フラッシュメッセージも確認
            $first->loginAs($this->auth_user)
                    ->visitRoute('users.index')
                    ->click($follow_button)
                    ->waitFor($unfollow_button)
                    ->waitForText('フォロー中')
                    ->assertSee('フォローしました') //toastrのフラッシュメッセージが表示されているか確認
                    ->screenshot('follow');

            //フォロー解除をして、ボタンが切り替わっているか確認
            //フラッシュメッセージも確認
            $second->loginAs($this->auth_user)
                    ->visitRoute('users.index')
                    ->click($follow_button)
                    ->waitUntilMissing($unfollow_button)
                    ->waitForText('フォロー')
                    ->assertSee('フォローを外しました') //toastrのフラッシュメッセージが表示されているか確認
                    ->screenshot('follow');
        });
    }

    //ユーザー詳細ページのフォローのカウントテスト
    public function testFollowingsCount()
    {
        $follow_button = '.follow';
        $unfollow_button = '.follow_button';

        $this->browse(function ($browser) use ($follow_button, $unfollow_button) {
            $browser->loginAs($this->auth_user)
                    ->visitRoute('users.index')
                    ->click($follow_button)
                    ->waitFor($unfollow_button);
            $browser->visitRoute('users.followings', $this->auth_user->id)

                    //フォローがカウント1になっていることを確認
                    ->assertSourceHas('<span class="badge badge-white badge-pill follow_count_badge">1</span>')
                    ->click($follow_button)
                    ->waitUntilMissing($unfollow_button)

                    //フォローがカウント0になっていることを確認
                    ->assertSourceHas('<span class="badge badge-white badge-pill follow_count_badge">0</span>')
                    ->click($follow_button)
                    ->waitFor($unfollow_button)

                    //フォローがカウント1になっていることを確認
                    ->assertSourceHas('<span class="badge badge-white badge-pill follow_count_badge">1</span>')
                    ->screenshot('follow');
        });
    }

    //ユーザー詳細ページのフォロワーのカウントテスト
    public function testFollowersCount()
    {
        $follow_button = '.follow';
        $unfollow_button = '.follow_button';

        $this->browse(function ($browser) use ($follow_button, $unfollow_button) {
            $browser->loginAs($this->auth_user)
                    ->visitRoute('users.index')
                    ->click($follow_button)
                    ->waitFor($unfollow_button);
            $browser->visitRoute('users.followers', $this->users[1]->id)

                    //フォロワーがカウント1になっていることを確認
                    ->assertSourceHas('<span class="badge badge-white badge-pill follower_count_badge">1</span>')
                    ->press('フォロー中')
                    ->waitUntilMissing($unfollow_button)

                    //フォロワーがカウント0になっていることを確認
                    ->assertSourceHas('<span class="badge badge-white badge-pill follower_count_badge">0</span>')
                    ->press('フォロー')
                    ->pause(1000)

                    //フォロワーがカウント1になっていることを確認
                    ->assertSourceHas('<span class="badge badge-white badge-pill follower_count_badge">1</span>')
                    ->screenshot('follow');
        });
    }
}