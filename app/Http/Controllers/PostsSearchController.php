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
    public function index(PostSearchRequest $request) {
        $query = Post::query();
        $animals = $request->animals_name;
        $keyword = $request->keyword;

        if (filled($animals) || filled($keyword)) {

            //どちらにも値が入っている場合の検索
            if (filled($animals) && filled($keyword)) {
                $query->where('content', 'LIKE', '%'.$keyword.'%')
                    ->whereHas('postCategorys', function($query) use($animals) {
                        $query->whereIn('name', $animals);
                    });
            }
    
            //キーワードのみ値が入っている場合の検索
            elseif (filled($keyword) && empty($animals)) {
                $query->where('content', 'LIKE', '%'.$keyword.'%');
            }
    
            //動物カテゴリーのみ値が入っている場合の検索
            elseif (filled($animals) && empty($keyword)) {
                $query->whereHas('postCategorys', function($query) use($animals) {
                        $query->whereIn('name', $animals);
                    });
            }
            $count = $query->count();
        }
        $posts = $query->with('postImages', 'postCategorys')
                    ->orderBy('created_at', 'desc')
                    ->simplePaginate(12)
                    ->appends($request->query());
        return view('posts.search',[
                'posts' => $posts,
                'keyword' => $keyword,
                'animals_name' => $animals,
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