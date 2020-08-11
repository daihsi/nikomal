<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;


class UserFollowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function setUp(): void
    {
        parent::setUp();

        // テストユーザー作成、認証
        $this->facker_user1 = factory(User::class)->create();
        $this->facker_user2 = factory(User::class)->create();
        $this->facker_user3 = factory(User::class)->create();
        $this->response = $this->actingAs($this->facker_user1);
    }

    //フォロー・アンフォローの動作、データベースに正常に保存してあるかテスト
    public function testUserFollowAction()
    {
        $this->response->post(route('user.follow', $this->facker_user2->id));

        //中間テーブルにフォロー関係のデータが正常に保存されているか
        $this->assertDatabaseHas('user_follow', [
                'user_id' => $this->facker_user1->id,
                'follow_id' => $this->facker_user2->id
            ]);
        $this->response->delete(route('user.unfollow', $this->facker_user2->id));

        //中間テーブルにフォロー関係のデータが正常に削除されているか
        $this->assertDatabaseMissing('user_follow', [
                'user_id' => $this->facker_user1->id,
                'follow_id' => $this->facker_user2->id,
            ]);
    }

    //フォローの重複が起こっていないかテスト
    public function testUserFollowNoDuplication()
    {
        $this->response->post(route('user.follow', $this->facker_user2->id));

        //中間テーブルにフォロー関係のデータが正常に保存されているか
        $this->assertDatabaseHas('user_follow', [
                'id' => 2,
                'user_id' => $this->facker_user1->id,
                'follow_id' => $this->facker_user2->id
            ]);
        $this->response->post(route('user.follow', $this->facker_user2->id));

        //中間テーブルに重複したデータが入っていないか
        $this->assertDatabaseMissing('user_follow', [
                'id' => 3,
                'user_id' => $this->facker_user1->id,
                'follow_id' => $this->facker_user2->id
            ]);
    }

    //ゲストユーザーがフォローリクエストしてもデータが保存されていないかテスト
    public function testGuestUserNoFollow()
    {
        $this->post(route('logout'));
        $this->assertGuest();

        $this->response->post(route('user.follow', $this->facker_user2->id));
        $this->assertDatabaseMissing('user_follow', [
                'id' => 1,
            ]);
    }

    //アクション後のフォローボタンの切り替わりが正常かテスト
    public function testFollowButton()
    {
        $url = route('users.show', $this->facker_user2->id);

        //ボタンが切り替わる前の確認
        $this->get($url)
            ->assertViewIs('users.show')
            ->assertSee('<button type="submit" class="btn btn-outline-primary btn-sm rounded-pill action_follow">フォロー</button>');

        //リクエスト後のリダイレクトを確認
        $this->response->from($url)
            ->post(route('user.follow', $this->facker_user2->id))
            ->assertStatus(302)
            ->assertRedirect($url);

        //フォローボタンが切り替わったか確認
        $this->get($url)
            ->assertViewIs('users.show')
            ->assertSee('<span class="follow_now_button">フォロー中</span><');
    }

    //認証ユーザー自身にフォローボタンが表示されていないかテスト
    public function testUserHimselfNoFollowButton()
    {
        $this->get(route('users.show', $this->facker_user1->id))
            ->assertDontSee('<button type="submit" class="btn btn-outline-primary btn-sm rounded-pill action_follow">フォロー</button>')
            ->assertDontSee('<span class="follow_now_button">フォロー中</span><');
    }

    //ゲストユーザーが各ページにアクセスしてもフォローボタンが表示されないかテスト
    public function testGuestUserNoFollowButton()
    {
        //ログアウトリクエスト
        $this->post(route('logout'));
        $this->assertGuest();

        //登録ユーザーの詳細ページにアクセスしても、フォローボタンが表示されないか
        $this->get(route('users.show', $this->facker_user1->id))
            ->assertDontSee('<button type="submit" class="btn btn-outline-primary btn-sm rounded-pill action_follow">フォロー</button>')
            ->assertDontSee('<span class="follow_now_button">フォロー中</span><');

        //ユーザー一覧ページにアクセスしても、フォローボタンが表示されないか
        $this->get(route('users.index'))
            ->assertDontSee('<button type="submit" class="btn btn-outline-primary btn-sm rounded-pill action_follow">フォロー</button>')
            ->assertDontSee('<span class="follow_now_button">フォロー中</span><');
    }

    //フォロー一覧ページにユーザーが表示されているかテスト
    public function testFollowingUser()
    {
        $this->response->post(route('user.follow', $this->facker_user2->id));
        $this->response->post(route('user.follow', $this->facker_user3->id));

        //フォローの名前が表示されているか
        $this->get(route('users.followings', $this->facker_user1->id))
            ->assertSeeText($this->facker_user2->name)
            ->assertSeeText($this->facker_user3->name);
    }

    //フォロワー一覧ページにユーザーが表示されているかテスト
    public function testFollowerUser()
    {
        $this->post(route('logout'));
        $response = $this->actingAs($this->facker_user2);
        $response->post(route('user.follow', $this->facker_user1->id));
        $this->post(route('logout'));
        $response = $this->actingAs($this->facker_user3);
        $response->post(route('user.follow', $this->facker_user1->id));

        $this->get(route('users.followers', $this->facker_user1->id))
            ->assertSeeText($this->facker_user2->name)
            ->assertSeeText($this->facker_user3->name);
    }
}
