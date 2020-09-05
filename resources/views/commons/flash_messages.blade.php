@if (session('msg_success'))
    <div class="msg_success"></div>
    <script>
        var msg_success = '{{ session("msg_success") }}';
    </script>
@elseif (session('msg_error'))
    <div class="msg_error"></div>
    <script>
        var msg_error = '{{ session("msg_error") }}';
    </script>
@elseif (Request::is('posts/search', 'categorys/*'))
    @if ($count > 0)
        <div class="msg_success"></div>
        <script>
            var msg_success = '{{ $count."件ヒットしました" }}';
        </script>
    @elseif ($count === 0)
        <div class="msg_warning"></div>
        <script>
            var msg_warning = '該当する投稿がありません';
        </script>
    @endif
@endif
