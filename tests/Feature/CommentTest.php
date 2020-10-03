<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Post;
use App\Comment;
use App\Http\Requests\CommentRequest;
use Tests\TestCase;

class CommentTest extends TestCase
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
        $this->factory_user = factory(User::class)->create();
        $this->post = factory(Post::class)->create([
                    'user_id' => $this->factory_user->id,
                    ]);

        //複数のコメントを生成
        $this->comments = factory(Comment::class, 5)->create([
                            'user_id' => $this->factory_user->id,
                            'post_id' => $this->post->id,
                        ]);

        //一つだけコメントを取得
        foreach ($this->comments as $comment) {
            $this->comment = $comment;
            break;
        }
    }

    public function tearDown(): void
    {
        //コメントテーブルのデータを消去
        \DB::table('comments')->truncate();
        parent::tearDown();
    }

    //コメントリクエストが通り、データベースに保存されているかテスト
    public function testNewPostComment(): void
    {
        $this->actingAs($this->factory_user);
        $post_id = $this->post->id;
        $comment = $this->comment->comment;

        //リクエストする為のデータをまとめる
        $data = [
                'post_id' => $post_id,
                'comment' => $comment,
            ];
        $url = route('posts.show', $post_id);

        //コメントリクエスト、リダイレクトの確認
        $this->from($url)
            ->post(route('posts.comment'), $data);

        //データベースにデータが存在するか確認
        $this->assertDatabaseHas('comments', $data);
    }

    //コメント削除、データベースのデータが削除されているかテスト
    public function testDeletePostComment(): void
    {
        $this->actingAs($this->factory_user);

        $post_id = $this->post->id;
        $comment_id = $this->comment->id;

        $url = route('posts.show', $post_id);

        //リクエストする為のデータをまとめる
        $data = [
                'post_id' => $post_id,
                'comment_id' => $comment_id,
                'comment_length' => 4,
            ];

        //コメント削除リクエスト
        $this->from($url)
            ->delete(route('posts.uncomment', $comment_id), $data);

        //コメントがデータベースから削除されたか確認
        $this->assertDeleted($this->comment);
    }

    //コメントリクエストが、ゲストユーザーができないようになっているかテスト
    public function testGuestUserPostComment() :void
    {
        $post_id = $this->post->id;
        $comment = factory(Comment::class)->make()->comment;

        //リクエストする為のデータをまとめる
        $data = [
                'post_id' => $post_id,
                'comment' => $comment,
            ];

        //コメントリクエスト
        $this->post(route('posts.comment'), $data);

        //データベースにデータが保存されていないか確認
        $this->assertDatabaseMissing('comments', $data);
    }

    //コメント所有者以外が、コメントを削除できないかテスト
    public function testNoDeletePostComment(): void
    {
        $post_id = $this->post->id;

        //認証ユーザーとは別のユーザーによるコメント
        $comment_factory = factory(Comment::class)->create([
                            'post_id' => $post_id,
                        ]);
        $this->actingAs($this->factory_user);

        //認証ユーザーによるコメント削除リクエスト
        $this->delete(route('posts.uncomment', $comment_factory->id));

        //データベースにデータが存在するか確認
        $this->assertDatabaseHas('comments',[
                    'comment' => $comment_factory->comment,
                ]);
    }

    //必須項目が空でリクエストされた場合のバリデーションテスト
    public function testCommentRequestNull(): void
    {
        //空でリクエストされたと仮定
        $data = [
                'post_id' => null,
                'comment' => null,
            ];
        $request = new CommentRequest;
        $rules = $request->rules();

        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();

        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'post_id' => ['Required' => [], ],
            'comment' => ['Required' => [], ],
        ];

        //どこがエラーになったか検証
        $this->assertEquals($expectedFailed, $validator->failed());
    }

    //コメントが桁あふれでリクエストされた場合のバリデーションテスト
    public function testCommentRequestOverFlow(): void
    {
        //コメントが一文字多いと仮定
        $data = [
                'post_id' => $this->post->id,
                'comment' => str_repeat('あ', 151),
            ];
        $request = new CommentRequest;
        $rules = $request->rules();

        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();

        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'comment' => ['Max' => [150], ],
        ];

        //どこがエラーになったか検証
        $this->assertEquals($expectedFailed, $validator->failed());
    }

    //投稿IDがデータベースに存在しない場合のバリデーションテスト
    public function testCommentRequestPostIdNotColumn(): void
    {
        //存在しない投稿IDでリクエストしたと仮定
        $data = [
                'post_id' => 0,
                'comment' => str_repeat('あ', 150),
            ];
        $request = new CommentRequest;
        $rules = $request->rules();

        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();

        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'post_id' => ['Exists' => ['posts', 'id'], ],
        ];

        //どこがエラーになったか検証
        $this->assertEquals($expectedFailed, $validator->failed());
    }

    //コメントが文字列でない場合のバリデーションテスト
    public function testCommentRequestNotString(): void
    {
        //コメントを文字列以外でリクエストしたと仮定
        $data = [
                'post_id' => $this->post->id,
                'comment' => 123456789,
            ];
        $request = new CommentRequest;
        $rules = $request->rules();

        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();

        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'comment' => ['String' => [], ],
        ];

        //どこがエラーになったか検証
        $this->assertEquals($expectedFailed, $validator->failed());
    }

    //バリデーションの通過テスト
    public function testCommentRequestNomal(): void
    {
        $data = [
            'post_id' => 1,
            'comment' => str_repeat('ああああああtest', 15),
        ];
        $request = new CommentRequest;
        $rules = $request->rules();

        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();

        //データが真であるか確認
        $this->assertTrue($result);
    }

    //コメントが投稿詳細ページに表示されているかテスト
    public function testCommentUersPostShow(): void
    {
        $response = $this->get(route('posts.show', $this->post->id));

        //生成したコメントが全て表示されているか確認
        foreach ($this->post->postComments as $comment) {
            $response->assertSee($comment->comment);
        }
    }

    //ゲストユーザーにはコメント入力エリアが表示されていないかテスト
    public function testCommentTextArea(): void
    {
        $this->get(route('posts.show', $this->post->id))
            ->assertDontSee('<textarea></textarea>');
    }

    //管理ユーザーが他ユーザーのコメントを削除できるかテスト
    public function testAdminDeleteComment(): void
    {
        //管理ユーザーでログイン
        $admin = factory(User::class)->create([
                    'email' => 'admin@example.com',
                ]);
        $this->actingAs($admin);

        $post_id = $this->post->id;
        $comment_id = $this->comment->id;

        $url = route('posts.show', $post_id);

        //リクエストする為のデータをまとめる
        $data = [
                'post_id' => $post_id,
                'comment_id' => $comment_id,
                'comment_length' => 4,
            ];

        //コメント削除リクエスト
        $this->from($url)
            ->delete(route('posts.uncomment', $comment_id), $data);

        //コメントがデータベースから削除されたか確認
        $this->assertDeleted($this->comment);
    }
}