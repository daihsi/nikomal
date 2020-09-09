<ul class="nav nav-tabs justify-content-center mb-3">
    {{-- 投稿コメントタブ --}}
    <li class="nav-item">
        <a href="{{ route('posts.show', ['post' => $post->id]) }}" class="nav-link {{ Request::routeIs('posts.show') ? 'active' : '' }} text-dark">
            コメント
            <span class="badge badge-white badge-pill">{{ $post->postComments->count() }}</span>
        </a>
    </li>
    {{-- 投稿いいねユーザータブ --}}
    <li class="nav-item">
        <a href="{{ route('post.likes', ['id' => $post->id]) }}" class="nav-link {{ Request::routeIs('post.likes') ? 'active' : '' }} text-dark">
            いいね
            <span class="badge badge-white badge-pill p_count_badge">{{ $post->likes->count() }}</span>
        </a>
    </li>
</ul>