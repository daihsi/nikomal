<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;

class PostsPopularController extends Controller
{
    //いいねが多い順の投稿一覧
    public function index()
    {
        $posts = Post::withCount('postImages', 'postCategorys', 'likes')->orderBy('likes_count', 'desc')->simplePaginate(12);
        return view('posts.popular', [
            'posts' => $posts,
        ]);
    }
}
