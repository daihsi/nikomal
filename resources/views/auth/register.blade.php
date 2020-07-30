@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center font-weight-bold mt-5 mb-5">{{ __('Register') }}</h2>
    
    <div class="row justify-content-center">
        <div class="col-12 col-sm-9 col-md-8 col-lg-6 col-xl-5">
            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" accept-charset="UTF-8">

                @csrf
                <div class="text-center">
                @if (!empty($file))
                    <img id="avatar_preview" src="data:image/{{ $mimeType }};base64,{{ $file }}" class="rounded-circle @error('avatar') is-invalid @enderror text-center" width="230" height="230">
                @else
                    <img id="avatar_preview" src="{{ asset('storage/images/default_icon.png') }}" class="rounded-circle @error('avatar') is-invalid @enderror text-center" width="230" height="230">
                @endif
                    <input type="file" name="avatar" id="avatarUpload" accept='image/jpeg,image/png,image/jpg' style="display:none;">
                    <button class="btn btn-outline-success d-none d-md-inline ml-3" id="avatarUploadButton">写真を選択</button>  
                    <div><button class="btn btn-outline-success d-md-none mt-2" id="avatarUploadButtonBottom">写真を選択</button></div>
                    @error('avatar')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group mt-3">
                    <label for="name" class="col col-form-label">{{ __('Name') }}</label>
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" placeholder="15字以下で入力してください" autofocus>

                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
    
                <div class="form-group mt-3">
                    <label for="email" class="col col-form-label">{{ __('E-Mail Address') }}</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
    
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
    
                <div class="form-group mt-3">
                    <label for="password" class="col col-form-label">{{ __('Password') }}</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="8字以上~15字以下で入力してください" autocomplete="new-password">
    
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
    
                <div class="form-group mt-3">
                    <label for="password-confirm" class="col col-form-label">{{ __('Confirm Password') }}</label>
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required placeholder="もう一度同じパスワードを入力してください" autocomplete="new-password">
                </div>
    
                <div class="form-group mb-3">
                    <button type="submit" class="btn btn-outline-success mt-3">登録する</button>
                </div>
                
                <div class="form-group">
                    {!! link_to_route('login', 'ログインの方はこちらへ', [], ['class' => 'btn btn-link']) !!}
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
