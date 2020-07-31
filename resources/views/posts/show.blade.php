@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-5 mt-5">
            <div class="card shadow p-2 mb-5 bg-white rounded">
                @foreach($post_images as $post_image)
                    <img src="{{ $post_image->image }}" class="card-img-top" style="width:100%;" alt="">
                @endforeach
                <div class="card-body">
                    <div class="d-flex justify-content-end">
                        @empty($post->user->avatar)
                            <a href="{{ route('users.show', $post->user->id) }}" class="text-dark mr-auto"><h5 class="card-title"><img src="{{ asset('storage/images/default_icon.png') }}" class="rounded-circle" width="40" height="40">{{ $post->user->name }}</h5></a>
                        @else
                            <a href="{{ route('users.show', $post->user->id) }}" class="text-dark mr-auto"><h5 class="card-title font-weight-bold"><img src="{{ $post->user->avatar }}" class="rounded-circle mr-2" width="40" height="40">{{ $post->user->name }}</h5></a>
                        @endempty
                        @if(Auth::id() === $post->user_id)
                            <a href="{{ route('posts.edit', $post->id) }}"><button class="btn btn-outline-success rounded-pill fas fa-edit mt-1">編集</button></a>
                            <form method="POST" action="{{ route('posts.destroy', $post->id) }}">
                                @method('DELETE')
                                @csrf
                                <button type="submit" class="btn btn-danger rounded-pill fas fa-trash-alt mt-1 ml-1">削除</button>
                            </form>
                        @endif
                    </div>
                    <p class="card-text">{!! nl2br(e($post->content)) !!}</p>
                    @foreach($post->postCategorys as $post_category)
                        <span class="badge badge-success"><i class="fas fa-hashtag">{{ $post_category->name }}</i></span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection