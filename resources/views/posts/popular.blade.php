@extends('layouts.app')

@section('title', 'いいねが多い順投稿一覧')

@section('content')

    @if(Auth::check())
        {{-- 投稿切り替えボタン --}}
        @include('commons.posts_sort_button')
    @endif

    {{-- 投稿一覧(いいねが多い順) --}}
    @include('posts.posts')

@endsection