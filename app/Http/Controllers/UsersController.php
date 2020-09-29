<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id', 'desc')->simplePaginate(12);
        
        return view('users.index', [
            'users' => $users,
        ]);
    }
    
    public function show($id)
    {
        $user = User::findOrFail($id);
        $user->loadRelationshipCounts();
        $posts = $user->posts()->orderBy('created_at', 'desc')->simplePaginate(6);

        return view('users.show', [
            'user' => $user,
            'posts' => $posts,
        ]);
    }
    
    public function edit($id)
    {
        $user = User::findOrFail($id);
        
        if (\Auth::id() === $user->id) {
            return view ('users.edit', ['user' => $user,]);
        }
        else {
            return back()->with('msg_error', '編集ページにアクセスできません');
        }
    }
    
    public function update(UserUpdateRequest $request, $id)
    {

        $user = User::findOrFail($id);

        if (!empty($request->file('avatar'))) {
            //既存ファイルを消去
            if(!empty($user->avatar)) {
                Storage::disk('s3')->delete('users_avatar/'.basename($user->avatar));
            }
            $file = $request->file('avatar');
            //アップロードされたファイル名取得
            $name = $file->getClientOriginalName();
    
            //画像を横幅300px,縦幅アスペクト比維持の自動サイズへリサイズ
            $image = Image::make($file)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                });;
            //s3へのアップロードと保存
            $path = Storage::disk('s3')->put('/users_avatar/'.$name, (string) $image->encode(), 'public');
            //データペースへ保存;
            $url = Storage::disk('s3')->url('users_avatar/'.$name);
        }
        elseif (empty($request->file('image')) && !empty($user->avatar)) {
            $url = $user->avatar;
        }
        
        $user->fill([
            'name' => $request->name,
            'avatar' => $url ?? null,
            'self_introduction' => $request->self_introduction,
        ])->save();

        return redirect()
            ->route('users.show', ['user' => $user])
            ->with('msg_success', '変更を保存しました');
    }

    public function destroy($id)
    {
        $user = User::findorFail($id);
        $posts = $user->posts()->with('postImages')->get();

        //投稿が空ではない場合この処理に入る
        //s3に保存してあるファイルを削除するため
        //オブジェクトが空でないかを判定する条件式
        if (!$posts->isEmpty()) {
            foreach ($posts as $post) {
                foreach ($post->postImages as $post_image) {
                    if(!empty($post_image->image)) {
                        Storage::disk('s3')
                            ->delete('post_images/'.basename($post_image->image));
                    }
                }
            }
        }

        //ユーザーのアバター画像が保存してあれば
        //s3内のファイルを削除
        if (!empty($user->avatar)) {
            Storage::disk('s3')
                ->delete('users_avatar/'.basename($user->avatar));
        }

        //ユーザー削除処理
        $user->delete();

        //リクエストしたページでリダイレクト先を変える
        //条件式は、リクエストURLの前のURLの/users以下が
        //空文字でなければというもの
        //ユーザー一覧(/users),ユーザー詳細(/users/*)であるから
        if (Str::after(url()->previous(), '/users') == "") {
            return back()->with('msg_success', '「'.$user->name.'」のアカウントを削除しました');
        }
        else {
            return redirect('/')->with('msg_success', '「'.$user->name.'」のアカウントを削除しました');
        }
    }

    public function followings($id)
    {
        $user = User::findOrFail($id);
        $user->loadRelationshipCounts();
        $followings = $user->followings()->withPivot('created_at AS joined_at')->orderBy('joined_at', 'desc')->simplePaginate(12);

        return view('users.followings', [
            'user' => $user,
            'users' => $followings
        ]);
    }

    public function followers($id)
    {
        $user = User::findOrFail($id);
        $user->loadRelationshipCounts();
        $followers = $user->followers()->withPivot('created_at AS joined_at')->orderBy('joined_at', 'desc')->simplePaginate(12);

        return view('users.followers', [
            'user' => $user,
            'users' => $followers,
        ]);
    }

    public function likes($id)
    {
        $user = User::findOrFail($id);
        $user->loadRelationshipCounts();
        $likes = $user->likes()->withPivot('created_at AS joined_at')->orderBy('joined_at', 'desc')->simplePaginate(6);

        return view('users.likes', [
            'user' => $user,
            'posts' => $likes,
        ]);
    }
}
