@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center font-weight-bold mt-5 mb-5">{{ __('Register') }}</h2>
    
    <div class="row justify-content-center text-center">
        <div class="col-12 col-sm-9 col-md-7 col-lg-5 col-xl-4">
            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">

                @csrf

                @if (!empty($file))
                    <img id="avatar_preview" src="data:image/{{ $mimeType }};base64,{{ $file }}">
                @else
                    <img id="avatar_preview" src="{{ asset('storage/images/default_icon.png') }}">
                @endif
                <input type="file" name="avatar" id="avatarUpload" accept='image/*' style="display:none;">
                <button class="btn btn-outline-success btn-sm ml-3" id="avatarUploadButton">写真を選択</button>  
    
                <div class="form-group">
                    <label for="name" class="col col-form-label">{{ __('Name') }}</label>
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror text-center" name="name" value="{{ old('name') }}" required autocomplete="name" placeholder="15字以下で入力してください" autofocus>
    
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
    
                <div class="form-group">
                    <label for="email" class="col col-form-label">{{ __('E-Mail Address') }}</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror text-center" name="email" value="{{ old('email') }}" required autocomplete="email">
    
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
    
                <div class="form-group">
                    <label for="password" class="col col-form-label">{{ __('Password') }}</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror text-center" name="password" required placeholder="8字以上~15字以下で入力してください" autocomplete="new-password">
    
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
    
                <div class="form-group">
                    <label for="password-confirm" class="col col-form-label">{{ __('Confirm Password') }}</label>
                    <input id="password-confirm" type="password" class="form-control text-center" name="password_confirmation" required placeholder="もう一度同じパスワードを入力してください" autocomplete="new-password">
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
