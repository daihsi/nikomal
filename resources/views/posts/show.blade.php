@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xl-5 col-lg-6 col-md-8 col-sm-10 col-12 offset-lg-0 offset-md-2 offset-sm-1 mt-5">
            <div class="card shadow p-2 mb-5 bg-white rounded show_post_card">
                @foreach($post_images as $post_image)
                    <img src="{{ $post_image->image }}" class="card-img-top" style="width:100%; max-height: 700px;" alt="">
                @endforeach
                <div class="card-body">
                    <div class="d-flex justify-content-end">
                        @empty($post->user->avatar)
                            <a href="{{ route('users.show', $post->user->id) }}" class="text-dark mr-auto font-weight-bold"><div class="card-title"><img src="{{ asset('storage/images/default_icon.png') }}" class="rounded-circle" width="40" height="40">{{ $post->user->name }}</div></a>
                        @else
                            <a href="{{ route('users.show', $post->user->id) }}" class="text-dark mr-auto font-weight-bold"><div class="card-title"><img src="{{ $post->user->avatar }}" class="rounded-circle mr-2" width="40" height="40">{{ $post->user->name }}</div></a>
                        @endempty
                    </div>
                    <p class="card-text">{!! nl2br(e($post->content)) !!}</p>
                    @foreach($post->postCategorys as $post_category)
                        <span class="badge badge-success align-self-center"><i class="fas fa-hashtag">{{ $post_category->name }}</i></span>
                    @endforeach
                    <div class="d-flex justify-content-end mt-3">
                        @if(Auth::id() === $post->user_id)
                            <a href="{{ route('posts.edit', $post->id) }}" class="d-block justify-content-start"><button class="btn btn-outline-success rounded-pill fas fa-edit mt-1">編集</button></a>
                            <form method="POST" action="{{ route('posts.destroy', $post->id) }}" class="mr-auto">
                                @method('DELETE')
                                @csrf
                                <button type="submit" class="btn btn-danger rounded-pill fas fa-trash-alt mt-1 ml-1">削除</button>
                            </form>
                        @endif
                        @include('likes.like_button')
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-7 col-lg-6 col-12 mt-5">
            {{-- タブ一覧 --}}
            @include('commons.navtabs_post_show')
        </div>
    </div>
</div>
@endsection