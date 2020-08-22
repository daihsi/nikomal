@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-9 col-md-8 col-lg-6 col-xl-5">
                <form method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data" accept-charset="UTF-8">
                    @method('PUT')
                    @csrf
                    <div class="text-center">
                        @if (!empty($file))
                            <img id="avatar_preview" src="data:image/{{ $mimeType }};base64,{{ $file }}" class="rounded-circle @error('avatar') is-invalid @enderror text-center" width="230" height="230">
                        @elseif ($user->avatar)
                            <img id="avatar_preview" src="{{ $user->avatar }}" class="rounded-circle @error('avatar') is-invalid @enderror text-center" width="230" height="230"></img>
                        @else
                            <img id="avatar_preview" src="{{ asset('storage/images/default_icon.png') }}" class="rounded-circle" width="230" height="230">
                        @endif
                            <input type="file" name="avatar" id="avatarUpload" class="" accept='image/jpeg,image/png,image/jpg' style="display:none;">
                            <button class="btn btn-outline-success ml-3 d-none d-md-inline btn-sm" id="avatarUploadButton">写真を選択</button>
                            <div><button class="btn btn-outline-success d-md-none mt-2 btn-sm" id="avatarUploadButtonBottom">写真を選択</button></div>
                            @error('avatar')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                    </div>

                    <div class="form-group mt-3">
                        <label for="name" class="col col-form-label">{{ __('Name') }}</label>
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $user->name }}" required autocomplete="name" placeholder="15字以下で入力してください" autofocus>

                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group mt-3">
                        <label for="self_introduction" class="col col-form-label">{{ __('Self_Introduction') }}</label>
                        <textarea id="self_introduction" class="form-control @error('self_introduction') is-invalid @enderror" name="self_introduction" placeholder="150字以下で入力してください" rows="5" maxlength="150">{{ $user->self_introduction }}</textarea>

                        @error('self_introduction')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <button type="submit" class="btn btn-outline-success mt-3">変更内容を保存する</button>
                    </div>
                
                    </div>

                </form>
            </div>
        </div>
    </div>


@endsection