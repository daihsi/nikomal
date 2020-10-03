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
    public function testUserFollowAction(): void
    {
        $this->response->post(route('user.follow', $this->facker_user2->id));

        //中間テーブルにフォロー関係のデータが正常に保存されているか
        $this->assertDatabaseHas('user_follow', [
                'user_id' => $this->facker_user1->id,
                'follow_id' => $this->facker_user2->id
            ]);
        $this->response->post(route('user.follow', $this->facker_user2->id));

        //中間テーブルにフォロー関係のデータが正常に削除されているか
        $this->assertDatabaseMissing('user_follow', [
                'user_id' => $this->facker_user1->id,
                'follow_id' => $this->facker_user2->id,
            ]);
    }

    //フォローの重複が起こっていないかテスト
    public function testUserFollowNoDuplication(): void
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
    public function testGuestUserNoFollow(): void
    {
        $this->post(route('logout'));
        $this->assertGuest();

        $this->response->post(route('user.follow', $this->facker_user2->id));
        $this->assertDatabaseMissing('user_follow', [
                'id' => 1,
            ]);
    }

    //認証ユーザー自身にフォローボタンが表示されていないかテスト
    public function testUserHimselfNoFollowButton(): void
    {
        $this->get(route('users.show', $this->facker_user1->id))
            ->assertDontSee('<button type="submit" class="btn btn-outline-primary btn-sm rounded-pill action_follow">フォロー</button>')
            ->assertDontSee('<span class="follow_now_button">フォロー中</span><');
    }

    //ゲストユーザーが各ページにアクセスしてもフォローボタンが表示されないかテスト
    public function testGuestUserNoFollowButton(): void
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
    public function testFollowingUser(): void
    {
        $this->response->post(route('user.follow', $this->facker_user2->id));
        $this->response->post(route('user.follow', $this->facker_user3->id));

        //フォローの名前が表示されているか
        $this->get(route('users.followings', $this->facker_user1->id))
            ->assertSeeText($this->facker_user2->name)
            ->assertSeeText($this->facker_user3->name);
    }

    //フォロワー一覧ページにユーザーが表示されているかテスト
    public function testFollowerUser(): void
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

    //管理ユーザーがフォローしようとして失敗するかテスト
    //レスポンスに失敗メッセージが含まれているか確認
    public function testCannotAdminUserFollow(): void
    {
        $admin = factory(User::class)->create([
                    'email' => 'admin@example.com',
                ]);
        $error = ['error' => '管理ユーザーはフォローができません'];
        $this->actingAs($admin)
            ->post(route('user.follow', $this->facker_user1->id))
            ->assertJson($error);
    }
}
