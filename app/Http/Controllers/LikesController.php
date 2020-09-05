<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LikesController extends Controller
{
    public function store($id)
    {
        \Auth::user()->like($id);
        return back()->with('msg_success', '投稿にいいねしました');
    }

    public function destroy($id)
    {
        \Auth::user()->like($id);
        return back()->with('msg_success', '投稿のいいねを外しました');
    }
}
