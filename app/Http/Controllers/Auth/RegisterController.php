<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Providers\RouteServiceProvider;
use App\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */
    use RegistersUsers;
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    

    //RegisterRequest使用のため
    public function register(RegisterRequest $request)
    {
        
        event(new Registered($user = $this->create($request->validated())));
        $this->guard()->login($user);
        return $this->registered($request, $user)
                        ?: redirect($this->redirectPath());
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
     
    
    protected function create(array $data)
    {
        if (isset($data['avatar'])) {
            
            $file = $data['avatar'];
            
            //ファイル名のタイムスタンプ
            $now = date_format(Carbon::now(), 'YmdHis');
            //アップロードされたファイル名取得
            $name = $file->getClientOriginalName();
            //s3保存先のパス生成
            $storePath="/nikomal".$now."_".$name;
            //画像を横幅300px,縦幅アスペクト比維持の自動サイズへリサイズ
            $image = Image::make($file)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            //s3へのアップロードと保存
            $data['avatar'] = Storage::disk('s3')->put($storePath, (string) $image->encode(), 'public');
        };

       return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'avatar' => $data['avatar'] ?? null,
        ]);
    }
}