@extends('layouts.app')

@section('title', 'パスワード再設定')

@section('content')
<div class="container">
    <h3 class="text-center font-weight-bold mt-5 mb-5">{{ __('Reset Password') }}</h3>
    <div class="row justify-content-center">
        <div class="col-12 col-sm-9 col-md-8 col-lg-6 col-xl-5">
            <form method="POST" action="{{ route('password.email') }}" accept-charset="UTF-8">
                @csrf
                <input name="guest_login_email" type="hidden" value="guest@example.com">
                <div class="form-group">
                    <label for="email" class="col col-form-label">{{ __('E-Mail Address') }}</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="メールアドレスを入力してください" required autocomplete="email" autofocus>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-outline-success">
                        {{ __('Send Password Reset Link') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
