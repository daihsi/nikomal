@extends('layouts.app')

@section('content')

{{-- ユーザー情報 --}}
@include('users.information')

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-12">
            {{-- タブ一覧 --}}
            @include('commons.navtabs_user_show')
        </div>
    </div>
</div>
{{-- いいね投稿一覧 --}}
@include('posts.posts')
@if(count($posts) == 0)
    <div class="d-flex justify-content-center align-items-center" style="height:200px; color:rgba(0,0,0,0.4);">
        まだいいねした投稿がありません
    </div>
@endif

@endsection