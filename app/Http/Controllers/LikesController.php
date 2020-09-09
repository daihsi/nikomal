<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;

class LikesController extends Controller
{
    public function store($id)
    {
        $user = \Auth::user();
        $like = $user->like($id);
        $post = Post::findOrFail($id);

        //いいね登録の場合
        if ($like === true) {
            $p_count = $post->likes()->count();
            $u_count = $user->likes()->count();
            return response()->json([
                            'like' => true,
                            'p_count' => $p_count,
                            'u_count' => $u_count,
                        ]);
        }

        //いいね解除の場合
        elseif ($like === false) {
            $p_count = $post->likes()->count();
            $u_count = $user->likes()->count();
            return response()->json([
                            'unlike' => false,
                            'p_count' => $p_count,
                            'u_count' => $u_count,
                        ]);
        }
    }
}