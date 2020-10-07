@extends('layouts.app')

@section('title', '投稿検索')

@section('content')

    {{-- 検索フォーム --}}
    @include('search.posts_search_form')

    {{-- 検索記事一覧 --}}
    @include('posts.posts')
@endsection