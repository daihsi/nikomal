@extends('layouts.app')

@section('title', 'フォロー一覧')

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
{{-- フォロー一覧 --}}
@include('users.users')
@if(count($users) == 0)
    <div class="d-flex justify-content-center align-items-center" style="height:200px; color:rgba(0,0,0,0.4);">
        まだフォローがいません
    </div>
@endif

@endsection
