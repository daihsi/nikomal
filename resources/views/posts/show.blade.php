@extends('layouts.app')

@section('title', '投稿詳細')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xl-5 col-lg-6 col-md-8 col-sm-10 col-12 offset-lg-0 offset-md-2 offset-sm-1 mt-5">
            {{-- 投稿　--}}
            @include('posts.information')
        </div>
        <div class="col-xl-7 col-lg-6 col-12 mt-5">
            {{-- タブ一覧 --}}
            @include('commons.navtabs_post_show')
            {{-- コメントフォーム --}}
            @if (Auth::check())
                @include('comments.comment_form')
            @endif
            {{-- コメント一覧 --}}
            @include('posts.comments')
        </div>
    </div>
</div>
@endsection