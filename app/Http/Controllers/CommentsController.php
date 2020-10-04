<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use Illuminate\Http\Request;
use App\Post;
use App\Comment;

class CommentsController extends Controller
{
    /**
     * 認証ユーザーからの、投稿コメントリクエスト
     * 
     * @param \App\Http\Requests\CommentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CommentRequest $request)
    {
        $user = auth()->user();

        $post_id = $request->post_id;
        $comment = $request->comment;

        //Commentモデルにリクエストを保存
        $user->userComments()->create([
                'post_id' => $post_id,
                'comment' => $comment,
            ]);

        //投稿のコメントを取得
        $comments = Post::findOrFail($post_id)->postComments();

        //最新のコメント一件を取得
        $comment = $comments
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->first();

        //コメントのカウントを取得
        $comment_count = $comments
                        ->count();

        return response()->json([
                    'comment' => $comment,
                    'comment_count' => $comment_count,
                    ]);
    }

    /**
     * コメント所有ユーザーからの、コメント削除リクエスト
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        //管理ユーザーとしてログインしているか
        $admin = \Gate::allows('admin');

        $comment_id = $request->comment_id;
        $comment_length = $request->comment_length;
        $post_id = $request->post_id;

        //コメントをidから特定
        $select_comment = Comment::findOrFail($comment_id);
        $auth_id = \Auth::id();

        //認証ユーザーidとコメントユーザーidを比較
        //または管理ユーザーであれば削除処理に入る
        if ($auth_id === $select_comment->user_id || \Gate::allows('admin')) {

            //コメント削除
            $select_comment->delete();

            //投稿のコメントを取得
            $comment = Post::findOrFail($post_id)
                        ->postComments()
                        ->with('user')
                        ->orderBy('created_at', 'desc')
                        ->skip($comment_length - 1)
                        ->first();

            //コメントカウント取得
            $comment_count = $select_comment->post->postComments->count();

            return response()->json([
                    'comment' => $comment,
                    'auth_id' => $auth_id,
                    'comment_count' => $comment_count,
                    'admin' => $admin,
                    ]);
        }
        else {
            return response()->json([
                        'message' => 'コメントを削除できません',
                    ]);
        }
    }
}