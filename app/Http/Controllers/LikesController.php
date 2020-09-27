<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;

class LikesController extends Controller
{
    public function store($id)
    {
        $user = \Auth::user();
        $auth_id = $user->id;
        $like = $user->like($id);
        $post = Post::findOrFail($id);

        //いいね登録はtrue,いいね解除はfalseを返す
        $p_count = $post->likes()->count();
        $u_count = $user->likes()->count();
        return response()->json([
                        'like' => $like == true ?? $like == false,
                        'p_count' => $p_count,
                        'u_count' => $u_count,
                        'auth_id' => $auth_id,
                    ]);
    }
}