<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use App\Post;
use App\User;
use App\PostImage;
use App\Animal;
use Tests\TestCase;

class PostLikeTest extends TestCase
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

        //一つの投稿と関係リレーションデータ同時生成
        $this->factory_user = factory(User::class)->create();
        $posts = factory(Post::class, 5)->create([
                    'user_id' => $this->factory_user->id,
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
        $this->posts = $this->factory_user->posts;

        //一つだけ投稿を取得
        foreach ($this->posts as $post) {
            $this->post = $post;
            break;
        }
    }

    public function tearDown(): void
    {
        Artisan::call('migrate:refresh');
        parent::tearDown();
    }

    //ゲストユーザーがいいねできないようになっているかテスト
    public function testGuestUserNoPostLike()
    {
        //ゲストユーザー用のいいねアイコンの表示になっているか確認
        $this->get('/')
            ->assertSee('<i class="far fa-heart fa-lg pr-2" style="color: #BBBBBB;"></i>');

        //いいねリクスト
        $this->post(route('posts.like', $this->post->id))
            ->assertStatus(302);

        //データが保存されていないことを確認
        $this->assertDatabaseMissing('likes', [
                'user_id' => $this->factory_user->id,
                'post_id' => $this->post->id,
            ]);
    }

    //いいね登録が正常に行われているか、データベースに保存してあるかテスト
    public function testtes()
    {
        //ログインユーザー用のいいねボタンの表示になっているか確認
        $this->actingAs($this->factory_user);
        $this->get('/')
            ->assertSee('<button type="submit" class="btn btn like_button far fa-heart fa-lg"></button>');

        //いいねリクスト、リダイレクトの確認
        $this->from('/')
            ->post(route('posts.like', $this->post->id))
            ->assertStatus(302)
            ->assertRedirect('/');

        //いいねした後のボタン表示に切り替わっているか確認
        $this->get('/')
            ->assertSee('<button type="submit" class="btn btn like_now_button fas fa-heart fa-lg"></button>');

        $this->assertDatabaseHas('likes', [
                'user_id' => $this->factory_user->id,
                'post_id' => $this->post->id,
            ]);

    }

    //いいね削除が正常に行われているか、データベースのテスト
    public function testDeletePostLike()
    {
        $this->actingAs($this->factory_user);

        //いいねリクスト
        $this->post(route('posts.like', $this->post->id));

        //データベースの確認
        $this->assertDatabaseHas('likes', [
                'user_id' => $this->factory_user->id,
                'post_id' => $this->post->id,
            ]);

        //いいね削除リクエスト
        $this->from('/')
            ->delete(route('posts.unlike', $this->post->id))
            ->assertStatus(302)
            ->assertRedirect('/');

        //いいね前のボタン表示に切り替わっているか確認
        $this->get('/')
            ->assertSee('<button type="submit" class="btn btn like_button far fa-heart fa-lg"></button>');

        //削除されたかデータベースの確認
        $this->assertDeleted('likes', [
                'user_id' => $this->factory_user->id,
                'post_id' => $this->post->id,
            ]);
    }

    //いいねが重複してデータベースに保存されていないかテスト
    public function testDuplicationPostLike()
    {
        $this->actingAs($this->factory_user);

        //いいねリクスト(1回目)
        $this->post(route('posts.like', $this->post->id));

        //データベースの確認
        $this->assertDatabaseHas('likes', [
                'id' => 1,
                'user_id' => $this->factory_user->id,
                'post_id' => $this->post->id,
            ]);

        //いいねリクスト(2回目)
        $this->post(route('posts.like', $this->post->id));

        //重複していないかデータベースの確認
        $this->assertDatabaseMissing('likes', [
                'id' => 2,
                'user_id' => $this->factory_user->id,
                'post_id' => $this->post->id,
            ]);
    }

    //ユーザーいいね一覧ページに、投稿が表示されているかテスト
    public function testLikePostList()
    {
        $user = $this->factory_user;
        $this->actingAs($user);

        //5投稿をいいね
        foreach ($this->posts as $post) {
            $this->post(route('posts.like', $post->id));
        }

        //認証ユーザーのいいね一覧ページへ
        $response = $this->get(route('users.likes', $user->id))
                    ->assertStatus(200)
                    ->assertViewIs('users.likes');

        //いいねした投稿が全て表示されているか確認
        foreach ($user->likes as $like) {
            $response->assertSee($like->content)
                    ->assertSee('<button type="submit" class="btn btn like_now_button fas fa-heart fa-lg"></button>');
        }
    }

    //いいねを解除して、その投稿はいいね一覧ページに表示されていないかテスト
    public function testLikePostDontShowList()
    {
        $user = $this->factory_user;
        $this->actingAs($user);
        foreach ($this->posts as $post) {
            $this->post(route('posts.like', $post->id));
        }

        //1投稿をいいね解除
        $this->delete(route('posts.unlike', $this->post->id));

        //いいね一覧ページへ。いいね解除投稿が表示されていないか確認
        $this->get(route('users.likes', $user->id))
            ->assertStatus(200)
            ->assertViewIs('users.likes')
            ->assertDontSee($this->post->content);
    }

    //いいねランキングページにゲストユーザーが
    //アクセスできないようになっているかテスト
    public function testGuestUserPostsPopularPage()
    {
        //いいねランキングページにアクセスしたが失敗し
        //ログインページにリダイレクトしたか確認
        $this->get(route('posts.popular'))
            ->assertStatus(302)
            ->assertRedirect(route('login'));
    }

    //ゲストユーザーがアクセス時に
    //トップページに切り替えボタンが表示されていないかテスト
    public function testNotSortButton()
    {
        $this->get('/')
            ->assertDontSee('人気投稿')
            ->assertDontSee('新規投稿');
    }

    //いいねが多い順で投稿が表示されているかテスト
    public function testPostsPopularPage()
    {
        factory(User::class, 5)->create();
        $users = User::all();
        $posts = Post::all();
        foreach ($users as $user) {

            //ユーザーid(1)が、いいねをする
            if ($user->id == 1) {
                $this->actingAs($user);
                foreach ($posts as $key => $post) {
                    if ($key == 5) {
                        break;
                    }
                    $this->post(route('posts.like', $post->id));
                }
                $this->post(route('logout'));
            }

            //ユーザーid(2)が、いいねをする
            if ($user->id == 2) {
                $this->actingAs($user);
                foreach ($posts as $key => $post) {
                    if ($key == 3) {
                        break;
                    }
                    $this->post(route('posts.like', $post->id));
                }
                $this->post(route('logout'));
            }

            //ユーザーid(3)が、いいねをする
            if ($user->id == 3) {
                $this->actingAs($user);
                foreach ($posts as $post) {
                    $this->post(route('posts.like', $post->id));
                    break;
                }
            }
        }

        //いいねが多い順の投稿を順番に取得
        $posts = Post::withCount('likes')->orderBy('likes_count', 'desc')->get();
        foreach ($posts as $post) {
            $data[] = $post->content;
        }

        //投稿がいいねが多い順で表示されているか取得
        $this->get(route('posts.popular'))
            ->assertSeeInOrder($data);
    }
}