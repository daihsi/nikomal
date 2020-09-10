<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserFollowController extends Controller
{
    public function store($id) {
        $user = \Auth::user();
        $auth_id = $user->id;
        $follow = $user->follow($id);

        //フォローの場合
        if ($follow === true) {
            $follow_count = $user->followings()->count();
            $follower_count = $user->followers()->count();
            return response()->json([
                            'follow' => true,
                            'follow_count' => $follow_count,
                            'follower_count' => $follower_count,
                            'auth_id' => $auth_id,
                        ]);
        }

        //アンフォローの場合
        elseif ($follow === false) {
            $follow_count = $user->followings()->count();
            $follower_count = $user->followers()->count();
            return response()->json([
                            'unfollow' => false,
                            'follow_count' => $follow_count,
                            'follower_count' => $follower_count,
                            'auth_id' => $auth_id,
                        ]);
        }
    }
}