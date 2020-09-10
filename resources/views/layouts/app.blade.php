<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&family=Sawarabi+Gothic&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

</head>
<body>

    {{-- フラッシュメッセージ --}}
    @include('commons.flash_messages')

    {{-- ナビゲーションバー --}}
    @include('commons.navbar')

    <main>
        @yield('content')
    </main>

    {{-- ページトップへ戻るボタン --}}
    <div id="page_top_button"><a href="#"><i class="fas fa-angle-double-up"></i></a></div>
</body>
</html>
