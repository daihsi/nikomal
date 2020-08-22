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
use Intervention\Image\Facades\Image;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::with('postImages', 'postCategorys')->orderBy('created_at', 'desc')->simplePaginate(10);
        return view('welcome', [
            'posts' => $posts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\PostRequest  $request
     * @return \Illuminate\Http\Response
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
                $animal_id[] = $animal->id;
            }
        }
        $post->belongsToCategory($animal_id);

        //画像データの保存
        foreach ($post->postImages() as $post_image) {
            $file = $request->file('image');
            $name = $file->getClientOriginalName();
            $image = Image::make($file)
                ->resize(400, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            $path = Storage::disk('s3')->put('/post_images/'.$name, (string) $image->encode(), 'public');
            $url = Storage::disk('s3')->url('post_images/'.$name);
    
            $post_image->create([
                'image' => $url,
            ]);
        }

        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::findOrFail($id);
        $post_images = $post->postImages;
        $post_categorys = $post->postCategorys;
        $post->loadRelationshipCounts();
        $like_users = $post->likeUsers()->withPivot('created_at AS joined_at')->orderBy('joined_at', 'desc')->simplePaginate(12);
        $comments = $post->postComments()->orderBy('created_at', 'desc')->simplePaginate(12);

        return view('posts.show',[
            'post' => $post,
            'post_images' => $post_images,
            'post_categorys' => $post_categorys,
            'users' => $like_users,
            'comments' => $comments,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::findOrFail($id);

        if (\Auth::id() === $post->user_id) {
            
            $post_images = $post->postImages;
            $post_categorys = $post->postCategorys;
            foreach ($post_categorys as $post_category) {
                $animals_name[] = $post_category->name;
            }

            return view('posts.edit', [
                'post' => $post,
                'animals_name' => $animals_name,
                'post_images' => $post_images,
            ]);
        }
        else {
            return back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\PostRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
            }
        }
        $test = $post->postCategorys()->sync($animal_id);
        
        //投稿画像の更新及びs3ストレージ内にある古いファイルの削除
        foreach ($post->postImages as $post_image) {
            if (!empty($request->file('image'))) {
                if (!empty($post_image->image)) {
                    $test = Storage::disk('s3')->delete('post_images/'.basename($post_image->image));
                }
                $file = $request->image;
                $name = $file->getClientOriginalName();
                $image = Image::make($file)
                    ->resize(400, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                $path = Storage::disk('s3')->put('/post_images/'.$name, (string) $image->encode(), 'public');
                $url = Storage::disk('s3')->url('post_images/'.$name);
            }
            $post_image->fill([
                'image' => $url ?? $post_image->image,
            ])->save();
        }

        //キャプショの更新
        $post->fill([
            'content' => $request->content,
        ])->save();
        
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        
        if (\Auth::id() === $post->user_id) {
            foreach ($post->postImages as $post_image) {
                if (!empty($post_image->image)) {
                    //既存ファイルの削除
                    Storage::disk('s3')->delete('post_images/'.basename($post_image->image));
                }
            }
            $post->delete();
            return redirect('/');
        }
        else {
            return back();
        }
    }
}
