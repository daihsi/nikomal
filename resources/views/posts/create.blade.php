@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center mt-3 mb-5">新規投稿</h1>
    <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" accept-charset="UTF-8">
        <div class="row justify-content-center">
            @csrf
            <div class="col-lg-7 col-md-8 col-sm-9 col-10 text-center">
                @if(!empty($file))
                    <img id="post_image_preview" src="data:image/{{ $mimeType }};base64,{{ $file }}">
                @else
                    <img id="post_image_preview"  class="img-fluid default_post_image" src="{{ asset('storage/images/default_post_image-69f9f14784d10ec4rer544rf661e2fc1cb46929c56bd8e6.png') }}" alt="投稿画像未選択">
                @endif
                    <input type="file" name="image" id="post_upload" class="@error('image') is-invalid @enderror" accept='image/jpeg,image/png,image/jpg' style="display:none;">
                    <p class="post_image_Supplement d-none d-sm-block ml-1 mt-2"><span class="badge badge-danger mr-1">必須</span>※2MBまでの画像をアップロードできます</p>
                    <p class="post_image_Supplement d-sm-none d-block ml-1 mt-2"><span class="badge badge-danger mr-1">必須</span>※2MBまでの画像選択可能</p> 
                @error('image')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="d-md-flex flex-md-column col-lg-5 col-md-8 col-sm-10 col-11 pt-3">
                <div class="form-group">
                    <label for="content" class="col col-form-label"><span class="badge badge-danger mr-1">必須</span>キャプショ</label>
                    <textarea id="content" name="content" value="{{ old('content') }}" class="form-control post_content @error('content') is-invalid @enderror" autocomplete="content" placeholder="150字以下で入力してください" rows=6 cols=30 wrap="hard" maxlength="150" autofocus required>{{ old('content') }}</textarea>
                    @error('content')
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="animals_name" class="col col-form-label"><span class="badge badge-danger mr-1">必須</span>動物カテゴリー</label>
                    <select name="animals_name[]" id="animals_select" class="form-control @error('animals_name') is-invalid @enderror" size="5" autofocus multiple required>
                        @foreach(config('animals.animals_optgroup') as $number => $attribute)
                            <optgroup label="{{ $attribute }}">
                                @foreach(config('animals.animals'. $number) as $index => $name)
                                    <option value="{{ $index }}"
                                    {{ collect(old('animals_name'))->contains($index) ? 'selected' : '' }}
                                    >{{ $name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>

                    @error('animals_name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror

                </div>
                <div class="form-group text-center mb-3">
                    <button type="submit" class="btn btn-outline-success mt-3">投稿する</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection