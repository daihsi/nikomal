<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\User;
use App\Post;
use App\Comment;

class CommentTest extends DuskTestCase
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
        $this->factory_user = factory(User::class)->create();
        $this->post = factory(Post::class)->create([
                    'user_id' => $this->factory_user->id,
                    ]);

        //複数のコメントを生成
        $this->comments = factory(Comment::class, 2)->make([
                            'user_id' => $this->factory_user->id,
                            'post_id' => $this->post->id,
                        ]);
    }

    //コメント投稿テスト
    public function testCreateComment(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->factory_user)
                    ->visitRoute('posts.show', $this->post);

            //繰り返してコメントを投稿、投稿されたか確認
            foreach ($this->comments as $comment) {
                $browser->type('comment', $comment->comment)
                        ->press('コメントする')
                        ->pause(1000)

                        //コメントがコンテンツに存在するか確認
                        ->assertSee($comment->comment)

                        //toastrのフラッシュメッセージが表示されているか確認
                        ->assertSee('コメント投稿しました'); 
            }
            $browser->screenshot('comment');
        });
    }

    //コメント未入力での挙動テスト
    public function testNoComment(): void
    {
        $this->browse(function (Browser $browser) {

            //未入力であることを明示するフラッシュメッセージが表示してあるか確認
            $browser->loginAs($this->factory_user)
                    ->visitRoute('posts.show', $this->post)

                    //テキストエリアを未入力
                    ->type('comment', '')
                    ->press('コメントする')

                    //toastrのフラッシュメッセージが表示されているか確認
                    ->assertSee('コメントが未入力です')
                    ->screenshot('comment');
        });
    }

    //コメント削除テスト
    public function testCommentDelete(): void
    {
        $comment = factory(Comment::class)->create([
                            'user_id' => $this->factory_user->id,
                            'post_id' => $this->post->id,
                        ]);
        $this->browse(function ($browser) use ($comment) {
            $browser->loginAs($this->factory_user)
                    ->visitRoute('posts.show', $this->post)

                    //コメントがコンテンツに存在するか確認
                    ->assertSee($comment->comment)
                    ->click('.comment_delete')
                    ->acceptDialog()
                    ->pause(1000)

                    //コメントがコンテンツに存在してないか確認
                    ->assertDontSee($comment->comment)

                    //toastrのフラッシュメッセージが表示されているか確認
                    ->assertSee('コメントを削除しました')

                     //コメント0状態の表示になっているか確認
                    ->assertSee('まだコメントがありません')
                    ->screenshot('comment');
        });
    }

    //ナビゲーションタブのコメントカウントテスト
    public function testCommentCount(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->factory_user)
                    ->visitRoute('posts.show', $this->post)

                    //コメントがないのでカウントが0であることを確認
                    ->assertSourceHas('<span class="badge badge-white badge-pill p_comment_count_badge">0</span>')
                    ->type('comment', $this->comments[0]->comment)
                    ->press('コメントする')
                    ->pause(1000)

                    //コメントしたのでカウントが1になったことを確認
                    ->assertSourceHas('<span class="badge badge-white badge-pill p_comment_count_badge">1</span>')
                    ->click('.comment_delete')
                    ->acceptDialog()
                    ->pause(1000)

                    //コメント削除したのでカウントが0になったことを確認
                    ->assertSourceHas('<span class="badge badge-white badge-pill p_comment_count_badge">0</span>');
        });
    }

    //認証ユーザーか認証ユーザー以外のコメントスタイル表示テスト
    public function testCommentStyle(): void
    {
        $user = factory(User::class)->create();
        $comment = factory(Comment::class)->make([
                            'user_id' => $user->id,
                            'post_id' => $this->post->id,
                        ]);

        $this->browse(function ($first, $second) use ($user, $comment) {

            //ユーザー1でログインしコメント投稿
            //認証ユーザー用のコメントのスタイルで表示されているか確認
            $first->loginAs($this->factory_user)
                ->visitRoute('posts.show', $this->post)
                ->type('comment', $this->comments[0]->comment)
                ->press('コメントする')
                ->pause(1000)

                //認証ユーザー用のコメントか確認
                ->assertPresent('.authenticated_user_comment');

            //ユーザー2でログインしコメント投稿
            //ユーザー1が投稿したコメントが認証ユーザー以外のスタイルで表示されているか確認
            $second->loginAs($user)
                ->visitRoute('posts.show', $this->post)

                //認証ユーザー以外のコメントが表示されており
                //認証ユーザーのスタイルコメントが表示されていないか確認
                ->assertPresent('.user_comment')
                ->assertMissing('.authenticated_user_comment')
                ->type('comment', $comment->comment)
                ->press('コメントする')
                ->pause(1000)

                //コメント投稿して、認証ユーザー用のコメントスタイルで表示されているか確認
                ->assertPresent('.authenticated_user_comment')
                ->screenshot('comment');
        });
    }

    //管理ユーザーでログイン
    //コメント投稿、他ユーザーコメントの削除ができるかテスト
    public function testAdminComment(): void
    {
        $admin = factory(User::class)->create([
                    'email' => 'admin@example.com',
                ]);
        $comment = factory(Comment::class)->make();
        factory(Comment::class, 3)->create([
                'user_id' => $this->factory_user->id,
                'post_id' => $this->post->id,
            ]);
        $this->browse(function ($browser) use ($admin, $comment) {

            //管理ユーザーでコメント投稿
            $browser->loginAs($admin)
                    ->visitRoute('posts.show', $this->post)
                    ->type('comment', $comment->comment)
                    ->press('コメントする')
                    ->pause(500)
                    ->assertSee($comment->comment)
                    ->assertSee('コメント投稿しました')
                    ->screenshot('comment');

            //管理ユーザーの投稿したコメント削除
            $browser->click('.comment_delete')
                    ->acceptDialog()
                    ->pause(500)
                    ->assertDontSee($comment->comment)
                    ->assertSee('コメントを削除しました')
                    ->screenshot('comment');

            //管理ユーザーのコメントがなくなったいま
            //他ユーザーのコメントを削除するためのボタンが存在するか確認
            //管理ユーザーが他のユーザーのコメントを削除
            $browser->assertPresent('.comment_delete')
                    ->click('.comment_delete')
                    ->acceptDialog()
                    ->pause(500)
                    ->assertDontSee($this->comments[0]->comment)
                    ->assertSee('コメントを削除しました')
                    ->screenshot('comment');
        });
    }
}