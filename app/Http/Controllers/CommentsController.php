<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Post;
use App\Comment;

class CommentsController extends Controller
{
    //認証ユーザーからの、投稿コメントリクエスト
    public function store(CommentRequest $request)
    {
        if (\Auth::check()) {
            $user = auth()->user();

            //Commentモデルにリクエストを保存
            $user->userComments()->create([
                    'post_id' => $request->post_id,
                    'comment' => $request->comment,
                ]);
            return back();
        }
        else {
            return back();
        }
    }

    //コメント所有ユーザーからの、コメント削除リクエスト
    public function destroy($id)
    {
        //コメントをidから特定
        $comment = Comment::findOrFail($id);

        //認証ユーザーidとコメントユーザーidを比較
        if (\Auth::id() === $comment->user_id) {
            $comment->delete();
            return back();
        }
        else {
            return back();
        }
    }
}
