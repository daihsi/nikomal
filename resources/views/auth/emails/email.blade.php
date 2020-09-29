@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="text-center font-weight-bold mt-5 mb-5">{{ __('Reset Email') }}</h4>
    <div class="row justify-content-center">
        <div class="col-12 col-sm-9 col-md-8 col-lg-6 col-xl-5">
            <form method="POST" action="{{ route('email.email') }}" accept-charset="UTF-8">
                @csrf
                <input name="guest_login_email" type="hidden" value="guest@example.com">
                <div class="form-group">
                    <label for="new_email" class="col col-form-label">{{ __('E-Mail Address') }}</label>
                    <input id="new_email" type="email" class="form-control @error('new_email') is-invalid @enderror" name="new_email" value="{{ old('new_email') }}" placeholder="新しいメールアドレスを入力してください" required autocomplete="new_email" autofocus>
                    @error('new_email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-outline-success">
                        {{ __('Send Email Reset Link') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection