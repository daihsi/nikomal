<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostSearchRequest;
use Illuminate\Http\Request;
use App\Post;
use App\Animal;

class PostsSearchController extends Controller
{
    /**
     * キーワード検索、動物カテゴリー選択検索
     * 
     * @param \App\Http\Requests\PostSearchRequest $request
     * @return \Illuminate\View\View
     */
    public function index(PostSearchRequest $request)
    {
        $query = Post::query();

        if (filled($request->animals_name) || filled($request->keyword)) {
            $query = Post::PostSearch($request->all());
            $count = $query->count();
        }
        $posts = $query->with('postImages', 'postCategorys')
                    ->orderBy('created_at', 'desc')
                    ->simplePaginate(12)
                    ->appends($request->query());
        return view('posts.search',[
                'posts' => $posts,
                'keyword' => $request->keyword ?? null,
                'animals_name' => $request->animals_name ?? null,
                'count' => $count ?? null,
            ]);
    }

    /**
     * カテゴリーリンク
     * 
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function categorys($id) {
        $query = Post::query();

        //アニマルIDで投稿を検索
        $animal = Animal::findOrFail($id);
        $query->whereHas('postCategorys', function($query) use($animal) {
                $query->whereIn('name', $animal);
            });
        $count = $query->count();
        $posts = $query->with('postImages', 'postCategorys')
                    ->orderBy('created_at', 'desc')
                    ->simplePaginate(12);
        return view('posts.search',[
                'posts' => $posts,
                'count' => $count,
            ]);
    }
}