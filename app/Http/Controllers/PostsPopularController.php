<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;

class PostsPopularController extends Controller
{
    /**
     * いいねが多い順の投稿一覧
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $posts = Post::withCount('likes')
                    ->having('likes_count', '>=', 1)
                    ->orderBy('likes_count', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->simplePaginate(12);
        return view('posts.popular', [
            'posts' => $posts,
        ]);
    }
}
