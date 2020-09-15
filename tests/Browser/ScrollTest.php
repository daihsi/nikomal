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
    public function testToppageScroll()
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
                    ->waitForText($post14)
                    ->driver
                    ->executeScript('window.scrollTo(1000, 2000);');

            //もっと見るボタン押下げ後、二ページ目の投稿を確認
            $browser->assertSee($post14)
                    ->screenshot('post');
        });
    }

    //人気投稿ページの無限スクロールテスト
    public function testPopularpageScroll()
    {
        $user = $this->users[0];
        $post1 = $this->posts[0]->content;
        $post12 = $this->posts[11]->content;
        $post14 = $this->posts[13]->content;

        //投稿のいいねデータを繰り返して保存
        foreach ($this->posts as $post) {
            $user->like($post->id);
        }

        $this->browse(function ($browser) use ($user, $post1, $post12, $post14) {

            //ページの一番上位にある投稿を確認
            $browser->loginAs($user)
                    ->visitRoute('posts.popular')
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
                    ->assertSee($post14)
                    ->screenshot('post');
        });
    }

    //検索ページの無限スクロールテスト
    public function testSearchpageScroll()
    {
        $post1 = $this->posts[0]->content;
        $post12 = $this->posts[11]->content;
        $post14 = $this->posts[13]->content;

        $this->browse(function ($browser) use ($post1, $post12, $post14) {

            //ページの一番上位にある投稿を確認
            $browser->visitRoute('posts.search')
                    ->pause(6000)
                    ->assertSee($post1)
                    ->driver
                    ->executeScript('window.scrollTo(0, 500);');

            //スクロールしてページの一番下位にある投稿を確認
            $browser->waitForText($post12)
                    ->assertSee($post12)
                    ->press('もっと見る')
                    ->waitForText($post14)
                    ->driver
                    ->executeScript('window.scrollTo(1000, 2000);');

            //もっと見るボタン押下げ後、二ページ目の投稿を確認
            $browser->assertSee($post14)
                    ->screenshot('post');
        });
    }

    //ユーザー一覧の無限スクロールテスト
    public function testUsersScroll()
    {
        $user1 = $this->users[0]->name;
        $user3 = $this->users[2]->name;
        $user14 = $this->users[13]->name;

        $this->browse(function ($browser) use ($user1, $user3, $user14) {

            //ページの一番上位にあるユーザーを確認
            $browser->visitRoute('users.index')
                    ->assertSee($user14)
                    ->driver
                    ->executeScript('window.scrollTo(0, 500);');

            //スクロールしてページの一番下位にあるユーザーを確認
            $browser->assertSee($user3)
                    ->press('もっと見る')
                    ->waitForText($user1)
                    ->driver
                    ->executeScript('window.scrollTo(1000, 2000);');

            //もっと見るボタン押下げ後、二ページ目のユーザーを確認
            $browser->assertSee($user1)
                    ->screenshot('users');
        });
    }

    //投稿詳細ページ(コメント)の無限スクロールテスト
    public function testCommentScroll()
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
                    ->assertSee($comment1)
                    ->driver
                    ->executeScript('window.scrollTo(0, 500);');

            //スクロールしてコメントを確認
            $browser->waitForText($comment6)
                    ->assertSee($comment6)
                    ->driver
                    ->executeScript('window.scrollTo(1000, 2000);');

            //スクロールしてページの一番下位にあるコメントを確認
            $browser->assertSee($comment12)
                    ->press('もっと見る')
                    ->driver
                    ->executeScript('window.scrollTo(2500, 3500);');

            //もっと見るボタン押下げ後、二ページ目のコメントを確認
            $browser->waitForText($comment14)
                    ->assertSee($comment14)
                    ->screenshot('comment');
        });
    }

    //投稿詳細ページ(いいね)の無限スクロールテスト
    public function testLikeUsersScroll()
    {
        $post = $this->posts[0];
        $user1 = $this->users[0]->name;
        $user12 = $this->users[11]->name;
        $user14 = $this->users[13]->name;

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
            $browser->screenshot('post_likes')
                    ->waitForText($user12)
                    ->assertSee($user12);
        });
    }

    //ユーザー詳細ページ(ユーザー投稿)の無限スクロールテスト
    public function testUserShowPostScroll()
    {
        $post1 = $this->posts[0]->content;
        $post7 = $this->posts[6]->content;
        $post13 = $this->posts[12]->content;

        $this->browse(function ($browser) use ($post1, $post7, $post13) {

            //ユーザー詳細ページの投稿コンテンツ
            //一番上位にある投稿を確認
            $browser->visitRoute('users.show', $this->users[0]->id)
                    ->pause(6000)
                    ->assertSee($post1)
                    ->press('もっと見る')
                    ->waitForText($post7)
                    ->driver
                    ->executeScript('window.scrollTo(0, 500);');

            //もっと見るボタン押下げ後、二ページ目の投稿を確認
            $browser->assertSee($post7)
                    ->press('もっと見る')
                    ->waitForText($post13)
                    ->driver
                    ->executeScript('window.scrollTo(1000, 2000);');

            //もっと見るボタン押下げ後、三ページ目の投稿を確認
            $browser->assertSee($post13)
                    ->screenshot('user.posts');
        });
    }

    //ユーザー詳細ページ(フォロー)の無限スクロールテスト
    public function testFollowingsScroll()
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
                    ->assertSee($user14)
                    ->screenshot('followings');
        });
    }

    //ユーザー詳細ページ(フォロワー)の無限スクロールテスト
    public function testFollowersScroll()
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
                    ->assertSee($user14)
                    ->screenshot('followers');
        });
    }

    //ユーザー詳細ページ(いいね)の無限スクロールテスト
    public function testLikePostsScroll()
    {
        $user = $this->users[0];
        $post1 = $this->posts[0]->content;
        $post12 = $this->posts[11]->content;

        //投稿のいいねデータを繰り返して保存
        foreach ($this->posts as $post) {
            $user->like($post->id);
        }
        $this->browse(function ($browser) use ($user, $post1, $post12) {

            //ユーザー詳細ページのいいね投稿コンテンツ
            //一番上位にあるいいね投稿を確認
            $browser->visitRoute('users.likes', $user->id)
                    ->pause(5000)
                    ->assertSee($post1)
                    ->press('もっと見る')
                    ->driver
                    ->executeScript('window.scrollTo(0, 1000);');

            //もっと見るボタン押下げ後、二ページ目のいいね投稿を確認
            $browser->waitForText($post12)
                    ->assertSee($post12)
                    ->screenshot('user_likes');
        });
    }
}