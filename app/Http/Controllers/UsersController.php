<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

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
        
        return view('users.show', [
            'user' => $user,
        ]);
    }
    
    public function edit($id)
    {
        $user = User::findOrFail($id);
        
        if (\Auth::id() === $user->id) {
            return view ('users.edit', ['user' => $user,]);
        }
        else {
            return back();
        }
    }
    
    public function update(UserUpdateRequest $request, $id)
    {
        
        $user = User::findOrFail($id);
        $avatar = $request->file('avatar');
        
        if ($avatar) {
             
            //既存ファイルを消去
            if ($user->avatar) {
                Storage::disk('s3')->delete('users_avatar/'.basename($user->avatar));
            }
             
            $file = $avatar;
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
        
        if (empty($avatar) && $user->avatar) {
            $url = Storage::disk('s3')->url('users_avatar/'.basename($user->avatar));
        }

        $user->fill([
            'name' => $request->name,
            'avatar' => $url ?? null,
            'self_introduction' => $request->self_introduction,
        ])->save();

        return redirect()->route('users.show', ['user' => $user]);

    }
}
