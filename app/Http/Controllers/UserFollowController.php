<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserFollowController extends Controller
{
    public function store($id) {
        $user = \Auth::user();
        $auth_id = $user->id;
        $follow = $user->follow($id);
        $follower_count = User::findOrFail($id)->followers()->count();

        //フォローの場合
        if ($follow === true) {
            $auth_follow_count = $user->followings()->count();
            $auth_follower_count = $user->followers()->count();
            return response()->json([
                            'follow' => true,
                            'follower_count' => $follower_count,
                            'auth_follow_count' => $auth_follow_count,
                            'auth_follower_count' => $auth_follower_count,
                            'auth_id' => $auth_id,
                        ]);
        }

        //アンフォローの場合
        elseif ($follow === false) {
            $auth_follow_count = $user->followings()->count();
            $auth_follower_count = $user->followers()->count();
            return response()->json([
                            'unfollow' => false,
                            'follower_count' => $follower_count,
                            'auth_follow_count' => $auth_follow_count,
                            'auth_follower_count' => $auth_follower_count,
                            'auth_id' => $auth_id,
                        ]);
        }
    }
}