<?php

namespace App\Http\Controllers;

use App\User;
use App\Post;
use App\PostImage;
use App\Animal;
use App\Comment;
use App\Http\Requests\PostRequest;
use App\Http\Requests\PostEditRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{
    /**
     * トップページ投稿一覧
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $posts = Post::with('postImages', 'postCategorys')->orderBy('created_at', 'desc')->simplePaginate(12);
        return view('welcome', [
            'posts' => $posts,
        ]);
    }

    /**
     * 新規投稿用フォームへ
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        if (\Auth::check()) {
            return view('posts.create');
        }
        else {
            return redirect('login');
        }
    }

    /**
     * 新規投稿リクエスト
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PostRequest $request)
    {
        //キャプショのデータ保存
        $user = auth()->user();
        $post = $user->posts()->create([
                    'content' => $request->content,
                ]);

        //カテゴリーの保存
        foreach ($request->animals_name as $animal_name) {
            if (!empty($animal_name)) {
                $animal = Animal::firstOrCreate([
                    'name' => $animal_name
                ]);
                $animal_id = $animal->id;
                $post->belongsToCategory($animal_id);
            }
        }

        $post->postImages()->create([
            'image' => $request->imageUrl(),
        ]);

        return redirect('/')->with('msg_success', '新規投稿しました');
    }

    /**
     * 投稿詳細ページへ
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $post = Post::findOrFail($id);
        $post_images = $post->postImages;
        $post_categorys = $post->postCategorys;
        $post->loadRelationshipCounts();
        $comments = $post->postComments()->orderBy('created_at', 'desc')->simplePaginate(11);

        return view('posts.show',[
            'post' => $post,
            'post_images' => $post_images,
            'post_categorys' => $post_categorys,
            'comments' => $comments,
        ]);
    }

    /**
     * 投稿編集フォームへ
     *
     * @param  int  $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        $post = Post::findOrFail($id);

        if (\Auth::id() === $post->user_id) {
            
            $post_images = $post->postImages;

            //モデルを配列に変換し、nameのみ取得
            //nameの配列を生成し、viewに渡す
            $post_categorys = $post->postCategorys->toArray();
            $animals_name = array_column($post_categorys, 'name');

            return view('posts.edit', [
                'post' => $post,
                'animals_name' => $animals_name,
                'post_images' => $post_images,
            ]);
        }
        else {
            return back()->with('msg_error', '編集ページにアクセスできません');
        }
    }

    /**
     * 投稿更新リクエスト
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PostEditRequest $request, $id)
    {
        $post = Post::findOrFail($id);
        
        //カテゴリーの更新及び動物名の新規保存
        foreach ($request->animals_name as $animal_name) {
            if (!empty($animal_name)) {
                $animal = Animal::firstOrCreate([
                    'name' => $animal_name,
                ]);
                $animal_id[] = $animal->id;
                $post->postCategorys()->sync($animal_id);
            }
        }

        //投稿画像の更新及びs3ストレージ内にある古いファイルの削除
        if (!empty($request->imageUrl())) {
            foreach ($post->postImages as $post_image) {
                if (!empty($post_image->image)) {
                    Storage::disk('s3')->delete('post_images/'.basename($post_image->image));
                }

                //画像をデータベースに保存
                $post_image->fill([
                    'image' => $request->imageUrl(),
                ])->save();
            }
        }

        //キャプショの更新
        $post->fill([
            'content' => $request->content,
        ])->save();
        
        return redirect('/')->with('msg_success', '変更を保存しました');
    }

    /**
     * 投稿削除リクエスト
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        //投稿所有者と管理ユーザーは投稿削除可能
        //投稿画像collectionが空でない場合は
        //s3内のファイルも削除
        if (\Auth::id() === $post->user_id || \Gate::allows('admin')) {
            if (!$post->postImages()->get()->isEmpty()) {
                foreach ($post->postImages as $post_image) {

                    //s3内の既存ファイルの削除
                    Storage::disk('s3')->delete('post_images/'.basename($post_image->image));
                }
            }
            $post->delete();
            return redirect('/')->with('msg_success', '投稿削除しました');
        }
        else {
            return back()->with('msg_error', '投稿削除できません');
        }
    }

    /**
     * 指定投稿にいいねしたユーザー一覧ページへ
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function likes($id)
    {
        $post = Post::findOrFail($id);
        $post_images = $post->postImages;
        $post_categorys = $post->postCategorys;
        $post->loadRelationshipCounts();
        $likes = $post->likes()->withPivot('created_at AS joined_at')->orderBy('joined_at', 'desc')->simplePaginate(12);

        return view('posts.likes', [
            'post' => $post,
            'post_images' => $post_images,
            'post_categorys' => $post_categorys,
            'users' => $likes,
        ]);
    }
}
