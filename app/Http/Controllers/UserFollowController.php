<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserFollowController extends Controller
{
    public function store($id) {
        \Auth::user()->follow($id);
        return back()->with('msg_success', 'フォローしました');
    }

    public function destroy($id) {
        \Auth::user()->unfollow($id);
        return back()->with('msg_success', 'フォローを外しました');
    }
}