@extends('layouts.app')

@section('title', 'ログイン')

@section('content')
<div class="container">
    <h2 class="text-center font-weight-bold mt-5 mb-5">{{ __('Login') }}</h2>
    <div class="row justify-content-center">
        <div class="col-12 col-sm-9 col-md-8 col-lg-6 col-xl-5">

            <!-- 通常ログイン -->
            <form method="POST" action="{{ route('login') }}" accept-charset="UTF-8">
                @csrf
                <div class="form-group">
                    <label for="email" class="col col-form-label">{{ __('E-Mail Address') }}</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                 </div>
                <div class="form-group">
                    <label for="password" class="col col-form-label">{{ __('Password') }}</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group mt-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>
                </div>
                <div class="form-group mt-3 mb-4">
                    <button type="submit" class="btn btn-outline-success">
                        {{ __('Login') }}
                    </button>
                </div>
            </form>

            <!-- ゲストユーザーログイン -->
            <form method="POST" action="{{ route('login') }}" accept-charset="UTF-8">
                @csrf
                <input type="hidden" name="email" value="guest@example.com">
                <input type="hidden" name="password" value="guest123456789">
                <div class="form-group mb-3">
                    <button type="submit" class="btn btn-warning">
                        かんたんログイン
                    </button>
                </div>
            </form>

            <!-- パスワード再設定・ユーザー登録ページリンク -->
            <div class="mb-2">
                @if (Route::has('password.request'))
                    <a class="btn btn-link" href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>
                @endif
            </div>
            <div>
                {!! link_to_route('register', 'ユーザー登録の方はこちらへ', [], ['class' => 'btn btn-link']) !!}
            </div>
        </div>
    </div>
</div>
@endsection
