@extends('layouts.app')

@section('content')

    {{-- 検索フォーム --}}
    @include('search.posts_search_form')

    {{-- 検索記事一覧 --}}
    @include('posts.posts')
@endsection