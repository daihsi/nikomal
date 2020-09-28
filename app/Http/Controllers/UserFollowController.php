<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserFollowController extends Controller
{
    public function store($id)
    {

        //管理ユーザーはこちらの処理に入れない
        if (!\Gate::allows('admin')) {
            $user = \Auth::user();
            $auth_id = $user->id;
            $follow = $user->follow($id);
            $follower_count = User::findOrFail($id)->followers()->count();

            //trueはフォロー、falseはアンフォロー
            $auth_follow_count = $user->followings()->count();
            $auth_follower_count = $user->followers()->count();
            return response()->json([
                            'follow' => $follow == true ?? $follow == false,
                            'follower_count' => $follower_count,
                            'auth_follow_count' => $auth_follow_count,
                            'auth_follower_count' => $auth_follower_count,
                            'auth_id' => $auth_id,
                        ]);
        }
        else {
            return response()->json([
                        'error' => '管理ユーザーはフォローができません',
                    ]);
        }
    }
}