@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center font-weight-bold mt-5 mb-5">{{ __('Login') }}</h2>
    
    <div class="row justify-content-center text-center">
        <div class="col-10 col-sm-8 col-md-6 col-lg-5 col-xl-4">
            <form method="POST" action="{{ route('login') }}">

                @csrf

                <div class="form-group">
                    <label for="email" class="col col-form-label text-center">{{ __('E-Mail Address') }}</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                 </div>

                <div class="form-group">
                    <label for="password" class="col col-form-label text-center">{{ __('Password') }}</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group mt-1">
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

                <div class="form-group mb-2">
                    @if (Route::has('password.request'))
                        <a class="btn btn-link" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>
                    @endif
                </div>
                
                <div class="form-group">
                    {!! link_to_route('register', 'ユーザー登録の方はこちらへ', [], ['class' => 'btn btn-link']) !!} 
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
