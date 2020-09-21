@extends('layouts.app')

@section('content')

<div class="container">
    <form method="POST" action="{{ route('posts.update', $post->id) }}" enctype="multipart/form-data" accept-charset="UTF-8">
        <div class="row justify-content-center mt-5">
            @method('PUT')
            @csrf
            <div class="col-lg-7 col-md-8 col-sm-9 col-10 text-center">
                @foreach ($post_images as $post_image)
                    @if (!empty($file))
                        <img id="edit_post_image_preview" src="data:image/{{ $mimeType }};base64,{{ $file }}" class="post_image_edit">
                    @elseif($post_image->image)
                        <img id="edit_post_image_preview" src="{{ $post_image->image }}" class="post_image_edit" alt="{{ implode('・', $animals_name). 'の笑顔' }}">
                    @endif
                        <input type="file" name="image" id="post_upload" class="@error('image') is-invalid @enderror" accept='image/jpeg,image/png,image/jpg' style="display:none;">
                        <p class="post_image_supplement d-none d-sm-inline-block ml-1 mt-2"><span class="badge badge-danger mr-1">必須</span>※2MBまでの画像をアップロードできます</p>
                        <p class="post_image_supplement d-sm-none ml-1 mt-2"><span class="badge badge-danger mr-1">必須</span>※2MBまでの画像選択可能</p> 
                    @error('image')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                @endforeach
            </div>
            <div class="d-md-flex flex-md-column col-lg-5 col-md-8 col-sm-10 col-11 pt-3">
                <div class="form-group">
                    <label for="content" class="col col-form-label"><span class="badge badge-danger mr-1">必須</span>キャプショ</label>
                    <textarea id="content" name="content" class="form-control @error('content') is-invalid @enderror" autocomplete="content" placeholder="150字以下で入力してください" rows=8 maxlength="150" autofocus required>{{ old('content', $post->content) }}</textarea>
                    @error('content')
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="animals_name" class="col col-form-label"><span class="badge badge-danger mr-1">必須</span>動物カテゴリー</label>
                    <div class="@error('animals_name') is-invalid @enderror">
                        <select name="animals_name[]" id="animals_select" class="form-control" size="5" autofocus multiple required>
                            @foreach(config('animals.animals_optgroup') as $number => $attribute)
                                <optgroup label="{{ $attribute }}">
                                    @foreach(config('animals.animals'. $number) as $index => $name)
                                            <option value="{{ $index }}"
                                            {{ collect(old('animals_name', $animals_name))->contains($index) ? 'selected' : '' }}
                                            >{{ $name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    @error('animals_name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group text-center mb-3">
                    <button type="submit" class="btn btn-outline-success mt-3">変更内容を保存する</button>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection