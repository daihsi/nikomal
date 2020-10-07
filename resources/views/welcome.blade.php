@extends('layouts.app')

@guest
    @section('title', 'トップページ')
@else
    @section('title', '新規投稿一覧')
@endguest

@section('content')
@guest
    @include('commons.guest_toppage')
@endguest

    @if(Auth::check())
        {{-- 投稿切り替えボタン --}}
        @include('commons.posts_sort_button')
    @endif

    {{-- 投稿一覧(新着投稿順) --}}
    @include('posts.posts')

@endsection