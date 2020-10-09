@extends('layouts.app')

@section('title', 'ユーザー一覧')

@section('content')

    {{-- ユーザー一覧ページ --}}
    @include('users.users')

@endsection