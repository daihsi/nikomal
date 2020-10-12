<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\User;
use App\Post;
use App\PostImage;
use App\Animal;
use App\Comment;

class ScrollTest extends DuskTestCase
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

        $this->users = factory(User::class, 14)->create();

        //投稿をリレーションデータを含めて生成
        $this->posts = factory(Post::class, 14)->create([
                    'user_id' => $this->users[0]->id,
                    ])
                    ->each(function ($post) {
                        //画像データの生成
                        $post->postImages()
                            ->save(
                                factory(PostImage::class)->make()
                            );
                        //動物カテゴリーデータの生成
                        $post->postCategorys()
                            ->createMany(
                                factory(Animal::class, 3)->make()
                                ->toArray()
                            );
                    });
    }

    //トップページの無限スクロールテスト
    public function testToppageScroll(): void
    {
        $post1 = $this->posts[0]->content;
        $post12 = $this->posts[11]->content;
        $post14 = $this->posts[13]->content;

        $this->browse(function ($browser) use ($post1, $post12, $post14) {

            //ページの一番上位にある投稿を確認
            $browser->loginAs($this->users[0])
                    ->visit('/')
                    ->waitForText($post1)
                    ->assertSee($post1)
                    ->driver
                    ->executeScript('window.scrollTo(0, 500);');

            //スクロールしてページの一番下位にある投稿を確認
            $browser->waitForText($post12)
                    ->assertSee($post12)
                    ->press('もっと見る')
                    ->driver
                    ->executeScript('window.scrollTo(1000, 2000);');

            //もっと見るボタン押下げ後、二ページ目の投稿を確認
            $browser->waitForText($post14)
                    ->assertSee($post14);
        });
    }

    //検索ページの無限スクロールテスト
    public function testSearchpageScroll(): void
    {
        $post1 = $this->posts[0]->content;
        $post12 = $this->posts[11]->content;
        $post14 = $this->posts[13]->content;

        $this->browse(function ($browser) use ($post1, $post12, $post14) {

            //ページの一番上位にある投稿を確認
            $browser->visit('/')
                    ->clickLink('検索')
                    ->pause(6000)
                    ->assertSee($post1)
                    ->driver
                    ->executeScript('window.scrollTo(0, 500);');

            //スクロールしてページの一番下位にある投稿を確認
            $browser->waitForText($post12)
                    ->assertSee($post12)
                    ->press('もっと見る')
                    ->driver
                    ->executeScript('window.scrollTo(1000, 2000);');

            //もっと見るボタン押下げ後、二ページ目の投稿を確認
            $browser->waitForText($post14)
                    ->assertSee($post14);
        });
    }

    //ユーザー一覧の無限スクロールテスト
    public function testUsersScroll(): void
    {
        //管理ユーザーの生成
        $admin = factory(User::class)->create([
                        'email' => 'admin@example.com',
                    ]);
        $user1 = $this->users[0]->name;
        $user3 = $this->users[2]->name;
        $user14 = $this->users[13]->name;

        $this->browse(function ($browser) use ($admin, $user1, $user3, $user14) {

            //ページの一番上位にあるユーザーを確認
            //管理ユーザーが一覧に表示されていないか確認
            $browser->visitRoute('users.index')
                    ->waitForText($user14)
                    ->assertSee($user14)
                    ->assertDontSee($admin->name)
                    ->driver
                    ->executeScript('window.scrollTo(0, 500);');

            //スクロールしてページの一番下位にあるユーザーを確認
            $browser->waitForText($user3)
                    ->assertSee($user3)
                    ->press('もっと見る')
                    ->driver
                    ->executeScript('window.scrollTo(1000, 2000);');

            //もっと見るボタン押下げ後、二ページ目のユーザーを確認
            //管理ユーザーが一覧に表示されていないか確認
            $browser->waitForText($user1)
                    ->assertDontSee($admin->name)
                    ->assertSee($user1);
        });
    }

    //投稿詳細ページ(コメント)の無限スクロールテスト
    public function testCommentScroll(): void
    {
        $comments = factory(Comment::class, 14)->create([
                            'user_id' => $this->users[0]->id,
                            'post_id' => $this->posts[0]->id,
                        ]);
        $comment1 = $comments[0]->comment;
        $comment6 = $comments[5]->comment;
        $comment12 = $comments[11]->comment;
        $comment14 = $comments[13]->comment;

        $this->browse(function ($browser) use ($comment1, $comment6, $comment12, $comment14) {

            //ページの一番上位にあるコメントを確認
            $browser->visitRoute('posts.show', $this->posts[0]->id)
                    ->waitForText($comment1)
                    ->assertSee($comment1)
                    ->driver
                    ->executeScript('window.scrollTo(0, 500);');

            //スクロールしてコメントを確認
            $browser->waitForText($comment6)
                    ->assertSee($comment6)
                    ->driver
                    ->executeScript('window.scrollTo(1000, 2000);');

            //スクロールしてページの一番下位にあるコメントを確認
            $browser->waitForText($comment12)
                    ->assertSee($comment12)
                    ->driver
                    ->executeScript('window.scrollTo(2500, 3500);');

            //もっと見るボタン押下げ後、二ページ目のコメントを確認
            $browser->waitForText($comment14)
                    ->assertSee($comment14);
        });
    }

    //投稿詳細ページ(いいね)の無限スクロールテスト
    public function testLikeUsersScroll(): void
    {
        $post = $this->posts[0];
        $user1 = $this->users[0]->name;
        $user12 = $this->users[11]->name;

        //投稿のいいねデータを繰り返して保存
        foreach ($this->users as $user) {
            $user->like($post->id);
        }

        $this->browse(function ($browser) use ($post, $user1, $user12) {

            //投稿詳細ページのいいねユーザーコンテンツ
            //一番上位にあるいいねユーザーを確認
            $browser->visitRoute('post.likes', $post->id)
                    ->waitForText($user1)
                    ->assertSee($user1)
                    ->driver
                    ->executeScript('window.scrollTo(0, 500);');

            //一ページ目の一番下位にあるいいねユーザーを確認
            $browser->waitForText($user12)
                    ->assertSee($user12);
        });
    }

    //ユーザー詳細ページ(ユーザー投稿)の無限スクロールテスト
    public function testUserShowPostScroll(): void
    {
        $post7 = $this->posts[6]->content;
        $post13 = $this->posts[12]->content;

        $this->browse(function ($browser) use ($post7, $post13) {

            //ユーザー詳細ページの投稿コンテンツ
            //一番上位にある投稿を確認
            $browser->visitRoute('users.show', $this->users[0]->id)
                    ->pause(5000)
                    ->press('もっと見る')
                    ->driver
                    ->executeScript('window.scrollTo(0, 500);');

            //もっと見るボタン押下げ後、二ページ目の投稿を確認
            $browser->waitForText($post7)
                    ->assertSee($post7)
                    ->press('もっと見る')
                    ->driver
                    ->executeScript('window.scrollTo(1000, 2000);');

            //もっと見るボタン押下げ後、三ページ目の投稿を確認
            $browser->waitForText($post13)
                    ->assertSee($post13);
        });
    }

    //ユーザー詳細ページ(フォロー)の無限スクロールテスト
    public function testFollowingsScroll(): void
    {
        $user1 = $this->users[0];
        $user2 = $this->users[1]->name;
        $user13 = $this->users[12]->name;
        $user14 = $this->users[13]->name;

        //フォローデータ繰り返して保存
        foreach ($this->users as $user) {
            if ($user->id === 1) {
                continue;
            }
            $user1->follow($user->id);
        }
        $this->browse(function ($browser) use ($user1, $user2, $user13, $user14) {

            //ページの一番上位にあるフォローユーザーを確認
            $browser->visitRoute('users.followings', $user1->id)
                    ->waitForText($user2)
                    ->assertSee($user2)
                    ->driver
                    ->executeScript('window.scrollTo(0, 500);');

            //スクロールしてページの一番下位にあるフォローユーザーを確認
            $browser->waitForText($user13)
                    ->assertSee($user13)
                    ->press('もっと見る')
                    ->driver
                    ->executeScript('window.scrollTo(500, 1500);');

            //もっと見るボタン押下げ後、二ページ目のフォローユーザーを確認
            $browser->waitForText($user14)
                    ->assertSee($user14);
        });
    }

    //ユーザー詳細ページ(フォロワー)の無限スクロールテスト
    public function testFollowersScroll(): void
    {
        $user1 = $this->users[0];
        $user2 = $this->users[1]->name;
        $user13 = $this->users[12]->name;
        $user14 = $this->users[13]->name;

        //フォロワーデータ繰り返して保存
        foreach ($this->users as $user) {
            if ($user->id === 1) {
                continue;
            }
            $user->follow($user1->id);
        }

        $this->browse(function ($browser) use ($user1, $user2, $user13, $user14) {

            //ページの一番上位にあるフォロワーユーザーを確認
            $browser->visitRoute('users.followers', $user1->id)
                    ->waitForText($user2)
                    ->assertSee($user2)
                    ->driver
                    ->executeScript('window.scrollTo(0, 500);');

            //スクロールしてページの一番下位にあるフォロワーユーザーを確認
            $browser->waitForText($user13)
                    ->assertSee($user13)
                    ->press('もっと見る')
                    ->driver
                    ->executeScript('window.scrollTo(500, 1500);');

            //もっと見るボタン押下げ後、二ページ目のフォロワーユーザーを確認
            $browser->waitForText($user14)
                    ->assertSee($user14);
        });
    }

    //ユーザー詳細ページ(いいね)の無限スクロールテスト
    public function testLikePostsScroll(): void
    {
        $user = $this->users[0];
        $post1 = $this->posts[0]->content;
        $post10 = $this->posts[9]->content;

        //投稿のいいねデータを繰り返して保存
        foreach ($this->posts as $post) {
            $user->like($post->id);
        }
        $this->browse(function ($browser) use ($user, $post1, $post10) {

            //ユーザー詳細ページのいいね投稿コンテンツ
            //一番上位にあるいいね投稿を確認
            $browser->visitRoute('users.likes', $user->id)
                    ->pause(6000)
                    ->assertSee($post1)
                    ->press('もっと見る')
                    ->driver
                    ->executeScript('window.scrollTo(0, 1000);');

            //もっと見るボタン押下げ後、二ページ目のいいね投稿を確認
            $browser->waitForText($post10)
                    ->assertSee($post10);
        });
    }
}